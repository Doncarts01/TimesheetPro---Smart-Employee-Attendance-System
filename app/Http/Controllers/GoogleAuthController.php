<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Laravel\Socialite\Facades\Socialite;
use App\Models\Employees;
use App\Models\clockings;
use App\Models\allowed_networks;
use Carbon\Carbon;

class GoogleAuthController extends Controller
{
    /**
     * Redirect to Google OAuth - This is where the QR code points to
     */
    public function redirectToGoogle(Request $request)
    {
        // First, validate the IP address
        if (!$this->isValidNetwork($request->ip())) {
            return view('clocking.error', [
                'message' => 'Access denied. You must be connected to the office network to clock in/out.',
                'ip_address' => $request->ip()
            ]);
        }

        // Store the IP address in session for use after OAuth callback
        session(['clocking_ip' => $request->ip()]);

        // Redirect to Google OAuth with specific scopes
        return Socialite::driver('google')
            ->scopes(['email', 'profile'])
            ->redirect();
    }

    /**
     * Handle Google OAuth callback and perform clocking
     */
    public function handleGoogleCallback(Request $request)
    {
        try {
            // Get user from Google
            $googleUser = Socialite::driver('google')->user();

            // Retrieve the IP address from session
            $ipAddress = session('clocking_ip', $request->ip());

            // Validate the IP again (security measure)
            if (!$this->isValidNetwork($ipAddress)) {
                return view('clocking.error', [
                    'message' => 'Access denied. Invalid network detected.',
                    'ip_address' => $ipAddress
                ]);
            }

            // Find employee by email
            $employee = Employees::where('email', $googleUser->getEmail())->first();

            if (!$employee) {
                return view('clocking.error', [
                    'message' => 'Employee not found. Please contact your administrator.',
                    'email' => $googleUser->getEmail()
                ]);
            }

            // Perform the clocking logic
            $result = $this->performClocking($employee, $ipAddress);

            return view('clocking.success', $result);
        } catch (\Exception $e) {
            return view('clocking.error', [
                'message' => 'Authentication failed. Please try again.',
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Validate if the request is coming from an allowed network
     */
    private function isValidNetwork($ipAddress)
    {
        $allowedNetworks = allowed_networks::where('is_active', true)->get();

        foreach ($allowedNetworks as $network) {
            if ($this->ipInRange($ipAddress, $network->subnet)) {
                return true;
            }
        }

        return false;
    }

    private function ipInRange($ip, $range)
    {
        list($subnet, $bits) = explode('/', $range);
        $ip = ip2long($ip);
        $subnet = ip2long($subnet);
        $mask = -1 << (32 - $bits);
        $subnet &= $mask; // align subnet
        return ($ip & $mask) == $subnet;
    }

    /**
     * Perform the actual clocking logic
     */
    private function performClocking(Employees $employee, $ipAddress)
    {
        $today = Carbon::today();

        // Check if employee has an open clocking for today
        $openClocking = clockings::where('employee_id', $employee->id)
            ->whereDate('clock_in_time', $today)
            ->whereNull('clock_out_time')
            ->first();

        if ($openClocking) {
            // Clock out
            $openClocking->update([
                'clock_out_time' => Carbon::now(),
                'clock_out_ip'   => $ipAddress
            ]);

            $workDuration = Carbon::parse($openClocking->clock_in_time)
                ->diffForHumans(Carbon::now(), true);

            return [
                'action'         => 'clock_out',
                'employee'       => $employee,
                'clock_in_time'  => $openClocking->clock_in_time,
                'clock_out_time' => Carbon::now(),
                'work_duration'  => $workDuration,
                'message'        => 'Successfully clocked out!'
            ];
        }

        // ✅ NEW CHECK: Has already clocked in *and out* today?
        $completedClocking = clockings::where('employee_id', $employee->id)
            ->whereDate('clock_in_time', $today)
            ->whereNotNull('clock_out_time')
            ->latest('clock_out_time')
            ->first();

        if ($completedClocking) {
            $workDuration = Carbon::parse($completedClocking->clock_in_time)
                ->diffForHumans(Carbon::parse($completedClocking->clock_out_time), true);

            return [
                'action'         => 'blocked',
                'employee'       => $employee,
                'clock_in_time'  => $completedClocking->clock_in_time,
                'clock_out_time' => $completedClocking->clock_out_time,
                'work_duration'  => $workDuration,
                'message'        => 'You have already clocked in and out today. You cannot clock in again.'
            ];
        }


        // Otherwise, allow clock in
        $clocking = clockings::create([
            'employee_id'  => $employee->id,
            'clock_in_time' => Carbon::now(),
            'clock_in_ip'  => $ipAddress,
            'date'         => $today
        ]);

        return [
            'action'        => 'clock_in',
            'employee'      => $employee,
            'clock_in_time' => $clocking->clock_in_time,
            'message'       => 'Successfully clocked in!'
        ];
    }
}

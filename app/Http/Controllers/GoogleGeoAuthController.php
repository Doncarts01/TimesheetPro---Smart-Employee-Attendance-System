<?php

namespace App\Http\Controllers;

use App\Models\clockings;
use App\Models\Employees;
use App\Models\allowed_networks;
use App\Models\ScanToken;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleGeoAuthController extends Controller
{
    // these are properties because DB values are runtime
    private float $officeLatitude;
    private float $officeLongitude;
    private int $allowedRadiusMeters;

    // Time window for clocking (constants)
    private const CLOCKING_START_HOUR = 6;
    private const CLOCKING_END_HOUR   = 23;

    // Static token used by the QR (change to your static value)
    private const STATIC_TOKEN = 'STATIC_CLOCKING_TOKEN_12345';

    public function __construct()
    {
        // load allowed_networks row id=1 if available, otherwise use defaults
        $coord = allowed_networks::find(1);

        // defaults — adjust if you want other fallback values
        $this->officeLatitude     = $coord->lat ?? 6.5964852;
        $this->officeLongitude    = $coord->lon ?? 3.2295335;
        $this->allowedRadiusMeters = (int) ($coord->meters_allowed ?? 100);
    }

    /**
     * Home page - renders the QR with a static token
     */
    public function Home()
    {
        $token = self::STATIC_TOKEN;
        return view('welcome', compact('token'));
    }

    /**
     * Show scan page after QR scanned / link clicked
     */
    public function show($token)
    {
        if ($token !== self::STATIC_TOKEN) {
            return redirect()->route('Index')->with('error', 'Invalid QR code.');
        }

        return view('scan', ['token' => $token]);
    }

    /**
     * Validate token + initial geolocation (AJAX endpoint).
     * If valid, save geo in session and return redirect to OAuth.
     */
    public function validateClockingRequest(Request $request)
    {
        $request->validate([
            'token'     => 'required|string',
            'latitude'  => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $token    = $request->input('token');
        $latitude = (float) $request->input('latitude');
        $longitude = (float) $request->input('longitude');

        // Validate the token (static)
        if ($token !== self::STATIC_TOKEN) {
            return response()->json([
                'message' => 'Invalid token',
                'next' => 'stop'
            ], 400);
        }

        // Geo check
        if (!$this->isWithinOfficeRadius($latitude, $longitude)) {
            return response()->json([
                'message' => 'Access denied. Must be within ' . $this->allowedRadiusMeters . 'm of office.',
                'next' => 'stop'
            ], 403);
        }

        // Time window check
        if (!$this->isWithinTimeWindow()) {
            return response()->json([
                'message' => 'Clocking allowed only between ' . self::CLOCKING_START_HOUR . ':00 and ' . self::CLOCKING_END_HOUR . ':00',
                'next' => 'stop'
            ], 403);
        }

        // Store geo in session so it can be rechecked after OAuth
        session([
            'clocking_lat' => $latitude,
            'clocking_lon' => $longitude,
            // we still store something to indicate a valid flow; not a DB id here
            'scan_token_id' => 'STATIC'
        ]);
        session()->save();

        return response()->json([
            'message' => 'Redirecting to Google login...',
            'next' => 'oauth',
            'redirect' => route('google.redirect')
        ]);
    }

    /**
     * Redirect to Google OAuth for authentication
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')
            ->scopes(['email', 'profile'])
            ->redirect();
    }

    /**
     * Handle Google OAuth callback and perform clocking (final step)
     */
    public function handleGoogleCallback(Request $request)
    {
        try {
            $ip_address = $request->ip();

            // Retrieve Google user
            $googleUser = Socialite::driver('google')->user();

            Log::info("[GeoDebug] Google user retrieved", [
                'email' => $googleUser->getEmail(),
                'name'  => $googleUser->getName(),
            ]);

            // Retrieve geolocation info stored pre-OAuth
            $latitude    = session('clocking_lat');
            $longitude   = session('clocking_lon');
            $scanTokenId = session('scan_token_id');

            Log::info("[GeoDebug] Session data retrieved", [
                'latitude'      => $latitude,
                'longitude'     => $longitude,
                'scan_token_id' => $scanTokenId,
                'is_null_lat'   => is_null($latitude),
                'is_null_lon'   => is_null($longitude),
            ]);

            // Must have session geo data
            if (!$latitude || !$longitude || !$scanTokenId) {
                return view('clocking.error', [
                    'message' => 'Session expired. Please rescan the QR code.'
                ]);
            }

            // Re-validate location (do not simply trust client)
            if (!$this->isWithinOfficeRadius((float)$latitude, (float)$longitude)) {
                Log::warning("[GeoDebug] User outside radius after OAuth", [
                    'lat' => $latitude, 'lon' => $longitude
                ]);

                // Clear session and reject
                $request->session()->forget(['clocking_lat', 'clocking_lon', 'scan_token_id']);
                return view('clocking.error', [
                    'message' => 'Access denied. You must be within the allowed office radius to clock in/out.'
                ]);
            }

            // Re-check time window
            if (!$this->isWithinTimeWindow()) {
                $request->session()->forget(['clocking_lat', 'clocking_lon', 'scan_token_id']);
                return view('clocking.error', [
                    'message' => 'Clocking is only allowed between '
                        . self::CLOCKING_START_HOUR . ':00 and '
                        . self::CLOCKING_END_HOUR . ':00.'
                ]);
            }

            // Find employee by Google email
            $employee = Employees::where('email', $googleUser->getEmail())->first();

            if (!$employee) {
                $request->session()->forget(['clocking_lat', 'clocking_lon', 'scan_token_id']);
                return view('clocking.error', [
                    'message' => 'Employee not found. Please contact your administrator.',
                    'email'   => $googleUser->getEmail()
                ]);
            }

            // Perform clocking (in/out)
            $result = $this->performClocking($employee, (float)$latitude, (float)$longitude, $ip_address);

            // Clear session after use
            $request->session()->forget(['clocking_lat', 'clocking_lon', 'scan_token_id']);

            return view('clocking.success', $result);
        } catch (\Exception $e) {
            Log::error("[GeoDebug] Exception in handleGoogleCallback", [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString()
            ]);

            // Always clear session on error
            $request->session()->forget(['clocking_lat', 'clocking_lon', 'scan_token_id']);

            return view('clocking.error', [
                'message' => 'Authentication or clocking failed. Please try again.',
                'error'   => $e->getMessage()
            ]);
        }
    }

    // ---------------- Helper methods ---------------------------------------

    /**
     * Validate if the current time is within allowed clocking window
     */
    private function isWithinTimeWindow(): bool
    {
        $now = Carbon::now();
        $start = $now->copy()->setTime(self::CLOCKING_START_HOUR, 0, 0);
        $end   = $now->copy()->setTime(self::CLOCKING_END_HOUR, 0, 0);

        return $now->between($start, $end);
    }

    /**
     * Checks if the user location is within the allowed radius of the office
     */
    private function isWithinOfficeRadius(float $userLat, float $userLon): bool
    {
        $earthRadius = 6371000; // meters

        $latFrom = deg2rad($this->officeLatitude);
        $lonFrom = deg2rad($this->officeLongitude);
        $latTo   = deg2rad($userLat);
        $lonTo   = deg2rad($userLon);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
            cos($latFrom) * cos($latTo) *
            sin($lonDelta / 2) * sin($lonDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distance = $earthRadius * $c;

        Log::info("[GeoDebug] Distance calculation", [
            'user_lat' => $userLat,
            'user_lon' => $userLon,
            'office_lat' => $this->officeLatitude,
            'office_lon' => $this->officeLongitude,
            'distance_meters' => round($distance, 2),
            'allowed_meters' => $this->allowedRadiusMeters,
            'within_range' => $distance <= $this->allowedRadiusMeters
        ]);

        return $distance <= $this->allowedRadiusMeters;
    }

    /**
     * Perform the actual clock-in / clock-out logic
     */
    private function performClocking(Employees $employee, float $latitude, float $longitude, string $ip_address): array
    {
        $today = Carbon::today();

        // If there's an open clocking -> clock out
        $openClocking = clockings::where('employee_id', $employee->id)
            ->whereDate('clock_in_time', $today)
            ->whereNull('clock_out_time')
            ->first();

        if ($openClocking) {
            $openClocking->update([
                'clock_out_time' => Carbon::now(),
                'clock_out_lat'  => $latitude,
                'clock_out_lon'  => $longitude,
                'clock_out_ip'   => $ip_address,
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

        // If already clocked in & out today -> block
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

        // Otherwise create new clock-in
        $clocking = clockings::create([
            'employee_id'   => $employee->id,
            'clock_in_time' => Carbon::now(),
            'clock_in_lat'  => $latitude,
            'clock_in_lon'  => $longitude,
            'clock_in_ip'   => $ip_address,
            'date'          => $today
        ]);

        return [
            'action'        => 'clock_in',
            'employee'      => $employee,
            'clock_in_time' => $clocking->clock_in_time,
            'message'       => 'Successfully clocked in!'
        ];
    }
}

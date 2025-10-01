<?php

namespace App\Http\Controllers;

use App\Models\allowed_networks;
use App\Models\clockings;
use App\Models\Employees;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class adminController extends Controller
{

    public function UpdatePassword(Request $request)
    {
        // validation
        $request->validate([
            "old_password" => 'required',
            "new_password" => 'required|confirmed',
        ]);

        // match the old password
        if (!Hash::check($request->old_password, Auth::user()->password)) {
            $notification = [
                "message" => "Old password does not match",
                "alert-type" => "error"
            ];
            return back()->with($notification);
        }

        // update the new password
        $userUpdate = User::findOrFail(auth::user()->id);
        $userUpdate->password = Hash::make($request->new_password);


        $notification = [
            "message" => "Password changed successfully",
            "alert-type" => "success"
        ];
        return back()->with($notification);
    }




    public function AdminDashboard()
    {
        $today = \Carbon\Carbon::today();

        // Total employees
        $data['totalEmployees'] = Employees::count();

        // Today's total clockings
        $data['todayClockingCountTotal'] = clockings::whereDate('created_at', $today)->count();

        // Today's clock-ins
        $data['todayClockinCount'] = clockings::whereDate('created_at', $today)
            ->count();

        // Today's clock-outs
        $data['todayClockoutCount'] = clockings::whereDate('created_at', $today)
            ->where(function ($q) {
                $q->whereNotNull('clock_out_time')
                    ->where('clock_out_time', '!=', '');
            })
            ->count();

        // Total clockings this month
        $data['totalClockingForTheMonth'] = clockings::whereBetween('created_at', [
            $today->copy()->startOfMonth(),
            $today->copy()->endOfMonth()
        ])
            ->count();

        return view("index", $data);
    }



    public function adminUpdateSettings(Request $request)
    {
        // dd($request->all());
        $request->validate(
            [
                'network_name' => 'required|string|max:255',
                'ip_address' => 'required|string|max:255',
                'bssid' => 'required|string|max:255',
            ],
            [
                'network_name.required' => 'The Network Name field is required.',
                'ip_address.required' => 'The IP Address field is required.',
                'bssid.required' => 'The MAC Address field is required.',
            ]
        );

        $updateNetwork = allowed_networks::findOrFail($request->network_id);

        $updateNetwork->network_name = $request->network_name;
        $updateNetwork->ip_address = $request->ip_address;
        $updateNetwork->bssid = $request->bssid;

        if ($updateNetwork->save()) {
            $notification = [
                "message" => "Network Settings Updated",
                "alert-type" => "info"
            ];
        }
        return redirect()->back()->with($notification);
    }

    public function adminUpdateLoationSettings(Request $request)
    {
        // dd($request->all());
        $request->validate(
            [
                'lat' => 'required',
                'lon' => 'required',
                'meters_allowed' => 'required',
            ],
            [
                'lat.required' => 'Latituude field is required.',
                'lon.required' => 'Longitude field is required.',
                'meters_allowed.required' => 'The Office radius field is required.',
            ]
        );

        $updateNetwork = allowed_networks::findOrFail($request->network_id);

        $updateNetwork->lat = $request->lat;
        $updateNetwork->lon = $request->lon;
        $updateNetwork->meters_allowed = $request->meters_allowed;

        if ($updateNetwork->save()) {
            $notification = [
                "message" => "Location Settings Updated",
                "alert-type" => "info"
            ];
        }
        return redirect()->back()->with($notification);
    }



    public function adminSettings()
    {
        $data['network'] = allowed_networks::findOrFail(1);
        return view("admin.manage-network", $data);
    }

    public function adminLocationSettings()
    {
        $data['network'] = allowed_networks::findOrFail(1);
        return view("admin.manage-location", $data);
    }

    public function ChangePassword()
    {
        return view("admin.change_password");
    }


    //
    public function AdminDestory(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $notification = [
            "message" => "You have Successfully Logged Out",
            "alert-type" => "info"
        ];

        return redirect('/login')->with($notification);
    }


    public function AdminProfile()
    {
        $data['id'] = Auth::user()->id;
        $data['adminData'] = User::findOrFail($data['id']);
        return view('admin.admin_profile_view', $data);
    }

    // Profile Store
    public function AdminProfileStore(Request $request)
    {
        $data['id'] = Auth::user()->id;
        $adminData = User::findOrFail($data['id']);
        $adminData->name = trim($request->name);
        $adminData->email = trim($request->email);
        $adminData->phone = trim($request->phone);

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');

            // delete old photo if exists
            if (!empty($adminData->photo) && file_exists(public_path("uploads/admin_images/" . $adminData->photo))) {
                @unlink(public_path("uploads/admin_images/" . $adminData->photo));
            }

            $fileName = date('YmdHi') . $file->getClientOriginalName();
            $file->move(public_path("uploads/admin_images"), $fileName);
            $adminData->photo = $fileName;
        }


        if ($adminData->save()) {
            $notification = [
                "message" => "Admin Profile Updated Successfully",
                "alert-type" => "success"
            ];
        } else {
            $notification = [
                "message" => "Oops Something went wrong",
                "alert-type" => "error"
            ];
        }


        return redirect()->back()->with($notification);
    }


    // manage clockng 
    public function ManageTimesheet()
    {
        $data['timesheet'] = clockings::latest()->get();
        return view('admin.manage-timesheet', $data);
    }
}

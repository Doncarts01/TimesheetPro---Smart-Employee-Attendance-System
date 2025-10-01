<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use App\Models\Employees;
use Illuminate\Support\Facades\Auth;

class employeeController extends Controller
{

    public function Home()
    {
        return view('welcome');
    }




    //

    public function adminManageEmployees()
    {
        $data['employees'] = Employees::latest()->get();
        $data['emp_count'] = Employees::count();
        return view("admin.manage-employees", $data);
    }

    public function adminStoreEmployees(Request $request)
    {

        $request->validate(
            [
                'firstname' => 'required|string|max:255',
                'lastname' => 'required|string|max:255',
                'org_email' => 'required|string|email|unique:employees,org_email|max:255',
                'email' => 'required|string|email|unique:employees,email|max:255',
            ],
            [
                'firstname.required' => 'The first name field is required.',
                'lastname.required' => 'The last name field is required.',
                'org_email.required' => 'The organizational email field is required.',
                'org_email.unique' => 'This organizational email is already in use.',
                'email.required' => 'The personal email field is required.',
                'email.unique' => 'This personal email is already in use.',
            ]
        );


        $newEmployee = new Employees();
        $newEmployee->firstname = trim($request->firstname);
        $newEmployee->lastname = trim($request->lastname);
        $newEmployee->org_email = trim($request->org_email);
        $newEmployee->email = trim($request->email);

        if ($newEmployee->save()) {

            $notification = [
                "message" => "Employee Successfully Created",
                "alert-type" => "info"
            ];
        } else {
            $notification = [
                "message" => "Something went wrong!",
                "alert-type" => "warning"
            ];
        }

        return redirect()->back()->with($notification);
    }

    public function adminUpdateEmployees(Request $request)
    {

        // dd($request->all());
        $request->validate(
            [
                'firstname' => 'required|string|max:255',
                'lastname' => 'required|string|max:255',
                'org_email' => 'required|string|max:255',
                'email' => 'required|string|max:255',
            ],
            [
                'firstname.required' => 'The first name field is required.',
                'lastname.required' => 'The last name field is required.',
                'org_email.required' => 'The organizational email field is required.',
                'email.required' => 'The personal email field is required.',
            ]
        );


        $employeeUpdate = Employees::findOrFail($request->employee_id);

        if (!$employeeUpdate) {
            $notification = [
                "message" => "Something went wrong!",
                "alert-type" => "warning"
            ];
        } else {
            $employeeUpdate->firstname = trim($request->firstname);
            $employeeUpdate->lastname = trim($request->lastname);
            $employeeUpdate->org_email = trim($request->org_email);
            $employeeUpdate->email = trim($request->email);

            if ($employeeUpdate->save())
                $notification = [
                    "message" => "Employee Successfully Created",
                    "alert-type" => "info"
                ];
        }


        return redirect()->back()->with($notification);
    }


    public function adminDeleteEmployees($id)
    {
        $decoded_id = base64_decode($id);
        $employee = Employees::findOrFail($decoded_id);
        if ($employee):
            $employee->delete();
            $notification = [
                "message" => "Employee Deleted Successfully!",
                "alert-type" => "info"
            ];
        else:
            $notification = [
                "message" => "Something went wrong!",
                "alert-type" => "warning"
            ];
        endif;

        return redirect()->back()->with($notification);
    }
}

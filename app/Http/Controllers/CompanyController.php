<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CompanyController extends Controller
{
    protected $rules;

    public function __construct()
    {
        $this->rules = [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'phone_number' => 'required',
        ];
    }

    public function create(Request $request)
    {
        $validator = Validator::make(request()->all(), $this->rules);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        try {
            DB::beginTransaction();
            $role = Auth::user()['role'];

            if ($role == 'super admin') {
                
                $company = Company::create($request->all());

                $Employee = Employee::create([
                    'company_id' => $company->id,
                    'phone_number' => $request->phone_number,
                    'role' => 'manager',
                ]);
                
                User::create([
                    'email' => $request->email,
                    'password' => bcrypt('password'),
                    'role' => 'manager',
                    'employee_id' => $Employee->id
                ]);

                DB::commit();

                return response()->json([
                    'message' => 'Company successfully created',
                    'data' => $company
                ], 400);
            } else {
                return response()->json([
                    'message' => 'Role not allowed',
                ], 400);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}

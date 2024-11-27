<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $company = Auth::user()['company_id'] ?? NULL;
        if ($user['role'] == 'employee') {
            $employees = Employee::where('role', 'employee')
                ->when(!empty($company), function ($query) use ($company) {
                    $query->where('company_id', $company);
                })
                ->paginate();
        } else {
            $employees = Employee::when(!empty($company), function ($query) use ($company) {
                $query->where('company_id', $company);
            })
            ->paginate();
        }
        return response()->json([
            'message' => 'Employees successfully returned',
            'data' => $employees
        ], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'phone_number' => 'required',
            'company_id' => 'required',
            'role' => 'required',
        ]);

        $user = Auth::user();

        try {
            DB::beginTransaction();
            if ($user['role'] == 'manager') {
                $employee = Employee::create($request->all());

                DB::commit();

                return response()->json([
                    'message' => 'Employee successfully created',
                    'data' => $employee
                ], 200);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function update($id, Request $request)
    {
        $request->validate([
            'name' => 'required',
            'phone_number' => 'required',
            'company_id' => 'required',
            'role' => 'required',
        ]);

        $user = Auth::user();

        try {
            DB::beginTransaction();
            if ($user['role'] == 'manager') {
                $employee = Employee::findOrFail($id);
                $employee->update($request->all());

                DB::commit();

                return response()->json([
                    'message' => 'Employee successfully updated',
                    'data' => $employee
                ], 200);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function delete($id)
    {
        $user = Auth::user();
        try {
            DB::beginTransaction();
            if ($user['role'] == 'manager') {
                $employee = Employee::findOrFail($id)->delete();

                DB::commit();

                return response()->json([
                    'message' => 'Employee successfully deleted',
                    'data' => $employee
                ], 200);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function show($id)
    {
        $user = Auth::user();
        try {
            if ($user['role'] == 'employee') {
                $get = Employee::where('id', $id)->first();
                $employee = Employee::where('role', 'employee')->where('company_id', $get->company_id)->get();
            } else if ($user['role'] == 'manager') {
                $employee = Employee::where('company_id', $user['company_id'])->get();
            } else {
                $employee = Employee::all();
            }
            return response()->json([
                'message' => 'Employee successfully returned',
                'data' => $employee
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}

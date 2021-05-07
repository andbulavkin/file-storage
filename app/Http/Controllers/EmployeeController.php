<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmployeeRequest;
use App\Models\Employee;
use Illuminate\Http\JsonResponse;

class EmployeeController extends Controller
{
    /**
     * Display a listing of all employees.
     *
     * @return JsonResponse
     */
    public function index():JsonResponse
    {
        $employees = Employee::all(['id','first_name','last_name','email_address','job_role']);

        return response()->json($employees,200);
    }


    /**
     * Store a newly created employee in storage.
     *
     * @param  EmployeeRequest  $request
     * @return JsonResponse
     */
    public function store(EmployeeRequest $request):JsonResponse
    {
        $validated = $request->validated();

        $employee = Employee::create($validated);

        return response()->json($employee,201);
    }

    /**
     * Display employee.
     *
     * @param Employee $employee
     * @return JsonResponse
     */
    public function show(Employee $employee):JsonResponse
    {
        return response()->json($employee,200);
    }

    /**
     * Update employee in storage.
     *
     * @param  EmployeeRequest  $request
     * @param Employee $employee
     * @return JsonResponse
     */
    public function update(EmployeeRequest $request, Employee $employee):JsonResponse
    {
        $validated = $request->validated();

        $employee->update($validated);

        return response()->json($employee,200);
    }

    /**
     * Remove employee from storage.
     *
     * @param Employee $employee
     * @return JsonResponse
     */
    public function destroy(Employee $employee):JsonResponse
    {
        $employee->delete();

        return response()->json(['success'=>'The Employee has been deleted'],202);
    }
}

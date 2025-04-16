<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use Illuminate\Http\Request;

class StaffController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $staff = Staff::with('specialization')->latest()->paginate(100)->withQueryString();;

        return inertia('admin/Staff', [
            'staff' => $staff,
            'pagination' => [
                'current_page' => $staff->currentPage(),
                'per_page' => $staff->perPage(),
            ]
        ]);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'first_name' => ['required', 'string'],
            'last_name' => ['required', 'string'],
            'email' => ['required', 'email'],
            'phone' => ['required', 'string'],
            'address' => ['nullable', 'string'],
            'emergency_contact' => ['required', 'string'],
            'commission' => ['required', 'numeric', 'min:0', 'max:100'],
            'specialization' => ['array'],
            'specialization.*' => ['exists:services,id'],
        ]);
    
        $staff = Staff::create($data);
        $staff->specialization()->sync($data['specialization'] ?? []);
    
        return back()->with('success', 'Staff created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Staff $staff)
    {
     
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Staff $staff)
    {
        $data = $request->validate([
            'first_name' => ['required', 'string'],
            'last_name' => ['required', 'string'],
            'email' => ['required', 'email'],
            'phone' => ['required', 'string'],
            'address' => ['nullable', 'string'],
            'emergency_contact' => ['required', 'string'],
            'commission' => ['required', 'numeric', 'min:0', 'max:100'],
            'specialization' => ['array'],
            'specialization.*' => ['exists:services,id'],
        ]);
    
        $staff->update($data);
        $staff->specialization()->sync($data['specialization'] ?? []);
    
        return back()->with('success', 'Staff updated successfully.');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Staff $staff)
    {
        $staff->delete();

        return back()->with('success', 'Staff deleted successfully.');
    }
}

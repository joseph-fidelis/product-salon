<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StaffController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $staff = Staff::with('specialization')->latest()->paginate(100)->withQueryString();

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
        try {
            $data = $request->validate([
                'first_name' => ['required', 'string'],
                'last_name' => ['required', 'string'],
                'email' => ['required', 'email', 'unique:staff,email'],
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

        } catch (\Exception $e) {
            // Log the error
            $errorMessage = $e->getMessage();
            Log::error('Error creating staff: ' . $errorMessage);

            // Display the actual error message to the user
            return back()->withErrors([
                'error' => $errorMessage
            ])->withInput();
        }
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
        try {
            $data = $request->validate([
                'first_name' => ['required', 'string'],
                'last_name' => ['required', 'string'],
                'email' => ['required', 'email', 'unique:staff,email,' . $staff->id],
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

        } catch (\Exception $e) {
            // Log the error
            $errorMessage = $e->getMessage();
            Log::error('Error updating staff: ' . $errorMessage);

            // Display the actual error message to the user
            return back()->withErrors([
                'error' => $errorMessage
            ])->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Staff $staff)
    {
        try {
            $staff->delete();
            return back()->with('success', 'Staff deleted successfully.');

        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            Log::error('Error deleting staff: ' . $errorMessage);

            return back()->withErrors([
                'error' => $errorMessage
            ]);
        }
    }
}

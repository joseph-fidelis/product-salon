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
        $staff = Staff::query()
            ->latest()
            ->paginate(100)
            ->withQueryString();

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
        Staff::create($request->validate());
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
        $staff->update($request->validate());
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

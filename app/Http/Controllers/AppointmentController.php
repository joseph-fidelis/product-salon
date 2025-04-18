<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Service;
use App\Models\Staff;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Commission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;


class AppointmentController extends Controller
{
    /**
     * Display the public booking form
     */
    public function showBookingForm()
    {
        // Get all services from the database without filtering by 'active'
        $services = Service::select('id', 'name', 'description', 'price', 'timeEstimate')
            ->orderBy('name')
            ->get();

        return Inertia::render('Booking/Form', [
            'services' => $services
        ]);
    }

    /**
     * Store a new appointment from public form
     */
    public function storeFromPublic(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'required|string|max:20',
            'booking_date' => 'required|date|after_or_equal:today',
            'booking_time' => 'required|date_format:H:i',
            'service_id' => 'required|exists:services,id',
            'message' => 'nullable|string',
        ]);

        // Start a database transaction
        DB::beginTransaction();

        try {
            // Create the appointment
            $appointment = Appointment::create([
                'customer_name' => $request->name,
                'customer_email' => $request->email,
                'customer_phone' => $request->phone,
                'appointment_date' => $request->booking_date,
                'appointment_time' => $request->booking_time,
                'status' => 'Pending', // Default status for new appointments
                'notes' => $request->message,
            ]);

            // Attach the service
            $appointment->services()->attach($request->service_id);

            DB::commit();

            // Return success response
            return back()->with('success', 'Your appointment has been submitted! We will contact you to confirm.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to book appointment. ' . $e->getMessage()]);
        }
    }

    /**
     * Display a listing of appointments for admin.
     */
    public function index(Request $request)
    {
        // This method should be protected by auth middleware in routes
        $query = Appointment::with(['services', 'assignedStaff'])
            ->orderBy('appointment_date', 'desc')
            ->orderBy('appointment_time', 'desc');

        // Apply filters
        if ($request->has('status') && $request->input('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->has('date') && $request->input('date')) {
            $query->whereDate('appointment_date', $request->input('date'));
        }

        if ($request->has('search') && $request->input('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_email', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%");
            });
        }

        $appointments = $query->paginate(10);

        return Inertia::render('admin/Appointments', [
            'appointments' => $appointments->through(function ($appointment) {
                return [
                    'id' => $appointment->id,
                    'customer_name' => $appointment->customer_name,
                    'customer_email' => $appointment->customer_email,
                    'customer_phone' => $appointment->customer_phone,
                    'appointment_date' => $appointment->appointment_date->format('Y-m-d'),
                    'appointment_time' => $appointment->appointment_time,
                    'status' => $appointment->status,
                    'notes' => $appointment->notes,
                    'invoice_id' => $appointment->invoice_id,
                    'services' => $appointment->services->map(function ($service) {
                        return [
                            'id' => $service->id,
                            'name' => $service->name,
                            'price' => $service->price,
                            'staff_id' => $service->pivot->staff_id,
                            'staff_name' => $service->pivot->staff_id
                                ? Staff::find($service->pivot->staff_id)->first_name . ' ' .
                                  Staff::find($service->pivot->staff_id)->last_name
                                : null,
                        ];
                    }),
                    'can_convert_to_invoice' => $appointment->canConvertToInvoice(),
                ];
            }),
            'filters' => $request->only(['status', 'date', 'search']),
            'pagination' => [
                'links' => $appointments->linkCollection()->toArray(),
                'from' => $appointments->firstItem(),
                'to' => $appointments->lastItem(),
                'total' => $appointments->total()
            ]
        ]);
    }

    /**
     * Show the form for creating a new appointment (admin).
     */
    public function create()
    {
        $services = Service::select('id', 'name', 'price', 'timeEstimate')->get();
        $staff = Staff::select('id', 'first_name', 'last_name')->get();

        return Inertia::render('admin/AppointmentCreate', [
            'services' => $services,
            'staffMembers' => $staff,
        ]);
    }

    /**
     * Store a newly created appointment by admin.
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'appointment_date' => 'required|date',
            'appointment_time' => 'required|date_format:H:i',
            'status' => 'required|in:Pending,Approved,Completed,Cancelled,No-Show',
            'notes' => 'nullable|string',
            'services' => 'required|array|min:1',
            'services.*.id' => 'required|exists:services,id',
            'services.*.staff_id' => 'nullable|exists:staff,id',
        ]);

        // Start a database transaction
        DB::beginTransaction();

        try {
            // Create the appointment
            $appointment = Appointment::create([
                'customer_name' => $request->customer_name,
                'customer_email' => $request->customer_email,
                'customer_phone' => $request->customer_phone,
                'appointment_date' => $request->appointment_date,
                'appointment_time' => $request->appointment_time,
                'status' => $request->status,
                'notes' => $request->notes,
            ]);

            // Attach services with staff assignments
            foreach ($request->services as $service) {
                $appointment->services()->attach($service['id'], [
                    'staff_id' => $service['staff_id'] ?? null,
                    'notes' => $service['notes'] ?? null
                ]);
            }

            DB::commit();

            return redirect()->route('admin.appointments.show', $appointment)
                ->with('success', 'Appointment created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to create appointment. ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified appointment.
     */
    public function show(Appointment $appointment)
    {
        $appointment->load(['services', 'assignedStaff']);
        $availableStaff = Staff::orderBy('first_name')->get();

        return Inertia::render('admin/AppointmentDetails', [
            'appointment' => [
                'id' => $appointment->id,
                'customer_name' => $appointment->customer_name,
                'customer_email' => $appointment->customer_email,
                'customer_phone' => $appointment->customer_phone,
                'appointment_date' => $appointment->appointment_date->format('Y-m-d'),
                'appointment_time' => $appointment->appointment_time,
                'status' => $appointment->status,
                'notes' => $appointment->notes,
                'invoice_id' => $appointment->invoice_id,
                'services' => $appointment->services->map(function ($service) {
                    return [
                        'id' => $service->id,
                        'name' => $service->name,
                        'price' => $service->price,
                        'staff_id' => $service->pivot->staff_id,
                        'staff_name' => $service->pivot->staff_id
                            ? Staff::find($service->pivot->staff_id)->first_name . ' ' .
                              Staff::find($service->pivot->staff_id)->last_name
                            : null,
                    ];
                }),
                'can_convert_to_invoice' => $appointment->canConvertToInvoice(),
            ],
            'availableStaff' => $availableStaff->map(function ($staff) {
                return [
                    'id' => $staff->id,
                    'name' => $staff->first_name . ' ' . $staff->last_name,
                ];
            }),
            'availableServices' => Service::select('id', 'name', 'price')->orderBy('name')->get(),
        ]);
    }

    /**
     * Update the specified appointment in storage.
     */
    public function update(Request $request, Appointment $appointment)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'appointment_date' => 'required|date',
            'appointment_time' => 'required|date_format:H:i',
            'status' => 'required|in:Pending,Approved,Completed,Cancelled,No-Show',
            'notes' => 'nullable|string',
            'services' => 'required|array|min:1',
            'services.*.id' => 'required|exists:services,id',
            'services.*.staff_id' => 'nullable|exists:staff,id',
        ]);

        // Start a database transaction
        DB::beginTransaction();

        try {
            // Update the appointment
            $appointment->update([
                'customer_name' => $request->customer_name,
                'customer_email' => $request->customer_email,
                'customer_phone' => $request->customer_phone,
                'appointment_date' => $request->appointment_date,
                'appointment_time' => $request->appointment_time,
                'status' => $request->status,
                'notes' => $request->notes,
            ]);

            // Sync services with staff assignments
            $syncData = [];
            foreach ($request->services as $service) {
                $syncData[$service['id']] = [
                    'staff_id' => $service['staff_id'] ?? null,
                    'notes' => $service['notes'] ?? null
                ];
            }
            $appointment->services()->sync($syncData);

            DB::commit();

            return redirect()->route('admin.appointments.show', $appointment)
                ->with('success', 'Appointment updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to update appointment. ' . $e->getMessage()]);
        }
    }

    /**
     * Update the status of an appointment.
     */
    public function updateStatus(Request $request, Appointment $appointment)
    {
        $request->validate([
            'status' => 'required|in:Pending,Approved,Completed,Cancelled,No-Show',
        ]);

        $appointment->update([
            'status' => $request->status
        ]);

        return back()->with('success', 'Appointment status updated successfully.');
    }

    /**
     * Assign staff to a service in an appointment.
     */
    public function assignStaff(Request $request, Appointment $appointment)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'staff_id' => 'required|exists:staff,id',
        ]);

        $appointment->services()->updateExistingPivot(
            $request->service_id,
            ['staff_id' => $request->staff_id]
        );

        return back()->with('success', 'Staff assigned successfully.');
    }

    /**
     * Convert an appointment to an invoice.
     */
    public function convertToInvoice(Appointment $appointment)
    {
        // Check if appointment can be converted to invoice
        if (!$appointment->canConvertToInvoice()) {
            return back()->withErrors(['error' => 'This appointment cannot be converted to an invoice.']);
        }

        // Start a database transaction
        DB::beginTransaction();

        try {
            // Create the invoice
            $invoice = Invoice::create([
                'customer_name' => $appointment->customer_name,
                'customer_email' => $appointment->customer_email,
                'customer_phone' => $appointment->customer_phone,
                'invoice_date' => Carbon::now()->format('Y-m-d'),
                'payment_method' => 'Cash', // Default, can be updated later
                'subtotal' => 0, // Will calculate below
                'tax' => 0, // Will calculate below
                'total' => 0, // Will calculate below
                'notes' => "Created from appointment #{$appointment->id} on {$appointment->appointment_date->format('Y-m-d')} at {$appointment->appointment_time}",
                'status' => 'Pending', // Default status
            ]);

            // Calculate totals and create invoice items
            $subtotal = 0;
            foreach ($appointment->services as $service) {
                // Skip services without assigned staff
                if (!$service->pivot->staff_id) {
                    continue;
                }

                $staff = Staff::find($service->pivot->staff_id);
                $itemTotal = $service->price; // Assuming quantity of 1
                $subtotal += $itemTotal;

                // Calculate commission
                $commission = $itemTotal * ($staff->commission_rate / 100);

                // Create invoice item
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'service_id' => $service->id,
                    'service_name' => $service->name,
                    'staff_id' => $staff->id,
                    'staff_name' => $staff->first_name . ' ' . $staff->last_name,
                    'quantity' => 1,
                    'price' => $service->price,
                    'discount' => 0, // No discount by default
                    'total' => $itemTotal,
                    'commission' => $commission,
                ]);

                // Create pending commission
                Commission::create([
                    'invoice_id' => $invoice->id,
                    'staff_id' => $staff->id,
                    'service_id' => $service->id,
                    'amount' => $commission,
                    'date' => Carbon::now()->format('Y-m-d'),
                    'status' => 'Pending',
                ]);
            }

            // Calculate tax and total
            $tax = $subtotal * 0.00; // 7% tax
            $total = $subtotal + $tax;

            // Update invoice with totals
            $invoice->update([
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $total,
            ]);

            // Link appointment to the invoice
            $appointment->update([
                'invoice_id' => $invoice->id,
                'status' => 'Completed', // Update status to completed
            ]);

            DB::commit();

            return redirect()->route('admin.invoice.show', $invoice->id)
                ->with('success', 'Appointment converted to invoice successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to convert appointment to invoice. ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified appointment from storage.
     */
    public function destroy(Appointment $appointment)
    {
        // Don't allow deleting appointments that have been invoiced
        if ($appointment->invoice_id) {
            return back()->withErrors(['error' => 'Cannot delete an appointment that has been invoiced.']);
        }

        // Start a database transaction
        DB::beginTransaction();

        try {
            // Detach all services
            $appointment->services()->detach();

            // Delete the appointment
            $appointment->delete();

            DB::commit();

            return redirect()->route('admin.appointments.index')
                ->with('success', 'Appointment deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to delete appointment. ' . $e->getMessage()]);
        }
    }
}

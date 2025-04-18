<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Service;
use App\Models\Staff;
use App\Models\Commission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class InvoiceController extends Controller
{
    /**
     * Display a listing of invoices.
     */
    public function index(Request $request)
    {
        $query = Invoice::query()
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%")
                  ->orWhere('id', 'like', "%{$search}%");
            });
        }

        if ($request->has('status') && $request->input('status') !== '') {
            $query->where('status', $request->input('status'));
        }

        $invoices = $query->paginate(10);

        return Inertia::render('admin/Invoice', [
            'invoices' => $invoices->through(function ($invoice) {
                return [
                    'id' => $invoice->id,
                    'customer_name' => $invoice->customer_name,
                    'invoice_date' => $invoice->invoice_date,
                    'total' => $invoice->total,
                    'status' => $invoice->status,
                ];
            }),
            'filters' => $request->only(['search', 'status']),
            'pagination' => [
                'links' => $invoices->linkCollection()->toArray(),
                'from' => $invoices->firstItem(),
                'to' => $invoices->lastItem(),
                'total' => $invoices->total()
            ]
        ]);
    }

    /**
     * Show the form for creating a new invoice.
     */
    public function create()
    {
        $services = Service::select('id', 'name', 'price', 'timeEstimate')->get();
        $staff = Staff::select('id', 'first_name', 'last_name', 'commission')->get();

        return Inertia::render('admin/InvoiceGenerator', [
            'services' => $services,
            'staffMembers' => $staff,
        ]);
    }

    /**
     * Store a newly created invoice in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'invoice_date' => 'required|date',
            'payment_method' => 'required|string|max:50',
            'invoice_items' => 'required|array|min:1',
            'invoice_items.*.service_id' => 'required|exists:services,id',
            'invoice_items.*.staff_id' => 'required|exists:staff,id',
            'invoice_items.*.quantity' => 'required|integer|min:1',
            'invoice_items.*.price' => 'required|numeric|min:0',
            'invoice_items.*.discount' => 'required|numeric|min:0|max:100',
        ]);

        // Calculate totals
        $subtotal = 0;
        foreach ($request->invoice_items as $item) {
            $itemSubtotal = $item['price'] * $item['quantity'];
            $discount = $itemSubtotal * ($item['discount'] / 100);
            $subtotal += $itemSubtotal - $discount;
        }

        $tax = $subtotal * 0.07; // 7% tax
        $total = $subtotal + $tax;

        // Start a database transaction
        DB::beginTransaction();

        try {
            // Create invoice
            $invoice = Invoice::create([
                'customer_name' => $request->customer_name,
                'customer_email' => $request->customer_email,
                'customer_phone' => $request->customer_phone,
                'invoice_date' => $request->invoice_date,
                'payment_method' => $request->payment_method,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $total,
                'notes' => $request->notes,
                'status' => 'Pending', // Default status
            ]);

            // Create invoice items
            foreach ($request->invoice_items as $item) {
                $itemSubtotal = $item['price'] * $item['quantity'];
                $discount = $itemSubtotal * ($item['discount'] / 100);
                $itemTotal = $itemSubtotal - $discount;

                // Get service and staff details
                $service = Service::find($item['service_id']);
                $staff = Staff::find($item['staff_id']);

                // Calculate commission
                $commission = $itemTotal * ($staff->commission/ 100);

                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'service_id' => $item['service_id'],
                    'service_name' => $service->name,
                    'staff_id' => $item['staff_id'],
                    'staff_name' => $staff->first_name . ' ' . $staff->last_name,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'discount' => $item['discount'],
                    'total' => $itemTotal,
                    'commission' => $commission,
                ]);

                // Create commission record
                if ($commission > 0) {
                    // Assuming you have a Commission model
                    Commission::create([
                        'invoice_id' => $invoice->id,
                        'staff_id' => $item['staff_id'],
                        'service_id' => $item['service_id'],
                        'amount' => $commission,
                        'date' => $request->invoice_date,
                        'status' => 'Pending',
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('admin.invoice.show', $invoice->id)
                ->with('success', 'Invoice created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to create invoice. ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified invoice.
     */
    public function show(Invoice $invoice)
    {
        $invoice->load('items');

        return Inertia::render('admin/InvoiceView', [
            'invoice' => [
                'id' => $invoice->id,
                'customer_name' => $invoice->customer_name,
                'customer_email' => $invoice->customer_email,
                'customer_phone' => $invoice->customer_phone,
                'invoice_date' => $invoice->invoice_date,
                'payment_method' => $invoice->payment_method,
                'subtotal' => $invoice->subtotal,
                'tax' => $invoice->tax,
                'total' => $invoice->total,
                'notes' => $invoice->notes,
                'status' => $invoice->status,
                'created_at' => $invoice->created_at,
                'invoice_items' => $invoice->items->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'service_name' => $item->service_name,
                        'staff_name' => $item->staff_name,
                        'quantity' => $item->quantity,
                        'price' => $item->price,
                        'discount' => $item->discount,
                        'total' => $item->total,
                    ];
                }),
            ],
        ]);
    }

    /**
     * Mark an invoice as paid and record commissions for staff
     */
    public function markAsPaid(Invoice $invoice)
    {
        // Start a database transaction
        DB::beginTransaction();

        try {
            // Update the invoice status to paid
            $invoice->update([
                'status' => 'Paid'
            ]);

            // Load invoice items if not already loaded
            if (!$invoice->relationLoaded('items')) {
                $invoice->load('items');
            }

            // Get existing commissions
            $existingCommissions = Commission::where('invoice_id', $invoice->id)->get();

            // If no commissions exist yet, create them based on invoice items but keep them as pending
            if ($existingCommissions->isEmpty()) {
                foreach ($invoice->items as $item) {
                    // Only create commission if the amount is greater than zero
                    if ($item->commission > 0) {
                        Commission::create([
                            'invoice_id' => $invoice->id,
                            'staff_id' => $item->staff_id,
                            'service_id' => $item->service_id,
                            'amount' => $item->commission,
                            'date' => $invoice->invoice_date,
                            'status' => 'Pending', // Set as Pending even though invoice is paid
                        ]);
                    }
                }
            }
            // Do NOT update existing commissions to paid

            DB::commit();

            return redirect()->route('admin.invoice.show', $invoice->id)
                ->with('success', 'Invoice marked as paid. Staff commissions are pending approval.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to mark invoice as paid. ' . $e->getMessage()]);
        }
    }

    /**
     * Update the specified invoice in storage.
     */
    public function update(Request $request, Invoice $invoice)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'invoice_date' => 'required|date',
            'payment_method' => 'required|string|max:50',
            'status' => 'required|string|in:Pending,Paid,Overdue',
            'invoice_items' => 'required|array|min:1',
            'invoice_items.*.service_id' => 'required|exists:services,id',
            'invoice_items.*.staff_id' => 'required|exists:staff,id',
            'invoice_items.*.quantity' => 'required|integer|min:1',
            'invoice_items.*.price' => 'required|numeric|min:0',
            'invoice_items.*.discount' => 'required|numeric|min:0|max:100',
        ]);

        // Calculate totals
        $subtotal = 0;
        foreach ($request->invoice_items as $item) {
            $itemSubtotal = $item['price'] * $item['quantity'];
            $discount = $itemSubtotal * ($item['discount'] / 100);
            $subtotal += $itemSubtotal - $discount;
        }

        $tax = $subtotal * 0.07; // 7% tax
        $total = $subtotal + $tax;

        // Start a database transaction
        DB::beginTransaction();

        try {
            // Update invoice
            $invoice->update([
                'customer_name' => $request->customer_name,
                'customer_email' => $request->customer_email,
                'customer_phone' => $request->customer_phone,
                'invoice_date' => $request->invoice_date,
                'payment_method' => $request->payment_method,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $total,
                'notes' => $request->notes,
                'status' => $request->status,
            ]);

            // Delete existing invoice items and commissions
            $invoice->items()->delete();
            $invoice->commissions()->delete();

            // Create new invoice items
            foreach ($request->invoice_items as $item) {
                $itemSubtotal = $item['price'] * $item['quantity'];
                $discount = $itemSubtotal * ($item['discount'] / 100);
                $itemTotal = $itemSubtotal - $discount;

                // Get service and staff details
                $service = Service::find($item['service_id']);
                $staff = Staff::find($item['staff_id']);

                // Calculate commission
                $commission = $itemTotal * ($staff->commission / 100);

                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'service_id' => $item['service_id'],
                    'service_name' => $service->name,
                    'staff_id' => $item['staff_id'],
                    'staff_name' => $staff->first_name . ' ' . $staff->last_name,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'discount' => $item['discount'],
                    'total' => $itemTotal,
                    'commission' => $commission,
                ]);

                // Create commission record
                if ($commission > 0) {
                    Commission::create([
                        'invoice_id' => $invoice->id,
                        'staff_id' => $item['staff_id'],
                        'service_id' => $item['service_id'],
                        'amount' => $commission,
                        'date' => $request->invoice_date,
                        'status' => $request->status === 'Paid' ? 'Paid' : 'Pending',
                    ]);
                }
            }

            // Update commission status if invoice is paid
            if ($request->status === 'Paid') {
                $invoice->commissions()->update(['status' => 'Paid']);
            }

            DB::commit();

            return redirect()->route('admin.invoice.show', $invoice->id)
                ->with('success', 'Invoice updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to update invoice. ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified invoice from storage.
     */
    public function destroy(Invoice $invoice)
    {
        // Start a database transaction
        DB::beginTransaction();

        try {
            // Delete related records
            $invoice->items()->delete();
            $invoice->commissions()->delete();

            // Delete the invoice
            $invoice->delete();

            DB::commit();

            return redirect()->route('admin.invoice')
                ->with('success', 'Invoice deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to delete invoice. ' . $e->getMessage()]);
        }
    }
}

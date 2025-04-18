<?php

namespace App\Http\Controllers;

use App\Models\Commission;
use App\Models\Invoice;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class CommissionController extends Controller
{
    /**
     * Display a listing of commissions.
     */
    public function index(Request $request)
    {
        $query = Commission::with(['staff', 'invoice'])
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->has('staff_id') && $request->input('staff_id')) {
            $query->where('staff_id', $request->input('staff_id'));
        }

        if ($request->has('status') && $request->input('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->has('date_from') && $request->input('date_from')) {
            $query->where('date', '>=', $request->input('date_from'));
        }

        if ($request->has('date_to') && $request->input('date_to')) {
            $query->where('date', '<=', $request->input('date_to'));
        }

        $commissions = $query->paginate(15);
        $staff = Staff::orderBy('first_name')->get(['id', 'first_name', 'last_name']);

        return Inertia::render('admin/Commissions', [
            'commissions' => $commissions->through(function ($commission) {
                return [
                    'id' => $commission->id,
                    'invoice_id' => $commission->invoice_id,
                    'staff_id' => $commission->staff_id,
                    'staff_name' => $commission->staff->first_name . ' ' . $commission->staff->last_name,
                    'service_id' => $commission->service_id,
                    'service_name' => $commission->service ? $commission->service->name : 'N/A',
                    'amount' => $commission->amount,
                    'date' => $commission->date,
                    'status' => $commission->status,
                    'invoice_number' => $commission->invoice ? $commission->invoice->id : 'N/A',
                    'customer_name' => $commission->invoice ? $commission->invoice->customer_name : 'N/A',
                ];
            }),
            'filters' => $request->only(['staff_id', 'status', 'date_from', 'date_to']),
            'pagination' => [
                'links' => $commissions->linkCollection()->toArray(),
                'from' => $commissions->firstItem(),
                'to' => $commissions->lastItem(),
                'total' => $commissions->total()
            ],
            'staff' => $staff->map(function ($member) {
                return [
                    'id' => $member->id,
                    'name' => $member->first_name . ' ' . $member->last_name,
                ];
            }),
        ]);
    }

    /**
     * Show staff member commissions summary
     */
    public function staffSummary(Request $request, Staff $staff)
    {
        $dateFrom = $request->input('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->input('date_to', now()->format('Y-m-d'));

        // Get commission summary
        $summary = DB::table('commissions')
            ->select(
                DB::raw('SUM(CASE WHEN status = "Paid" THEN amount ELSE 0 END) as paid_amount'),
                DB::raw('SUM(CASE WHEN status = "Pending" THEN amount ELSE 0 END) as pending_amount'),
                DB::raw('COUNT(DISTINCT invoice_id) as invoice_count')
            )
            ->where('staff_id', $staff->id)
            ->whereBetween('date', [$dateFrom, $dateTo])
            ->first();

        // Get commissions by service
        $serviceCommissions = DB::table('commissions')
            ->join('services', 'commissions.service_id', '=', 'services.id')
            ->select(
                'services.name as service_name',
                DB::raw('SUM(commissions.amount) as total_amount'),
                DB::raw('COUNT(*) as count')
            )
            ->where('commissions.staff_id', $staff->id)
            ->whereBetween('commissions.date', [$dateFrom, $dateTo])
            ->groupBy('services.name')
            ->orderBy('total_amount', 'desc')
            ->get();

        // Get recent commissions
        $recentCommissions = Commission::with(['invoice', 'service'])
            ->where('staff_id', $staff->id)
            ->whereBetween('date', [$dateFrom, $dateTo])
            ->orderBy('date', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($commission) {
                return [
                    'id' => $commission->id,
                    'invoice_id' => $commission->invoice_id,
                    'service_name' => $commission->service ? $commission->service->name : 'N/A',
                    'customer_name' => $commission->invoice ? $commission->invoice->customer_name : 'N/A',
                    'amount' => $commission->amount,
                    'date' => $commission->date,
                    'status' => $commission->status,
                ];
            });

        return Inertia::render('admin/StaffCommissionSummary', [
            'staff' => [
                'id' => $staff->id,
                'name' => $staff->first_name . ' ' . $staff->last_name,
                'commission_rate' => $staff->commission_rate,
            ],
            'summary' => [
                'paid_amount' => $summary->paid_amount,
                'pending_amount' => $summary->pending_amount,
                'total_amount' => $summary->paid_amount + $summary->pending_amount,
                'invoice_count' => $summary->invoice_count,
            ],
            'serviceCommissions' => $serviceCommissions,
            'recentCommissions' => $recentCommissions,
            'filters' => [
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
            ]
        ]);
    }

    /**
     * Update commission status
     */
    public function updateStatus(Request $request, Commission $commission)
    {
        $request->validate([
            'status' => 'required|in:Paid,Pending',
        ]);

        $commission->update([
            'status' => $request->status,
        ]);

        return back()->with('success', 'Commission status updated successfully.');
    }

    /**
     * Batch update commission status
     */
    public function batchUpdate(Request $request)
    {
        $request->validate([
            'commission_ids' => 'required|array',
            'commission_ids.*' => 'exists:commissions,id',
            'status' => 'required|in:Paid,Pending',
        ]);

        Commission::whereIn('id', $request->commission_ids)
            ->update(['status' => $request->status]);

        return back()->with('success', count($request->commission_ids) . ' commissions updated successfully.');
    }

    /**
     * Record commission for a staff member manually
     */
    public function recordManual(Request $request)
    {
        $request->validate([
            'staff_id' => 'required|exists:staff,id',
            'service_id' => 'required|exists:services,id',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'status' => 'required|in:Paid,Pending',
            'notes' => 'nullable|string|max:255',
        ]);

        Commission::create([
            'staff_id' => $request->staff_id,
            'service_id' => $request->service_id,
            'invoice_id' => null, // Manual entry, no invoice
            'amount' => $request->amount,
            'date' => $request->date,
            'status' => $request->status,
            'notes' => $request->notes,
        ]);

        return back()->with('success', 'Commission recorded successfully.');
    }

    /**
     * Get commission statistics for all staff
     */
    public function statistics(Request $request)
    {
        $dateFrom = $request->input('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->input('date_to', now()->format('Y-m-d'));

        // Get commission totals by staff
        $staffCommissions = DB::table('commissions')
            ->join('staff', 'commissions.staff_id', '=', 'staff.id')
            ->select(
                'staff.id',
                'staff.first_name',
                'staff.last_name',
                DB::raw('SUM(CASE WHEN commissions.status = "Paid" THEN commissions.amount ELSE 0 END) as paid_amount'),
                DB::raw('SUM(CASE WHEN commissions.status = "Pending" THEN commissions.amount ELSE 0 END) as pending_amount'),
                DB::raw('COUNT(DISTINCT commissions.invoice_id) as invoice_count')
            )
            ->whereBetween('commissions.date', [$dateFrom, $dateTo])
            ->groupBy('staff.id', 'staff.first_name', 'staff.last_name')
            ->orderBy('paid_amount', 'desc')
            ->get();

        // Get daily commission totals for chart
        $dailyCommissions = DB::table('commissions')
            ->select(
                DB::raw('DATE(date) as day'),
                DB::raw('SUM(amount) as total_amount'),
                DB::raw('COUNT(*) as count')
            )
            ->whereBetween('date', [$dateFrom, $dateTo])
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        // Get service commission totals
        $serviceCommissions = DB::table('commissions')
            ->join('services', 'commissions.service_id', '=', 'services.id')
            ->select(
                'services.id',
                'services.name',
                DB::raw('SUM(commissions.amount) as total_amount'),
                DB::raw('COUNT(*) as count')
            )
            ->whereBetween('commissions.date', [$dateFrom, $dateTo])
            ->groupBy('services.id', 'services.name')
            ->orderBy('total_amount', 'desc')
            ->limit(10)
            ->get();

        return Inertia::render('admin/CommissionStatistics', [
            'staffCommissions' => $staffCommissions,
            'dailyCommissions' => $dailyCommissions,
            'serviceCommissions' => $serviceCommissions,
            'filters' => [
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
            ],
            'totals' => [
                'paid' => $staffCommissions->sum('paid_amount'),
                'pending' => $staffCommissions->sum('pending_amount'),
                'total' => $staffCommissions->sum('paid_amount') + $staffCommissions->sum('pending_amount'),
            ]
        ]);
    }
}

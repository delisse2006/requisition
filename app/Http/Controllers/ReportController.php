<?php

namespace App\Http\Controllers;

use App\Models\Requisition;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf; // ✅ Correct facade (capital 'P')
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Display a listing of requisitions with optional status filter.
     */
    public function index(Request $request)
    {
        $requisitions = Requisition::with('user')
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.reports.index', compact('requisitions'));
    }

    /**
     * Export requisitions to PDF with optional date filtering.
     */
    public function exportPDF(Request $request)
    {
        $startDate = $request->query('start');
        $endDate = $request->query('end');

        $query = Requisition::with('user')->orderBy('created_at', 'desc');

        // Apply date range filter if both dates are provided and valid
        if ($startDate && $endDate) {
            try {
                $start = Carbon::parse($startDate)->startOfDay();
                $end = Carbon::parse($endDate)->endOfDay();
                $query->whereBetween('created_at', [$start, $end]);
            } catch (\Exception $e) {
                // Invalid date format — ignore filter
            }
        }

        $requisitions = $query->get();

        $pdf = Pdf::loadView('admin.reports.pdf', [
            'requisitions' => $requisitions,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);

        return $pdf->download('requisition-report-' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Show system summary statistics.
     */
    public function summary()
    {
        $totalRequisitions = Requisition::count();
        $pendingRequisitions = Requisition::where('status', 'pending')->count();
        $completedRequisitions = Requisition::where('status', 'paid')->count();
        $totalUsers = User::count();
        $employees = User::where('role', 'employee')->count();
        $accountants = User::where('role', 'accountant')->count();

        return view('admin.reports.summary', compact(
            'totalRequisitions',
            'pendingRequisitions',
            'completedRequisitions',
            'totalUsers',
            'employees',
            'accountants'
        ));
    }
}

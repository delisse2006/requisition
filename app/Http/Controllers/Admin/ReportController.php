<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Requisition;
use App\Models\User;
use Illuminate\Http\Request;
use PDF;

class ReportController extends Controller
{
    public function index()
    {
        $requisitions = Requisition::with('user')
            ->when(request('status'), fn($q) => $q->where('status', request('status')))
            ->when(request('urgency'), fn($q) => $q->where('urgency', request('urgency')))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.reports.index', compact('requisitions'));
    }

    public function exportPDF(Request $request)
    {
        $query = Requisition::with('user')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('urgency')) {
            $query->where('urgency', $request->urgency);
        }

        if ($request->filled('month')) {
            // expected format YYYY-MM
            try {
                [$year, $month] = explode('-', $request->month);
                $query->whereYear('created_at', $year)->whereMonth('created_at', $month);
            } catch (\Throwable $e) {
                // ignore invalid month format
            }
        }

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $requisitions = $query->get();

        $pdf = PDF::loadView('admin.reports.pdf', compact('requisitions'));
        return $pdf->download('requisition-report-' . now()->format('Y-m-d') . '.pdf');
    }

    public function summary()
    {
        $stats = [
            'total_users' => User::count(),
            'total_requisitions' => Requisition::count(),
            'pending_requisitions' => Requisition::where('status', 'pending')->count(),
            'bought_requisitions' => Requisition::where('status', 'bought')->count(),
            'done_requisitions' => Requisition::where('status', 'done')->count(),
            // backwards-compatible alias expected by views
            'completed_requisitions' => Requisition::where('status', 'done')->count(),
            'paid_requisitions' => Requisition::where('status', 'paid')->count(),
            'employees' => User::where('role', 'employee')->count(),
            'accountants' => User::where('role', 'accountant')->count(),
            'admins' => User::where('role', 'admin')->count(),
        ];

        // Recent requisitions for the summary view
        $recentRequisitions = Requisition::with('user')->latest()->take(10)->get();

        return view('admin.reports.summary', compact('stats', 'recentRequisitions'));
    }
}
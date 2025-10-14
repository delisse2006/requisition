<?php

namespace App\Http\Controllers;

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
            ->latest()
            ->paginate(20);

        return view('admin.reports.index', compact('requisitions'));
    }

    public function exportPDF()
    {
        $requisitions = Requisition::with('user')->get();
        $pdf = PDF::loadView('admin.reports.pdf', compact('requisitions'));
        return $pdf->download('requisition-report-' . now()->format('Y-m-d') . '.pdf');
    }

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
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

    public function exportPDF()
    {
        $requisitions = Requisition::with('user')->get();
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
            'paid_requisitions' => Requisition::where('status', 'paid')->count(),
            'employees' => User::where('role', 'employee')->count(),
            'accountants' => User::where('role', 'accountant')->count(),
            'admins' => User::where('role', 'admin')->count(),
        ];

        return view('admin.reports.summary', compact('stats'));
    }
}
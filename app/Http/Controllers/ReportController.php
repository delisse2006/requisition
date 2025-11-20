<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Requisition;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Display a listing of requisitions with optional status filter.
     */
    public function index(Request $request)
    {
        $query = Requisition::with('user');

        // Apply status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Apply date range filter
        if ($request->filled('start_date') && $request->filled('end_date')) {
            try {
                $startDate = Carbon::parse($request->start_date)->startOfDay();
                $endDate = Carbon::parse($request->end_date)->endOfDay();
                $query->whereBetween('created_at', [$startDate, $endDate]);
            } catch (\Exception $e) {
                // Invalid date format - ignore filter
            }
        }

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('item_name', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%')
                  ->orWhere('requisition_no', 'like', '%' . $search . '%')
                  ->orWhereHas('user', function ($u) use ($search) {
                      $u->where('name', 'like', '%' . $search . '%');
                  });
            });
        }

        $requisitions = $query->latest()->paginate(20)->withQueryString();

        return view('admin.reports.index', compact('requisitions'));
    }

    /**
     * Export requisitions to PDF with optional date filtering.
     */
    public function exportPDF(Request $request)
    {
        $query = Requisition::with('user');

        // Apply status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Apply urgency filter
        if ($request->filled('urgency')) {
            $query->where('urgency', $request->urgency);
        }

        // Apply date filters: support `month` (YYYY-MM) or `start_date`/`end_date`
        if ($request->filled('month')) {
            try {
                [$year, $month] = explode('-', $request->month);
                $query->whereYear('created_at', $year)->whereMonth('created_at', $month);
            } catch (\Throwable $e) {
                // ignore invalid month format
            }
        } else {
            try {
                $startDate = $request->filled('start_date') ? Carbon::parse($request->start_date)->startOfDay() : null;
                $endDate = $request->filled('end_date') ? Carbon::parse($request->end_date)->endOfDay() : null;
                if ($startDate && $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                } elseif ($startDate) {
                    $query->whereDate('created_at', '>=', $startDate);
                } elseif ($endDate) {
                    $query->whereDate('created_at', '<=', $endDate);
                }
            } catch (\Exception $e) {
                // Invalid date format - ignore filter
            }
        }

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('item_name', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%')
                  ->orWhere('requisition_no', 'like', '%' . $search . '%')
                  ->orWhereHas('user', function ($u) use ($search) {
                      $u->where('name', 'like', '%' . $search . '%');
                  });
            });
        }

        $requisitions = $query->orderBy('created_at', 'desc')->get();

        $pdf = Pdf::loadView('admin.reports.pdf', [
            'requisitions' => $requisitions,
            'filters' => [
                'status' => $request->status,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'search' => $request->search,
            ],
            'generated_at' => now()->format('F j, Y \a\t g:i A')
        ]);

        return $pdf->download('requisition-report-' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Show system summary statistics.
     */
    public function summary()
    {
        // Requisition statistics
        $totalRequisitions = Requisition::count();
        $pendingRequisitions = Requisition::where('status', 'pending')->count();
        $boughtRequisitions = Requisition::where('status', 'bought')->count();
        $doneRequisitions = Requisition::where('status', 'done')->count();
        $paidRequisitions = Requisition::where('status', 'paid')->count();
        
        // User statistics
        $totalUsers = User::count();
        $employees = User::where('role', 'employee')->count();
        $accountants = User::where('role', 'accountant')->count();
        $admins = User::where('role', 'admin')->count();
        
        // Monthly statistics
        $thisMonthRequisitions = Requisition::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
            
        $thisMonthUsers = User::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
            
        // Recent activity
        $recentRequisitions = Requisition::with('user')
            ->latest()
            ->take(10)
            ->get();
            
        $recentUsers = User::latest()
            ->take(10)
            ->get();

        return view('admin.reports.summary', compact(
            'totalRequisitions',
            'pendingRequisitions',
            'boughtRequisitions',
            'doneRequisitions',
            'paidRequisitions',
            'totalUsers',
            'employees',
            'accountants',
            'admins',
            'thisMonthRequisitions',
            'thisMonthUsers',
            'recentRequisitions',
            'recentUsers'
        ));
    }
}

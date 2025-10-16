<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class accountantcontroller extends Controller
{
    use App\Models\Requisition;
use Carbon\Carbon;

public function dashboard()
{
    $stats = [
        'total' => Requisition::count(),
        'pending' => Requisition::where('status', 'pending')->count(),
        'completed' => Requisition::where('status', 'paid')->count(),
        'this_month' => Requisition::whereBetween('created_at', [
            Carbon::now()->startOfMonth(),
            Carbon::now()->endOfMonth()
        ])->count(),
        'high_urgency' => Requisition::where('urgency', 'high')->count(),
    ];

    $requisitions = Requisition::with('user')
        ->whereIn('status', ['pending', 'bought', 'done'])
        ->latest()
        ->paginate(10);

    return view('accountant.dashboard', compact('stats', 'requisitions'));
}
}

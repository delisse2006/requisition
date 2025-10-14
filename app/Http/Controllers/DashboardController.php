<?php

namespace App\Http\Controllers;

use App\Models\Requisition;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
  public function index()
{
    $user = auth()->user();
    
    // Get statistics
    $totalRequisitions = Requisition::count();
    $pendingRequisitions = Requisition::where('status', 'pending')->count();
    $completedRequisitions = Requisition::where('status', 'paid')->count();
    $monthlyRequisitions = Requisition::whereMonth('created_at', now()->month)->count();

    if ($user->isEmployee()) {
        $requisitions = $user->requisitions()->latest()->paginate(10);
        // Employee-specific stats
        $pendingRequisitions = $user->requisitions()->where('status', 'pending')->count();
        $completedRequisitions = $user->requisitions()->where('status', 'paid')->count();
        $monthlyRequisitions = $user->requisitions()->whereMonth('created_at', now()->month)->count();
    } else {
        $requisitions = Requisition::with('user')->latest()->paginate(10);
    }

    return view('dashboard', compact(
        'requisitions', 
        'totalRequisitions', 
        'pendingRequisitions', 
        'completedRequisitions', 
        'monthlyRequisitions'
    ));
}
}
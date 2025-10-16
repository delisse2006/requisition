<?php

namespace App\Http\Controllers;

use App\Models\Requisition;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
  public function index()
{
    $user = auth()->user();
    
    // Statistics for dashboard cards
    $stats = [
        'total' => Requisition::count(),
        'pending' => Requisition::where('status', 'pending')->count(),
        'completed' => Requisition::where('status', 'paid')->count(),
        'this_month' => Requisition::whereMonth('created_at', now()->month)->count(),
        'high_urgency' => Requisition::where('urgency', 'high')->count(),
    ];
    
    // Employee sees only their requisitions
    if ($user->isEmployee()) {
        $stats['total'] = $user->requisitions()->count();
        $stats['pending'] = $user->requisitions()->where('status', 'pending')->count();
        $stats['completed'] = $user->requisitions()->where('status', 'paid')->count();
        $stats['this_month'] = $user->requisitions()->whereMonth('created_at', now()->month)->count();
        $stats['high_urgency'] = $user->requisitions()->where('urgency', 'high')->count();
        
        $requisitions = $user->requisitions()->latest()->paginate(10);
    } else {
        $requisitions = Requisition::with('user')->latest()->paginate(10);
    }

    return view('dashboard', compact('requisitions', 'stats'));
}
}
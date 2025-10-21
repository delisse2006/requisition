<?php

namespace App\Http\Controllers;

use App\Models\Requisition;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // ✅ ROLE-BASED REQUISITION LOADING
        if ($user->isAdmin()) {
            // Admin sees ALL requisitions
            $requisitions = Requisition::with('user')
                ->latest()
                ->paginate(15)
                ->withQueryString();
                
            // Admin statistics
            $stats = [
                'total' => Requisition::count(),
                'pending' => Requisition::where('status', 'pending')->count(),
                'bought' => Requisition::where('status', 'bought')->count(),
                'done' => Requisition::where('status', 'done')->count(),
                'paid' => Requisition::where('status', 'paid')->count(),
                'this_month' => Requisition::whereMonth('created_at', now()->month)->count(),
                'high_urgency' => Requisition::where('urgency', 'high')->count(),
            ];
        } 
        elseif ($user->isAccountant()) {
            // Accountant sees ALL requisitions (but can only update status)
            $requisitions = Requisition::with('user')
                ->latest()
                ->paginate(15)
                ->withQueryString();
                
            // Accountant statistics
            $stats = [
                'total' => Requisition::count(),
                'pending' => Requisition::where('status', 'pending')->count(),
                'bought' => Requisition::where('status', 'bought')->count(),
                'done' => Requisition::where('status', 'done')->count(),
                'paid' => Requisition::where('status', 'paid')->count(),
                'this_month' => Requisition::whereMonth('created_at', now()->month)->count(),
                'high_urgency' => Requisition::where('urgency', 'high')->count(),
            ];
        } 
        else {
            // Employee sees only THEIR requisitions
            $requisitions = $user->requisitions()
                ->latest()
                ->paginate(15)
                ->withQueryString();
                
            // Employee statistics
            $stats = [
                'total' => $user->requisitions()->count(),
                'pending' => $user->requisitions()->where('status', 'pending')->count(),
                'bought' => $user->requisitions()->where('status', 'bought')->count(),
                'done' => $user->requisitions()->where('status', 'done')->count(),
                'paid' => $user->requisitions()->where('status', 'paid')->count(),
                'this_month' => $user->requisitions()->whereMonth('created_at', now()->month)->count(),
                'high_urgency' => $user->requisitions()->where('urgency', 'high')->count(),
            ];
        }

        return view('dashboard', compact('requisitions', 'stats'));
    }
}
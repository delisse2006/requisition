<?php

namespace App\Http\Controllers;

use App\Models\Requisition;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
  public function index()
{
    $user = auth()->user();
    $query = Requisition::with('user');

    // Apply search filter
    if (request('search')) {
        $query->where(function($q) {
            $q->where('item_name', 'like', '%' . request('search') . '%')
              ->orWhere('description', 'like', '%' . request('search') . '%')
              ->orWhereHas('user', function($u) {
                  $u->where('name', 'like', '%' . request('search') . '%');
              });
        });
    }

    // Apply status filter
    if (request('status')) {
        $query->where('status', request('status'));
    }

    // Apply urgency filter
    if (request('urgency')) {
        $query->where('urgency', request('urgency'));
    }

    // Employee sees only their requisitions
    if ($user->isEmployee()) {
        $query->where('user_id', $user->id);
    }

    $requisitions = $query->latest()->paginate(10)->withQueryString();

    return view('dashboard', compact('requisitions'));
}
}
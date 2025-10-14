@extends('layouts.app')

@section('content')
<div class="container">
    <h2>System Summary</h2>
    
    <div class="row">
        <div class="col-md-3">
            <div class="card text-white bg-primary mb-3">
                <div class="card-body">
                    <h5 class="card-title">Total Requisitions</h5>
                    <h2 class="mb-0">{{ $totalRequisitions }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning mb-3">
                <div class="card-body">
                    <h5 class="card-title">Pending</h5>
                    <h2 class="mb-0">{{ $pendingRequisitions }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success mb-3">
                <div class="card-body">
                    <h5 class="card-title">Completed</h5>
                    <h2 class="mb-0">{{ $completedRequisitions }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-info mb-3">
                <div class="card-body">
                    <h5 class="card-title">Total Users</h5>
                    <h2 class="mb-0">{{ $totalUsers }}</h2>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">User Breakdown</div>
                <div class="card-body">
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Employees</span>
                            <span class="badge bg-success">{{ $employees }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Accountants</span>
                            <span class="badge bg-info">{{ $accountants }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Admins</span>
                            <span class="badge bg-danger">{{ $totalUsers - $employees - $accountants }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
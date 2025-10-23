@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>System Summary</h2>
        <a href="{{ route('admin.reports.export.pdf') }}" class="btn btn-success">
            <i class="fas fa-file-pdf me-1"></i> Export PDF
        </a>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary mb-3">
                <div class="card-body text-center">
                    <h5 class="card-title">Total Requisitions</h5>
                    <h2 class="mb-0">{{ $stats['total_requisitions'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning mb-3">
                <div class="card-body text-center">
                    <h5 class="card-title">Pending</h5>
                    <h2 class="mb-0">{{ $stats['pending_requisitions'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-info mb-3">
                <div class="card-body text-center">
                    <h5 class="card-title">Bought</h5>
                    <h2 class="mb-0">{{ $stats['bought_requisitions'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success mb-3">
                <div class="card-body text-center">
                    <h5 class="card-title">Completed</h5>
                    <h2 class="mb-0">{{ $stats['completed_requisitions'] }}</h2>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-secondary mb-3">
                <div class="card-body text-center">
                    <h5 class="card-title">Total Users</h5>
                    <h2 class="mb-0">{{ $stats['total_users'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-danger mb-3">
                <div class="card-body text-center">
                    <h5 class="card-title">Admins</h5>
                    <h2 class="mb-0">{{ $stats['admins'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-info mb-3">
                <div class="card-body text-center">
                    <h5 class="card-title">Accountants</h5>
                    <h2 class="mb-0">{{ $stats['accountants'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success mb-3">
                <div class="card-body text-center">
                    <h5 class="card-title">Employees</h5>
                    <h2 class="mb-0">{{ $stats['employees'] }}</h2>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Recent Requisitions</h5>
                </div>
                <div class="card-body p-0">
                    @if($recentRequisitions->isEmpty())
                        <div class="text-center py-4">
                            <i class="fas fa-inbox" style="font-size: 3rem; color: #dee2e6;"></i>
                            <p class="mt-2 mb-0">No recent requisitions</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Item</th>
                                        <th>User</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentRequisitions as $req)
                                    <tr>
                                        <td>{{ $req->item_name }}</td>
                                        <td>{{ $req->user->name }}</td>
                                        <td>
                                            <span class="badge bg-{{ $req->status == 'pending' ? 'secondary' : ($req->status == 'bought' ? 'info' : ($req->status == 'done' ? 'primary' : 'success')) }} rounded-pill">
                                                {{ ucfirst($req->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $req->created_at->format('Y-m-d') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">User Distribution</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>Admins</span>
                            <span class="badge bg-danger rounded-pill">{{ $stats['admins'] }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>Accountants</span>
                            <span class="badge bg-info rounded-pill">{{ $stats['accountants'] }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>Employees</span>
                            <span class="badge bg-success rounded-pill">{{ $stats['employees'] }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

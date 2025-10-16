<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Stock Requisition System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        /* Background for all authenticated pages */
        body {
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), 
                        url('{{ asset('images/backgrounds/back.jpg') }}') center center no-repeat fixed;
            background-size: cover;
            min-height: 100vh;
            padding-top: 56px;
        }
        
        /* Content wrapper with semi-transparent background */
        .content-wrapper {
            background: rgba(20, 18, 18, 0.92);
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
            box-shadow: 0 0 30px rgba(99, 89, 89, 0.46);
        }
        
        .navbar-brand {
            font-weight: 600;
        }
        .user-role-badge {
            font-size: 0.75em;
            padding: 2px 6px;
        }
    </style>
</head>

<style>
.dark-mode {
    background-color: #121212 !important;
    color: #e0e0e0 !important;
}
.dark-mode .navbar-dark { background-color: #1e1e1e !important; }
.dark-mode .table-light { background-color: #1e1e1e !important; color: #e0e0e0 !important; }
.dark-mode .card { background-color: #1e1e1e !important; border-color: #333 !important; color: #e0e0e0 !important; }
.dark-mode .form-control, .dark-mode .form-select {
    background-color: #2d2d2d !important;
    border-color: #444 !important;
    color: #e0e0e0 !important;
}
.dark-mode .btn-outline-primary {
    color: #e0e0e0 !important;
    border-color: #0d6efd !important;
}
.dark-mode .btn-outline-primary:hover {
    background-color: #0d6efd !important;
    border-color: #0d6efd !important;
}
</style>

<script>
// Dark mode toggle
document.addEventListener('DOMContentLoaded', function() {
    const darkModeToggle = document.getElementById('darkModeToggle');
    const darkModeText = document.getElementById('darkModeText');
    const body = document.body;
    
    // Check if dark mode was previously enabled
    if (localStorage.getItem('darkMode') === 'enabled') {
        body.classList.add('dark-mode');
        darkModeText.textContent = 'Light Mode';
        darkModeToggle.innerHTML = '<i class="fas fa-sun me-2"></i><span>Light Mode</span>';
    }
    
    if (darkModeToggle) {
        darkModeToggle.addEventListener('click', function() {
            body.classList.toggle('dark-mode');
            
            if (body.classList.contains('dark-mode')) {
                localStorage.setItem('darkMode', 'enabled');
                darkModeToggle.innerHTML = '<i class="fas fa-sun me-2"></i><span>Light Mode</span>';
            } else {
                localStorage.setItem('darkMode', 'disabled');
                darkModeToggle.innerHTML = '<i class="fas fa-moon me-2"></i><span>Dark Mode</span>';
            }
        });
    }
});
</script>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="{{ route('dashboard') }}">Stock Requisition</a>
            
            <div class="d-flex align-items-center">
                @auth
                    <div class="dropdown">
                        <a href="#" 
                           class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" 
                           id="userDropdown" 
                           data-bs-toggle="dropdown">
                            <img src="{{ auth()->user()->avatar_small_url }}" 
                                 alt="Avatar" 
                                 class="rounded-circle me-2" 
                                 width="32" 
                                 height="32"
                                 style="object-fit: cover;">
                            <span class="d-none d-sm-inline">{{ auth()->user()->name }}</span>
                        </a>
                        
                        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark text-small shadow">
                            <li class="px-3 py-2">
                                <div class="d-flex align-items-center">
                                    <img src="{{ auth()->user()->avatar_url }}" 
                                         alt="Avatar" 
                                         class="rounded-circle me-2" 
                                         width="40" 
                                         height="40"
                                         style="object-fit: cover;">
                                    <div>
                                        <div class="fw-bold">{{ auth()->user()->name }}</div>
                                        <div class="text-muted small">
                                            <span class="badge bg-{{ auth()->user()->role == 'admin' ? 'danger' : (auth()->user()->role == 'accountant' ? 'info' : 'success') }} user-role-badge">
                                                {{ ucfirst(auth()->user()->role) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            
                            <li>
                                <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                    <i class="fas fa-user me-2"></i>My Profile
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('dashboard') }}">
                                    <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                                </a>
                            </li>

                            <!-- ✅ FIXED: Accountant Dashboard link -->
                            @if(auth()->user()->isAccountant() || auth()->user()->isAdmin())
                                <li>
                                    <a class="dropdown-item" href="{{ route('accountant.dashboard') }}">
                                        <i class="fas fa-calculator me-2"></i>Accountant Dashboard
                                    </a>
                                </li>
                            @endif

                            <!-- ✅ FIXED: Admin Panel section -->
                            @if(auth()->check() && auth()->user()->isAdmin())
                                <li><hr class="dropdown-divider"></li>
                                <li><span class="dropdown-item-text text-muted">Admin Panel</span></li>
                                <li>
                                                                          <i class="fas fa-users me-2"></i>Manage Users
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.reports.index') }}">
                                        <i class="fas fa-chart-bar me-2"></i>Reports
                                    </a>
                                </li>
                            @endif
                            
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                    @csrf

                                 <li>
                                          <button class="dropdown-item" id="darkModeToggle">
                                           <i class="fas fa-moon me-2"></i>
                                           <span id="darkModeText">Dark Mode</span>
                                   </button>
                                 </li>
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="fas fa-sign-out-alt me-2"></i>Sign out
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline-light btn-sm">Login</a>
                @endauth
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="content-wrapper">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        });
    </script>
    @stack('scripts')
</body>
</html>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

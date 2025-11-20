<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Stock Requisition System</title>
    <!-- ✅ FIXED: Proper CDN URLs without extra spaces -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        /* Page background (subtle) */
        body {
            background: url('{{ asset('images/backgrounds/back.jpg') }}') center center no-repeat fixed;
            background-size: cover;
            min-height: 100vh;
            padding-top: 56px;
        }

        /* Content wrapper: light background by default; dark-mode will override */
        .content-wrapper {
            background: rgba(255, 255, 255, 0.96);
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
            box-shadow: 0 0 30px rgba(0, 0, 0, 0.08);
            color: #212529;
        }
        
        .navbar-brand {
            font-weight: 600;
        }
        .user-role-badge {
            font-size: 0.75em;
            padding: 2px 6px;
        }

        /* Dark mode styles */
        .dark-mode {
            background-color: #121212 !important;
            color: #e0e0e0 !important;
        }
        .dark-mode .navbar-dark { 
            background-color: #1e1e1e !important; 
        }
        .dark-mode .table-light { 
            background-color: #1e1e1e !important; 
            color: #e0e0e0 !important; 
        }
        .dark-mode .card { 
            background-color: #1e1e1e !important; 
            border-color: #333 !important; 
            color: #e0e0e0 !important; 
        }
        .dark-mode .form-control, 
        .dark-mode .form-select {
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
        .dark-mode .dropdown-menu {
            background-color: #2d2d2d !important;
            border-color: #444 !important;
        }
        .dark-mode .dropdown-item {
            color: #e0e0e0 !important;
        }
        .dark-mode .dropdown-item:hover {
            background-color: #3d3d3d !important;
        }
    </style>
    <style>
        /* Ensure Bootstrap modals appear above custom overlays/backdrops */
        .modal {
            z-index: 20050 !important;
        }
        /* make backdrop less aggressive and keep it below modal */
        .modal-backdrop {
            background-color: rgba(0,0,0,0.12) !important;
            /* allow clicks to pass through the backdrop so it doesn't block forms */
            pointer-events: none !important;
        }
        .modal-backdrop.show {
            z-index: 20040 !important;
            opacity: 0.6 !important;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="{{ route('dashboard') }}">Stock Requisition</a>
            
            <button class="navbar-toggler" 
                    type="button" 
                    data-bs-toggle="collapse" 
                    data-bs-target="#navbarNav" 
                    aria-controls="navbarNav" 
                    aria-expanded="false" 
                    aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <div class="d-flex align-items-center ms-auto">
                    @auth
                    {{-- Notification bell (centralized) --}}
                    <div class="me-3">
                        <div class="dropdown">
                            <a href="#" id="notifBell" class="text-white text-decoration-none position-relative" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-bell fa-lg"></i>
                                <span id="notifBadge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger d-none">0</span>
                            </a>

                            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark shadow" aria-labelledby="notifBell" style="min-width:320px; max-width:420px;">
                                <li class="dropdown-header px-3 py-2 d-flex justify-content-between align-items-center">
                                    <strong>Notifications</strong>
                                    <small><a href="{{ route('notifications.index') }}" class="text-decoration-none text-muted">See all</a></small>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li id="notifLoading" class="px-3 py-3 text-center text-muted small">Loading…</li>
                                <div id="notifItems" class="list-group list-group-flush d-none"></div>
                            </ul>
                        </div>
                    </div>
                        <div class="dropdown">
                            <a href="#" 
                               class="d-flex align-items-center text-white text-decoration-none dropdown-toggle position-relative" 
                               id="userDropdown" 
                               data-bs-toggle="dropdown" 
                               aria-expanded="false">
                                <img src="{{ auth()->user()->avatar_small_url }}" 
                                     alt="Avatar" 
                                     class="rounded-circle me-2" 
                                     width="32" 
                                     height="32"
                                     style="object-fit: cover;">
                                <span class="d-none d-sm-inline">{{ auth()->user()->name }}</span>
                                
                                {{-- Notification count is fetched via AJAX to avoid DB errors at view render time --}}
                                <span id="navNotifCount" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger d-none"></span>
                            </a>
                            
                            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark text-small shadow" 
                                aria-labelledby="userDropdown">
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

                                <!-- Admin Panel Links -->
                                @if(auth()->user()->isAdmin())
                                    <li><hr class="dropdown-divider"></li>
                                    <li><span class="dropdown-item-text text-muted">Admin Panel</span></li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.users.index') }}">
                                            <i class="fas fa-users me-2"></i>Manage Users
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.reports.index') }}">
                                            <i class="fas fa-chart-bar me-2"></i>Reports
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.reports.summary') }}">
                                            <i class="fas fa-tachometer-alt me-2"></i>System Summary
                                        </a>
                                    </li>
                                @endif

                                <!-- Accountant Panel Links -->
                                @if(auth()->user()->isAccountant())
                                    <li><hr class="dropdown-divider"></li>
                                    <li><span class="dropdown-item-text text-muted">Accountant Panel</span></li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('accountant.dashboard') }}">
                                            <i class="fas fa-calculator me-2"></i>Accountant Dashboard
                                        </a>
                                    </li>
                                @endif

                                <!-- Notifications Link -->
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item d-flex justify-content-between align-items-center" href="{{ route('notifications.index') }}">
                                        <span><i class="fas fa-bell me-2"></i>Notifications</span>
                                        <span id="userDropdownNotifBadge" class="badge bg-danger rounded-pill d-none"></span>
                                    </a>
                                </li>
                                
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <button class="dropdown-item" id="darkModeToggle">
                                        <i class="fas fa-moon me-2"></i>
                                        <span id="darkModeText">Dark Mode</span>
                                    </button>
                                </li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                        @csrf
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
        </div>
    </nav>

    <div class="container-fluid">
        <div class="content-wrapper">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
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

    <!-- ✅ FIXED: Proper CDN URL without extra spaces -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Defensive: remove any leftover modal backdrops that may persist between navigation
            try {
                document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
            } catch (e) {
                // ignore
            }
            // Dark mode toggle
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
                        darkModeText.textContent = 'Light Mode';
                        darkModeToggle.innerHTML = '<i class="fas fa-sun me-2"></i><span>Light Mode</span>';
                    } else {
                        localStorage.setItem('darkMode', 'disabled');
                        darkModeText.textContent = 'Dark Mode';
                        darkModeToggle.innerHTML = '<i class="fas fa-moon me-2"></i><span>Dark Mode</span>';
                    }
                });
            }
            
            // CSRF token setup (guard if axios isn't loaded)
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            if (window.axios && window.axios.defaults && window.axios.defaults.headers) {
                window.axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken;
            }

            // Notification polling & UI — wrap in try/catch so any unexpected error doesn't break the page
            try {
                const notifBadge = document.getElementById('notifBadge');
                const notifItems = document.getElementById('notifItems');
                const notifLoading = document.getElementById('notifLoading');

                const latestUrl = "{{ route('notifications.getLatest') }}";
                const unreadCountUrl = "{{ route('notifications.unreadCount') }}";
                const markAsReadTemplate = "{{ url('notifications') }}" + '/:id/mark-as-read';

                function setBadge(count) {
                    // bell badge
                    if (notifBadge) {
                        if (count > 0) {
                            notifBadge.textContent = count;
                            notifBadge.classList.remove('d-none');
                        } else {
                            notifBadge.classList.add('d-none');
                        }
                    }

                    // nav/profile dropdown badge
                    const navBadge = document.getElementById('navNotifCount');
                    const userDropdownBadge = document.getElementById('userDropdownNotifBadge');
                    if (navBadge) {
                        if (count > 0) {
                            navBadge.textContent = count;
                            navBadge.classList.remove('d-none');
                        } else {
                            navBadge.classList.add('d-none');
                        }
                    }
                    if (userDropdownBadge) {
                        if (count > 0) {
                            userDropdownBadge.textContent = count;
                            userDropdownBadge.classList.remove('d-none');
                        } else {
                            userDropdownBadge.classList.add('d-none');
                        }
                    }
                }

                async function fetchUnreadCount() {
                    try {
                        const res = await fetch(unreadCountUrl, { headers: { 'X-CSRF-TOKEN': csrfToken } });
                        const data = await res.json();
                        setBadge(data.count || 0);
                    } catch (e) {
                        // ignore
                    }
                }

                function renderNotifications(list) {
                    if (!notifItems || !notifLoading) return;
                    notifItems.innerHTML = '';
                    if (!list || list.length === 0) {
                        notifLoading.textContent = 'No notifications';
                        notifLoading.classList.remove('d-none');
                        notifItems.classList.add('d-none');
                        return;
                    }

                    notifLoading.classList.add('d-none');
                    notifItems.classList.remove('d-none');

                    list.forEach(n => {
                        const a = document.createElement('a');

                        // build href depending on notification payload
                        let href = '{{ route('notifications.index') }}';
                        try {
                            if (n.data && n.data.requisition_id) href = '/requisitions/' + n.data.requisition_id;
                            else if (n.data && n.data.user_id) href = '/admin/users/' + n.data.user_id;
                        } catch (e) {
                            href = '{{ route('notifications.index') }}';
                        }

                        a.href = href;
                        a.className = 'list-group-item list-group-item-action d-flex justify-content-between align-items-start';
                        a.dataset.id = n.id;

                        const left = document.createElement('div');
                        left.className = 'ms-2 me-auto';

                        // icon + title
                        const iconHtml = n.icon ? '<i class="' + n.icon + ' me-2"></i>' : '';
                        const title = n.title || 'Notification';
                        const message = n.message ? n.message : '';
                        const time = n.created_at_human ? n.created_at_human : '';

                        left.innerHTML = '<div class="fw-bold">' + iconHtml + title + '</div>' +
                                         '<div class="small text-muted">' + message + '</div>' +
                                         '<div class="small text-muted mt-1">' + time + '</div>';

                        // badge indicator on right
                        const right = document.createElement('div');
                        right.className = 'text-end';
                        const badge = document.createElement('span');
                        badge.className = 'badge rounded-pill ' + (n.badge_color ? ('bg-' + n.badge_color) : 'bg-secondary');
                        badge.textContent = n.read ? '' : '•';
                        right.appendChild(badge);

                        a.appendChild(left);
                        a.appendChild(right);

                        a.addEventListener('click', async function(e) {
                            e.preventDefault();
                            const id = this.dataset.id;
                            // mark as read then navigate
                            try {
                                const url = markAsReadTemplate.replace(':id', id);
                                await fetch(url, { method: 'POST', headers: { 'X-CSRF-TOKEN': csrfToken } });
                            } catch (err) {
                                // ignore
                            }
                            window.location.href = this.href;
                        });

                        notifItems.appendChild(a);
                    });
                }

                async function fetchLatest() {
                    try {
                        const res = await fetch(latestUrl, { headers: { 'X-CSRF-TOKEN': csrfToken } });
                        const data = await res.json();
                        renderNotifications(data.notifications || []);
                        setBadge(data.unread_count || 0);
                    } catch (e) {
                        // ignore
                    }
                }

                // initial fetch
                fetchUnreadCount();
                fetchLatest();

                // Poll every 15s
                setInterval(fetchUnreadCount, 15000);
                setInterval(fetchLatest, 30000);
            } catch (err) {
                console.warn('Notification polling skipped:', err);
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel') - {{ config('app.name', 'Modestik') }}</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">

    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">

    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">

    <!-- Typography System & Admin CSS -->
    <link rel="stylesheet" href="{{ asset('css/typography.css') }}?v=1.0">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}?v={{ time() }}">

    @yield('styles')
</head>
<body class="admin-body">
    <!-- Sidebar -->
    <aside class="admin-sidebar" id="adminSidebar">
        <div class="sidebar-header">
            <a href="{{ route('admin.dashboard') }}" class="sidebar-brand">
                <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name', 'Modestik') }} Logo">
            </a>
            <button class="sidebar-close d-lg-none" id="sidebarClose"><i class="fas fa-times"></i></button>
        </div>

        <div class="sidebar-nav-wrapper">
            <nav class="sidebar-nav">
                <ul class="nav-list">
                    <li class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <a href="{{ route('admin.dashboard') }}"><i class="fas fa-th-large"></i><span>Dashboard</span></a>
                    </li>

                    <!-- Sales & Orders Submenu -->
                    <li class="nav-item {{ request()->routeIs('admin.orders.*') || request()->routeIs('admin.complaints.*') ? 'active' : '' }}">
                        <a href="#salesOrdersSubmenu" data-bs-toggle="collapse" class="d-flex align-items-center justify-content-between text-decoration-none {{ request()->routeIs('admin.orders.*') || request()->routeIs('admin.complaints.*') ? '' : 'collapsed' }}">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fas fa-shopping-cart text-success"></i>
                                <span class="fw-bold">Sales & Orders</span>
                            </div>
                            <div class="d-flex align-items-center gap-1">
                                @php $pendingOrdersCount = \App\Models\Order::where('status', 'pending')->count(); @endphp
                                @if($pendingOrdersCount > 0)
                                    <span class="nav-badge">{{ $pendingOrdersCount }}</span>
                                @endif
                                <i class="fas fa-chevron-down" style="font-size: 11px;"></i>
                            </div>
                        </a>
                        <div class="collapse {{ request()->routeIs('admin.orders.*') ? 'show' : '' }}" id="salesOrdersSubmenu">
                            <ul class="nav flex-column ms-3 mt-1 pe-2" style="font-size: 13px; list-style: none;">
                                <li class="nav-item py-1">
                                    <a href="{{ route('admin.orders.index') }}" class="text-nowrap {{ request()->routeIs('admin.orders.index') ? 'fw-bold text-success' : 'text-dark' }}">
                                        <i class="fas fa-list me-2 text-primary"></i>All Orders
                                    </a>
                                </li>
                                <li class="nav-item py-1">
                                    <a href="{{ route('admin.orders.create') }}" class="text-nowrap {{ request()->routeIs('admin.orders.create') ? 'fw-bold text-success' : 'text-dark' }}">
                                        <i class="fas fa-plus-circle me-2 text-success"></i>Create Orders
                                    </a>
                                </li>
                                <li class="nav-item py-1">
                                    <a href="{{ route('admin.orders.incomplete') }}" class="text-nowrap {{ request()->routeIs('admin.orders.incomplete') ? 'fw-bold text-success' : 'text-dark' }}">
                                        <i class="fas fa-shopping-basket me-2 text-danger"></i>Incomplete Orders
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <li class="nav-item {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.products.index') }}"><i class="fas fa-box"></i><span>Products</span></a>
                    </li>

                    <li class="nav-item {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.categories.index') }}"><i class="fas fa-sitemap"></i><span>Categories</span></a>
                    </li>

                    <li class="nav-item {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.customers.index') }}"><i class="fas fa-users"></i><span>Customers</span></a>
                    </li>

                    <!-- Accounting System -->
                    <li class="nav-item {{ request()->routeIs('admin.accounting.*') ? 'active' : '' }}">
                        <a href="#accountingSubmenu" data-bs-toggle="collapse" class="d-flex align-items-center justify-content-between text-decoration-none {{ request()->routeIs('admin.accounting.*') ? '' : 'collapsed' }}">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fas fa-calculator" style="color: #d97d8c;"></i>
                                <span class="fw-bold">Accounting</span>
                            </div>
                            <i class="fas fa-chevron-down" style="font-size: 11px;"></i>
                        </a>
                        <div class="collapse {{ request()->routeIs('admin.accounting.*') ? 'show' : '' }}" id="accountingSubmenu">
                            <ul class="nav flex-column ms-3 mt-1 pe-2" style="font-size: 13px; list-style: none;">
                                <li class="nav-item py-1"><a href="{{ route('admin.accounting.dashboard') }}" class="{{ request()->routeIs('admin.accounting.dashboard') ? 'fw-bold text-danger' : 'text-dark' }}"><i class="fas fa-chart-pie me-2"></i>Dashboard</a></li>
                                <li class="nav-item py-1"><a href="{{ route('admin.accounting.money-in') }}" class="{{ request()->routeIs('admin.accounting.money-in') ? 'fw-bold text-danger' : 'text-dark' }}"><i class="fas fa-arrow-down me-2 text-success"></i>Money In</a></li>
                                <li class="nav-item py-1"><a href="{{ route('admin.accounting.money-out') }}" class="{{ request()->routeIs('admin.accounting.money-out') ? 'fw-bold text-danger' : 'text-dark' }}"><i class="fas fa-arrow-up me-2 text-danger"></i>Money Out</a></li>
                                <li class="nav-item py-1"><a href="{{ route('admin.accounting.sales-profit') }}" class="{{ request()->routeIs('admin.accounting.sales-profit') ? 'fw-bold text-danger' : 'text-dark' }}"><i class="fas fa-chart-line me-2 text-primary"></i>Sales & Profit</a></li>
                                <li class="nav-item py-1"><a href="{{ route('admin.accounting.purchases.index') }}" class="{{ request()->routeIs('admin.accounting.purchases.*') ? 'fw-bold text-danger' : 'text-dark' }}"><i class="fas fa-shopping-cart me-2 text-secondary"></i>Purchases</a></li>
                                <li class="nav-item py-1"><a href="{{ route('admin.accounting.customer-due') }}" class="{{ request()->routeIs('admin.accounting.customer-due') ? 'fw-bold text-danger' : 'text-dark' }}"><i class="fas fa-hand-holding-usd me-2 text-warning"></i>Customer Due</a></li>
                                <li class="nav-item py-1"><a href="{{ route('admin.accounting.supplier-due') }}" class="{{ request()->routeIs('admin.accounting.supplier-due') ? 'fw-bold text-danger' : 'text-dark' }}"><i class="fas fa-file-invoice-dollar me-2 text-danger"></i>Supplier Due</a></li>
                                <li class="nav-item py-1"><a href="{{ route('admin.accounting.accounts.index') }}" class="{{ request()->routeIs('admin.accounting.accounts.*') ? 'fw-bold text-danger' : 'text-dark' }}"><i class="fas fa-wallet me-2 text-info"></i>Cash & Accounts</a></li>
                                <li class="nav-item py-1"><a href="{{ route('admin.accounting.investments.index') }}" class="{{ request()->routeIs('admin.accounting.investments.*') ? 'fw-bold text-danger' : 'text-dark' }}"><i class="fas fa-coins me-2 text-warning"></i>Investments</a></li>
                                <li class="nav-item py-1"><a href="{{ route('admin.accounting.salaries.index') }}" class="{{ request()->routeIs('admin.accounting.salaries.*') ? 'fw-bold text-danger' : 'text-dark' }}"><i class="fas fa-user-tie me-2 text-dark"></i>Salaries</a></li>
                                <li class="nav-item py-1"><a href="{{ route('admin.accounting.owner-withdrawals.index') }}" class="{{ request()->routeIs('admin.accounting.owner-withdrawals.*') ? 'fw-bold text-danger' : 'text-dark' }}"><i class="fas fa-money-bill-wave me-2 text-success"></i>Owner Withdrawals</a></li>
                                <li class="nav-item py-1"><a href="{{ route('admin.accounting.courier-settlements') }}" class="{{ request()->routeIs('admin.accounting.courier-settlements') ? 'fw-bold text-danger' : 'text-dark' }}"><i class="fas fa-truck-loading me-2 text-primary"></i>Courier Settlements</a></li>
                                <li class="nav-item py-1"><a href="{{ route('admin.accounting.profit-loss') }}" class="{{ request()->routeIs('admin.accounting.profit-loss') ? 'fw-bold text-danger' : 'text-dark' }}"><i class="fas fa-balance-scale me-2 text-info"></i>Profit & Loss</a></li>
                                <li class="nav-item py-1"><a href="{{ route('admin.accounting.inventory-value') }}" class="{{ request()->routeIs('admin.accounting.inventory-value') ? 'fw-bold text-danger' : 'text-dark' }}"><i class="fas fa-cubes me-2 text-secondary"></i>Inventory Value</a></li>
                                <li class="nav-item py-1"><a href="{{ route('admin.accounting.monthly-closing') }}" class="{{ request()->routeIs('admin.accounting.monthly-closing') ? 'fw-bold text-danger' : 'text-dark' }}"><i class="fas fa-calendar-check me-2 text-primary"></i>Monthly Closing</a></li>
                                <li class="nav-item py-1"><a href="{{ route('admin.accounting.reports.index') }}" class="{{ request()->routeIs('admin.accounting.reports.*') ? 'fw-bold text-danger' : 'text-dark' }}"><i class="fas fa-file-excel me-2 text-success"></i>Financial Reports</a></li>
                                <li class="nav-item py-1"><a href="{{ route('admin.accounting.transactions.index') }}" class="{{ request()->routeIs('admin.accounting.transactions.*') ? 'fw-bold text-danger' : 'text-dark' }}"><i class="fas fa-list-alt me-2 text-dark"></i>Transaction Ledger</a></li>
                            </ul>
                        </div>
                    </li>




                    <li class="nav-item {{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.reviews.index') }}"><i class="fas fa-star"></i><span>Reviews</span></a>
                    </li>

                    <li class="nav-item {{ request()->routeIs('admin.media.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.media.index') }}"><i class="fas fa-photo-video"></i><span>Media</span></a>
                    </li>

                    <li class="nav-item {{ request()->routeIs('admin.pages.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.pages.index') }}"><i class="fas fa-file-alt"></i><span>Pages</span></a>
                    </li>

                    <li class="nav-item {{ request()->routeIs('admin.blogs.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.blogs.index') }}"><i class="fas fa-blog"></i><span>Blog Posts</span></a>
                    </li>

                    <li class="nav-item {{ request()->routeIs('admin.shipping-zones.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.shipping-zones.index') }}"><i class="fas fa-truck"></i><span>Shipping</span></a>
                    </li>

                    <li class="nav-item {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.payments.index') }}"><i class="fas fa-credit-card"></i><span>Payments</span></a>
                    </li>

                    <li class="nav-header">Analytics & System</li>
                    <li class="nav-item {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.reports.sales') }}"><i class="fas fa-chart-bar"></i><span>Reports</span></a>
                    </li>

                    <li class="nav-item {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.settings.index') }}"><i class="fas fa-cog"></i><span>Settings</span></a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('admin.couriers.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.couriers.settings') }}"><i class="fas fa-shipping-fast"></i><span>Courier Setup</span></a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('admin.analytics-config.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.analytics-config.index') }}"><i class="fas fa-chart-line"></i><span>Pixel & Analytics Config</span></a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('admin.backup.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.backup.index') }}"><i class="fas fa-database"></i><span>Database Backup</span></a>
                    </li>

                    <!-- Extra intakes from existing system -->
                    <li class="nav-header">Other Intake</li>
                    <li class="nav-item {{ request()->routeIs('admin.attributes.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.attributes.index') }}"><i class="fas fa-palette"></i><span>Attributes</span></a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('admin.coupons.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.coupons.index') }}"><i class="fas fa-ticket-alt"></i><span>Coupons</span></a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('admin.campaigns.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.campaigns.index') }}"><i class="fas fa-bullhorn"></i><span>Campaigns</span></a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('admin.sliders.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.sliders.index') }}"><i class="fas fa-images"></i><span>Sliders</span></a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('admin.banners.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.banners.index') }}"><i class="fas fa-ad"></i><span>Banners</span></a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('admin.blogs.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.blogs.index') }}"><i class="fas fa-blog"></i><span>Blogs</span></a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('admin.testimonials.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.testimonials.index') }}"><i class="fas fa-quote-left"></i><span>Testimonials</span></a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.users.index') }}"><i class="fas fa-user-shield"></i><span>Users</span></a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.roles.index') }}"><i class="fas fa-user-lock"></i><span>Roles</span></a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('admin.activity-logs.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.activity-logs.index') }}"><i class="fas fa-history"></i><span>Activity Log</span></a>
                    </li>
                </ul>
            </nav>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="admin-main">
        <!-- Top Bar -->
        <header class="admin-topbar">
            <div class="topbar-left">
                <button class="sidebar-toggle" id="sidebarToggle"><i class="fas fa-bars"></i></button>
                <h4 class="topbar-title">@yield('title', 'Dashboard')</h4>
            </div>
            <div class="topbar-right">
                <div class="topbar-search-wrapper d-none d-md-block">
                    <i class="fas fa-search topbar-search-icon"></i>
                    <input type="text" class="topbar-search-input" placeholder="Search..">
                </div>
                <div class="dropdown">
                    <button class="topbar-icon-btn dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" title="Notifications" style="background:none; border:none; padding:0; display:flex; align-items:center; justify-content:center;">
                        <i class="far fa-bell"></i>
                        @php $unreadCount = auth()->check() ? auth()->user()->unreadNotifications()->count() : 0; @endphp
                        @if($unreadCount > 0)
                            <span class="topbar-icon-badge">{{ $unreadCount }}</span>
                        @endif
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end p-2" style="width: 320px; max-height: 400px; overflow-y: auto; border-radius: 12px; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05); border: 1px solid #f1f5f9;">
                        <li class="dropdown-header d-flex justify-content-between align-items-center py-2 px-3">
                            <span class="fw-bold text-dark">Notifications</span>
                            @if($unreadCount > 0)
                                <a href="#" class="text-xs text-primary" id="markAllNotificationsRead" style="font-size: 11px; text-decoration: none;">Mark all as read</a>
                            @endif
                        </li>
                        <li><hr class="dropdown-divider my-1"></li>
                        @if(auth()->check())
                            @forelse(auth()->user()->notifications()->take(5)->get() as $notification)
                                <li class="p-2 border-bottom {{ $notification->read_at ? '' : 'bg-light' }}" style="border-radius: 6px; margin-bottom: 2px;">
                                    <a href="{{ $notification->data['link'] ?? '#' }}" class="d-flex flex-column text-decoration-none py-1 px-2" style="color: inherit;">
                                        <span class="text-sm font-semibold" style="font-size: 13px; color: #000; font-weight: 500;">{{ $notification->data['message'] ?? 'Notification received' }}</span>
                                        <small class="text-muted mt-1" style="font-size: 11px;">{{ $notification->created_at->diffForHumans() }}</small>
                                    </a>
                                </li>
                            @empty
                                <li class="text-center p-3 text-muted" style="font-size: 13px;">No notifications</li>
                            @endforelse
                        @else
                            <li class="text-center p-3 text-muted" style="font-size: 13px;">No notifications</li>
                        @endif
                    </ul>
                </div>
                <div class="dropdown">
                    <a href="#" class="topbar-user dropdown-toggle" data-bs-toggle="dropdown">
                        <img src="{{ auth()->user()->avatar_url ?? 'https://www.gravatar.com/avatar/' . md5(strtolower(trim(auth()->user()->email))) . '?d=mp&s=100' }}" alt="Avatar" class="topbar-avatar">
                        <span class="d-none d-md-inline">{{ auth()->user()->name }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="{{ route('customer.profile') }}"><i class="fas fa-user me-2"></i>Profile</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.settings.index') }}"><i class="fas fa-cog me-2"></i>Settings</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button class="dropdown-item text-danger"><i class="fas fa-sign-out-alt me-2"></i>Logout</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <div class="admin-content">
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
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>

        <!-- Admin Footer -->
        <footer class="admin-footer">
            <p>&copy; {{ date('Y') }} Modestik. All rights reserved | Developed by <strong><a href="https://engr-saad.com/" target="_blank" style="color: #000080 !important; text-decoration: none;">Engr Saad</a></strong></p>
        </footer>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

        // Sidebar Toggle
        $('#sidebarToggle').click(function() {
            if ($(window).width() < 992) {
                $('#adminSidebar').toggleClass('show');
            } else {
                $('#adminSidebar').toggleClass('collapsed');
                $('.admin-main').toggleClass('expanded');
            }
        });
        $('#sidebarClose').click(function() { $('#adminSidebar').removeClass('show'); });

        // Select2 init
        $('.select2').select2({ theme: 'bootstrap-5' });

        // Delete confirmation
        $(document).on('click', '.btn-delete', function(e) {
            e.preventDefault();
            let form = $(this).closest('form');
            Swal.fire({
                title: 'Are you sure?',
                text: "This action cannot be undone!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) form.submit();
            });
        });

        // Mark notifications as read
        $(document).on('click', '#markAllNotificationsRead', function(e) {
            e.preventDefault();
            $.post("{{ route('admin.notifications.read-all') }}")
                .done(function() {
                    $('.topbar-icon-badge').remove();
                    $('#markAllNotificationsRead').remove();
                    $('.dropdown-menu .bg-light').removeClass('bg-light');
                    Swal.fire({
                        icon: 'success',
                        title: 'Notifications marked as read',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 2000
                    });
                });
        });
    </script>

    <script src="{{ asset('js/typography.js') }}"></script>
    @yield('scripts')
</body>
</html>

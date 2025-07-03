<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="DUKANI - Advanced Retail Management System" />
    <meta name="keywords" content="retail, management, inventory, sales" />
    <meta name="theme-color" content="#0f172a" />
    <meta http-equiv="Content-Language" content="en-us" />
    <title>{{ setting('shop_name', 'DUKANI') }} | Retail Intelligence Platform</title>

    <!-- DNS Hint -->
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link rel="preconnect" href="https://code.jquery.com">
    <link rel="preconnect" href="https://cdn.datatables.net">

    <!-- Core Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('css/dukani/master.css') }}" />
</head>

<body>
<!-- 🌀 Loading Skeleton -->
<div id="loader" style="position:fixed;inset:0;z-index:9999;background:#0f172a;display:flex;align-items:center;justify-content:center;">
    <div class="spinner-border text-light" role="status"><span class="visually-hidden">Loading...</span></div>
</div>

<!-- 🌐 App Container -->
<div class="app-container" id="appRoot" style="visibility:hidden;">
    @php
        $shopName = setting('shop_name', 'DUKANI');
    @endphp
    <div class="sidebar" id="sidebar">

        <ul class="sidebar-nav-list">
            @if(Auth::check())
                @if(auth()->user()->isAdmin())
                    <li class='sidebar-nav-item'>
                        <a class='sidebar-nav-link' href="{{ route('users.index') }}">
                            <span class="nav-text">👥 Manage Users</span>
                        </a>
                    </li>
                @endif
                <li class='sidebar-nav-item'><a class='sidebar-nav-link' href='{{ route('reports.index') }}'><span class="nav-text">📊 Reports</span></a></li>
                <li class='sidebar-nav-item'><a class='sidebar-nav-link active' href='{{ route('counter.report') }}'><span class="nav-text">💵 POS Terminal</span></a></li>
                <li class='sidebar-nav-item'><a class='sidebar-nav-link' href='{{ route('audit-logs.index') }}'><span class="nav-text">📖 Audit Logs</span></a></li>
                <li class='sidebar-nav-item dropdown'>
                    <a class='sidebar-nav-link dropdown-toggle' href='#' data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="nav-text">📦 Stock</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-dark">
                        <li><a class="dropdown-item" href="{{ route('products.index') }}">🧺 Product</a></li>
                        <li><a class="dropdown-item" href="{{ route('stock.adjust.form') }}">🛠️ Stock Adjustment</a></li>
                    </ul>
                </li>
                <li class='sidebar-nav-item'><a class='sidebar-nav-link' href='{{ route('settings.index') }}'><span class="nav-text">⚙️ Settings</span></a></li>
                <li class='sidebar-nav-item'><a class='sidebar-nav-link' href='{{ route('expenses.index') }}'><span class="nav-text">💰 Expenses</span></a></li>
            @else
                <script>window.location.href = '{{ route('login') }}';</script>
            @endif
        </ul>
    </div>

    <div class="content-area" id="contentArea">
        <nav class="top-navbar">
            <button class="btn btn-link text-white d-lg-none sidebar-toggle-btn" type="button"
                    data-bs-toggle="offcanvas" data-bs-target="#sidebar" aria-controls="sidebar">
                <i class="bi bi-list fs-4"></i>
            </button>
            <h1 class="mb-0 title">{{ $shopName }}</h1>
            <ul class="navbar-nav ms-auto user-menu-dropdown">
                <li class='nav-item dropdown'>
                    <a class='nav-link dropdown-toggle' href='#' id="navbarDropdownUser" role="button"
                       data-bs-toggle="dropdown" aria-expanded="false">
                        <sl-avatar shape="circle" initials="{{ substr(Auth::user()->name, 0, 1) }}" style="--size: 1.75rem;"></sl-avatar>
                        <span class="ms-2 d-none d-md-inline">{{ Auth::user()->name }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-dark">
                        <li><a class="dropdown-item" href="#"><i class="bi bi-person-fill me-2"></i> Profile</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    <i class="bi bi-box-arrow-right me-2"></i> Log Out
                                </button>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </nav>

        <div class="container main-content">
            @yield('content')
        </div>
    </div>
</div>
<div class="fab" id="quickActionBtn">
    <i class="bi bi-lightning-charge-fill" style="font-size: 1.25rem;"></i>
</div>
<sl-switch class="theme-toggle" onsl-change="toggleTheme()">
    <i class="bi bi-moon-fill" slot="checked"></i>
    <i class="bi bi-sun-fill" slot="unchecked"></i>
</sl-switch>

@stack('scripts')
<script defer src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script defer>
    document.addEventListener('DOMContentLoaded', function () {
        const htmlEl = document.documentElement;
        const savedTheme = localStorage.getItem('dukani-theme') || 'dark';
        htmlEl.setAttribute('data-theme', savedTheme);
        const toggle = document.querySelector('.theme-toggle');
        if (toggle) toggle.checked = savedTheme === 'light';

        // Sidebar collapse
        const sidebar = document.getElementById('sidebar');
        const contentArea = document.getElementById('contentArea');
        const sidebarToggle = document.getElementById('sidebarToggle');
        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', () => {
                sidebar.classList.toggle('collapsed');
                contentArea.classList.toggle('sidebar-collapsed');
                sidebarToggle.querySelector('i').classList.toggle('bi-chevron-left');
                sidebarToggle.querySelector('i').classList.toggle('bi-chevron-right');
            });
        }
        sidebar.querySelectorAll('.sidebar-nav-item.dropdown').forEach(item => {
            item.addEventListener('mouseenter', () => {
                if (sidebar.classList.contains('collapsed')) {
                    item.querySelector('.dropdown-menu')?.classList.add('show');
                }
            });
            item.addEventListener('mouseleave', () => {
                if (sidebar.classList.contains('collapsed')) {
                    item.querySelector('.dropdown-menu')?.classList.remove('show');
                }
            });
        });
        document.getElementById('quickActionBtn')?.addEventListener('click', () => {
            document.querySelector('.quick-action-menu')?.show();
        });
        document.getElementById('loader').style.display = 'none';
        document.getElementById('appRoot').style.visibility = 'visible';
        if (document.querySelector('.datatable')) {
            const css = document.createElement('link');
            css.rel = 'stylesheet';
            css.href = 'https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css';
            document.head.appendChild(css);

            const script = document.createElement('script');
            script.src = 'https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js';
            script.onload = () => $('.datatable').DataTable();
            document.body.appendChild(script);
        }
    });

    function toggleTheme() {
        const html = document.documentElement;
        const newTheme = html.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
        html.setAttribute('data-theme', newTheme);
        localStorage.setItem('dukani-theme', newTheme);
    }
</script>
</body>
</html>

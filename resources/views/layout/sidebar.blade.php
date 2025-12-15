{{-- resources/views/layout/sidebar.blade.php --}}
@php
    $user = auth()->user();

    // combined page
    $isAC = request()->routeIs('admin.access-control.index');
@endphp

<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="{{ route('admin.dashboard') }}" class="app-brand-link">
            <span class="app-brand-text demo menu-text fw-bolder ms-2">Admin Panel</span>
        </a>

        <a href="javascript:void(0);"
           class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
            <i class="bx bx-chevron-left bx-sm align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">

        {{-- Dashboard --}}
        @if($user->hasPermission('view_dashboard'))
            <li class="menu-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <a href="{{ route('admin.dashboard') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-home-circle"></i>
                    <div>Dashboard</div>
                </a>
            </li>
        @endif

        {{-- Access Control header --}}
        @if(
            $user->hasPermission('manage_roles') ||
            $user->hasPermission('manage_permissions') ||
            $user->hasPermission('manage_users')
        )
            <li class="menu-header small text-uppercase">
                <span class="menu-header-text">Access Control</span>
            </li>
        @endif

        {{-- ✅ Single link for Roles & Permissions (common page) --}}
        @if($user->hasPermission('manage_roles') || $user->hasPermission('manage_permissions'))
            <li class="menu-item {{ $isAC ? 'active' : '' }}">
                <a href="{{ route('admin.access-control.index', ['tab' => request('tab','roles')]) }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-shield-quarter"></i>
                    <div>Roles & Permissions</div>
                </a>
            </li>
        @endif

        {{-- User Access --}}
        @if($user->hasPermission('manage_users'))
            <li class="menu-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <a href="{{ route('admin.users.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-user"></i>
                    <div>User Access</div>
                </a>
            </li>
        @endif

    </ul>
</aside>

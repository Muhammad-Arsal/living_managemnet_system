<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="{{ route('admin.dashboard') }}" class="app-brand-link">
            <span class="app-brand-logo">
                <img src="{{ asset('img/logo-mark.svg') }}" alt="{{ config('app.name') }}">
            </span>
            <span class="app-brand-text demo">
                Living
                <small>Admin Panel</small>
            </span>
        </a>
        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large d-block d-xl-none ms-auto">
            <i class="iconify iconify-lg" data-icon="bx:bx-menu"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        <li class="menu-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <a href="{{ route('admin.dashboard') }}" class="menu-link">
                <i class="menu-icon tf-icon iconify iconify-lg" data-icon="bx:bx-home-circle"></i>
                <div>Dashboard</div>
            </a>
        </li>

        <li class="menu-item {{ request()->routeIs('admin.staff.*') ? 'active' : '' }}">
            <a href="{{ route('admin.staff.index') }}" class="menu-link">
                <i class="menu-icon tf-icon iconify iconify-lg" data-icon="bx:bx-user"></i>
                <div>Staff</div>
            </a>
        </li>

        <li class="menu-item {{ request()->routeIs('admin.council.*') ? 'active' : '' }}">
            <a href="{{ route('admin.council.index') }}" class="menu-link">
                <i class="menu-icon tf-icon iconify iconify-lg" data-icon="bx:bx-group"></i>
                <div>Council</div>
            </a>
        </li>

        <li class="menu-item {{ request()->routeIs('admin.settings.*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icon iconify iconify-lg" data-icon="bx:bx-cog"></i>
                <div>Settings</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->routeIs('admin.settings.admins.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.settings.admins.index') }}" class="menu-link">
                        <div>Admins</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.settings.email-templates.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.settings.email-templates.index') }}" class="menu-link">
                        <div>Email Templates</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.settings.site-settings.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.settings.site-settings.index') }}" class="menu-link">
                        <div>Site Settings</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.settings.audit-logs.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.settings.audit-logs.index') }}" class="menu-link">
                        <div>Audit Logs</div>
                    </a>
                </li>
            </ul>
        </li>
    </ul>
</aside>

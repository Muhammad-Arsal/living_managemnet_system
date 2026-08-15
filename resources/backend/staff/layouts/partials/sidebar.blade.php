<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="{{ route('staff.dashboard') }}" class="app-brand-link">
            <span class="app-brand-logo">
                <img src="{{ asset('img/logo-mark.svg') }}" alt="{{ config('app.name') }}">
            </span>
            <span class="app-brand-text demo">
                Living
                <small>Staff Panel</small>
            </span>
        </a>
        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large d-block d-xl-none ms-auto">
            <i class="iconify iconify-lg" data-icon="bx:bx-menu"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        <li class="menu-item {{ request()->routeIs('staff.dashboard') ? 'active' : '' }}">
            <a href="{{ route('staff.dashboard') }}" class="menu-link">
                <i class="menu-icon tf-icon iconify iconify-lg" data-icon="bx:bx-home-circle"></i>
                <div>Dashboard</div>
            </a>
        </li>
        <li class="menu-item {{ request()->routeIs('staff.tickets.*') ? 'active' : '' }}">
            <a href="{{ route('staff.tickets.index') }}" class="menu-link">
                <i class="menu-icon tf-icon iconify iconify-lg" data-icon="bx:bx-headphone"></i>
                <div>Support Tickets</div>
            </a>
        </li>
        <li class="menu-item {{ request()->routeIs('staff.profile.*') ? 'active' : '' }}">
            <a href="{{ route('staff.profile.edit') }}" class="menu-link">
                <i class="menu-icon tf-icon iconify iconify-lg" data-icon="bx:bx-user"></i>
                <div>My Profile</div>
            </a>
        </li>
    </ul>
</aside>

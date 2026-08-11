<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="{{ route('dashboard') }}" class="app-brand-link">
            <img src="{{ asset('img/logo-light.svg') }}" alt="{{ config('app.name') }}" class="app-brand-logo"
                style="max-height: 35px; max-width: 180px; width: auto; object-fit: contain;">
        </a>
        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large d-block d-xl-none ms-auto">
            <i class="iconify iconify-lg" data-icon="bx:bx-menu"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        <li class="menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <a href="{{ route('dashboard') }}" class="menu-link">
                <i class="menu-icon tf-icon iconify iconify-lg" data-icon="bx:bx-home-circle"></i>
                <div>Dashboard</div>
            </a>
        </li>
    </ul>
</aside>

<nav class="layout-navbar container-fluid navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
    id="layout-navbar">
    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-xl-0 d-xl-none me-3">
        <a class="nav-item nav-link me-xl-4 px-0" href="javascript:void(0)">
            <i class="iconify iconify-lg" data-icon="bx:bx-menu"></i>
        </a>
    </div>

    <div class="navbar-nav-right d-flex align-items-center w-100" id="navbar-collapse">
        <div class="d-none d-md-flex align-items-center gap-2 me-auto text-muted">
            <i class="iconify" data-icon="bx:bx-grid-alt" style="font-size: 1.1rem; color: #10b981;"></i>
            <span class="fw-semibold" style="color: #0f172a;">Admin Panel</span>
        </div>
        <ul class="navbar-nav align-items-center ms-auto flex-row">
            @include('backend::partials.notifications-dropdown')
            @php($authUser = Auth::guard('admin')->user())
            <li class="nav-item navbar-dropdown dropdown dropdown-user">
                <a class="nav-link dropdown-toggle hide-arrow d-flex align-items-center gap-2" href="javascript:void(0);" data-bs-toggle="dropdown">
                    <div class="d-none d-sm-flex flex-column text-end">
                        <span class="fw-semibold" style="font-size: 0.875rem; color: #0f172a; line-height: 1.2;">{{ $authUser->name ?? 'Admin' }}</span>
                        <small class="text-muted" style="font-size: 0.72rem;">Signed in</small>
                    </div>
                    <div class="avatar avatar-online">
                        @if ($authUser?->avatar_url)
                            <img src="{{ $authUser->avatar_url }}" alt="Avatar" class="rounded-circle">
                        @else
                            <span class="avatar-initial rounded-circle bg-label-primary">
                                {{ $authUser?->initials ?? 'AD' }}
                            </span>
                        @endif
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="{{ route('admin.profile.edit') }}">
                            <div class="d-flex">
                                <div class="me-3 flex-shrink-0">
                                    <div class="avatar avatar-online">
                                        @if ($authUser?->avatar_url)
                                            <img src="{{ $authUser->avatar_url }}" alt="Avatar" class="rounded-circle">
                                        @else
                                            <span class="avatar-initial rounded-circle bg-label-primary">
                                                {{ $authUser?->initials ?? 'AD' }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <span class="fw-semibold d-block">{{ $authUser->name ?? 'Admin' }}</span>
                                    <small class="text-muted">{{ $authUser->email ?? '' }}</small>
                                </div>
                            </div>
                        </a>
                    </li>
                    <li>
                        <div class="dropdown-divider"></div>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('admin.profile.edit') }}">
                            <i class="iconify me-2" data-icon="bx:bx-user"></i>
                            <span class="align-middle">My Profile</span>
                        </a>
                    </li>
                    <li>
                        <div class="dropdown-divider"></div>
                    </li>
                    <li>
                        <form action="{{ route('admin.logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="dropdown-item">
                                <i class="iconify me-2" data-icon="bx:bx-log-out"></i>
                                <span class="align-middle">Log Out</span>
                            </button>
                        </form>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</nav>

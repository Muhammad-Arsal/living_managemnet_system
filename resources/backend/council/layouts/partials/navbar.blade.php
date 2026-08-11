<nav class="layout-navbar container-fluid navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
    id="layout-navbar">
    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-xl-0 d-xl-none me-3">
        <a class="nav-item nav-link me-xl-4 px-0" href="javascript:void(0)">
            <i class="iconify iconify-lg" data-icon="bx:bx-menu"></i>
        </a>
    </div>

    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
        <ul class="navbar-nav align-items-center ms-auto flex-row">
            @php($authUser = Auth::guard('council')->user())
            <li class="nav-item navbar-dropdown dropdown dropdown-user">
                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                    <div class="avatar avatar-online">
                        @if ($authUser?->avatar_url)
                            <img src="{{ $authUser->avatar_url }}" alt="Avatar" class="rounded-circle">
                        @else
                            <span class="avatar-initial rounded-circle bg-label-primary">
                                {{ $authUser?->initials ?? 'CO' }}
                            </span>
                        @endif
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="{{ route('council.profile.edit') }}">
                            <div class="d-flex">
                                <div class="me-3 flex-shrink-0">
                                    <div class="avatar avatar-online">
                                        @if ($authUser?->avatar_url)
                                            <img src="{{ $authUser->avatar_url }}" alt="Avatar" class="rounded-circle">
                                        @else
                                            <span class="avatar-initial rounded-circle bg-label-primary">
                                                {{ $authUser?->initials ?? 'CO' }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <span class="fw-semibold d-block">{{ $authUser->name ?? 'Council' }}</span>
                                    <small class="text-muted">{{ $authUser->email ?? '' }}</small>
                                </div>
                            </div>
                        </a>
                    </li>
                    <li>
                        <div class="dropdown-divider"></div>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('council.profile.edit') }}">
                            <i class="iconify me-2" data-icon="bx:bx-user"></i>
                            <span class="align-middle">My Profile</span>
                        </a>
                    </li>
                    <li>
                        <div class="dropdown-divider"></div>
                    </li>
                    <li>
                        <form action="{{ route('council.logout') }}" method="POST">
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

<li class="nav-item navbar-dropdown dropdown me-2 me-md-3">
    <a class="nav-link dropdown-toggle hide-arrow lms-notify-toggle" href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="false">
        <span class="lms-notify-bell">
            <i class="iconify" data-icon="bx:bx-bell"></i>
            @if (($portalUnreadCount ?? 0) > 0)
                <span class="lms-notify-badge">{{ $portalUnreadCount > 9 ? '9+' : $portalUnreadCount }}</span>
            @endif
        </span>
    </a>
    <ul class="dropdown-menu dropdown-menu-end lms-notify-menu">
        <li class="lms-notify-menu__header">
            <span>Notifications</span>
            @if (($portalUnreadCount ?? 0) > 0 && ! empty($portalKey))
                <form action="{{ route($portalKey.'.notifications.read-all') }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-link p-0">Mark all read</button>
                </form>
            @endif
        </li>
        @forelse ($portalNotifications ?? [] as $notification)
            <li>
                <a class="dropdown-item lms-notify-item {{ $notification->read_at ? '' : 'lms-notify-item--unread' }}"
                    href="{{ route($portalKey.'.notifications.open', $notification->id) }}">
                    <span class="lms-notify-item__title">{{ $notification->data['title'] ?? 'Ticket update' }}</span>
                    <span class="lms-notify-item__message">{{ $notification->data['message'] ?? '' }}</span>
                    <span class="lms-notify-item__time">{{ $notification->created_at?->diffForHumans() }}</span>
                </a>
            </li>
        @empty
            <li class="lms-notify-empty">No notifications yet.</li>
        @endforelse
    </ul>
</li>

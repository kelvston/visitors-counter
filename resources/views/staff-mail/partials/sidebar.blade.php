<aside class="mail-sidebar">
    <a class="btn btn-primary w-100 mb-3" href="{{ route('staff-mail.create') }}">
        <span aria-hidden="true">＋</span> Compose
    </a>
    <nav class="nav flex-column" aria-label="Mail folders">
        <a class="mail-folder-link {{ ($folder ?? '') === 'inbox' ? 'active' : '' }}"
           href="{{ route('staff-mail.inbox') }}">
            <span>Inbox</span>
            @if ($unreadCount)
                <span class="badge rounded-pill text-bg-primary">{{ $unreadCount > 99 ? '99+' : $unreadCount }}</span>
            @endif
        </a>
        <a class="mail-folder-link {{ ($folder ?? '') === 'sent' ? 'active' : '' }}"
           href="{{ route('staff-mail.sent') }}">Sent</a>
    </nav>
</aside>

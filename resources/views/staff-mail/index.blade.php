@extends('layouts.header')

@section('content')
    <div class="container mt-4 mail-page">
        <div class="mail-workspace">
            @include('staff-mail.partials.sidebar')
            <main class="mail-main">
                <div class="mail-toolbar d-flex flex-wrap justify-content-between align-items-center gap-2 pb-3 mb-3">
                    <div>
                        <h1 class="h3 mail-page-heading mb-1">{{ $folder === 'inbox' ? 'Inbox' : 'Sent mail' }}</h1>
                        <p class="text-muted mb-0">Private messages for staff in your category</p>
                    </div>
                </div>

                @if (session('status'))
                    <div class="alert alert-success">{{ session('status') }}</div>
                @endif

                <div class="mail-list">
                    @forelse ($conversations as $conversation)
                        @php
                            $latestMessage = $conversation->latestMessage;
                            $visibleRecipients = collect($latestMessage?->recipientCopies)
                                ->filter(fn ($copy) => in_array($copy->type, ['to', 'cc'], true));
                            if ($folder === 'sent') {
                                $displayRecipients = collect($latestMessage?->recipientCopies)
                                    ->filter(fn ($copy) => in_array($copy->type, ['to', 'cc', 'bcc'], true))
                                    ->pluck('user.name')
                                    ->filter()
                                    ->unique()
                                    ->implode(', ');
                            } else {
                                $displayRecipients = $latestMessage?->sender_id === null
                                    ? 'Library System'
                                    : ($latestMessage?->sender?->name ?? 'Former staff member');
                            }
                        @endphp
                        <article class="mail-list-item {{ $conversation->unread_count ? 'mail-list-item-unread' : '' }}">
                            <a class="mail-subject-link" href="{{ route('staff-mail.show', $conversation) }}">
                                {{ $conversation->subject }}
                            </a>
                            <div class="mail-list-meta">
                                <span>{{ $folder === 'inbox' ? 'From' : 'To' }}: {{ $displayRecipients ?: 'Former staff member' }}</span>
                                <time>{{ $latestMessage?->created_at?->format('d M Y, H:i') }}</time>
                            </div>
                            <a class="mail-message-preview" href="{{ route('staff-mail.show', $conversation) }}">
                                {{ Str::limit(strip_tags($latestMessage?->safe_body ?? ''), 160) }}
                            </a>
                            <div class="mail-list-footer">
                                @if ($visibleRecipients?->where('type', 'cc')->isNotEmpty())
                                    <span class="mail-label">CC</span>
                                @endif
                                @if ($conversation->unread_count)
                                    <span class="badge rounded-pill text-bg-primary">{{ $conversation->unread_count }} unread</span>
                                @endif
                                @if ($latestMessage?->attachment_path)
                                    @php($previewable = in_array(strtolower(pathinfo($latestMessage->attachment_name ?? '', PATHINFO_EXTENSION)), ['pdf', 'jpg', 'jpeg', 'png'], true))
                                    <span class="mail-attachment-chip">
                                        <span>{{ $latestMessage->attachment_name }}</span>
                                        @if ($previewable)
                                            <a href="{{ route('staff-mail.attachments.preview', $latestMessage) }}"
                                               target="_blank" rel="noopener">Preview</a>
                                        @endif
                                        <a href="{{ route('staff-mail.attachments.download', $latestMessage) }}">Download</a>
                                    </span>
                                @endif
                            </div>
                        </article>
                    @empty
                        <div class="mail-empty-state">
                            <span class="mail-empty-icon" aria-hidden="true">✉</span>
                            <strong>{{ $folder === 'inbox' ? 'Your inbox is empty' : 'No sent messages yet' }}</strong>
                            <span class="text-muted">When you {{ $folder === 'inbox' ? 'receive' : 'send' }} mail, it will appear here.</span>
                        </div>
                    @endforelse
                </div>

                <div class="mt-3">{{ $conversations->links() }}</div>
            </main>
        </div>
    </div>
@endsection

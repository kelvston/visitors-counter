@extends('layouts.header')

@section('content')
    <div class="container mt-4 mail-page">
        <div class="mail-workspace">
            @include('staff-mail.partials.sidebar', ['folder' => null])
            <main class="mail-main">
                <div class="mail-toolbar d-flex flex-wrap justify-content-between align-items-center gap-2 pb-3 mb-3">
                    <div>
                        <h1 class="h3 mail-page-heading mb-1">{{ $conversation->subject }}</h1>
                        <div class="text-muted">Conversation</div>
                    </div>
                    <a href="{{ route('staff-mail.inbox') }}" class="btn btn-outline-secondary">Back to inbox</a>
                </div>

                @if (session('status'))
                    <div class="alert alert-success">{{ session('status') }}</div>
                @endif

                <div class="d-flex flex-column gap-3 mb-4">
                    @foreach ($conversation->messages as $message)
                        @php
                            $toRecipients = $message->recipientCopies->where('type', 'to');
                            $ccRecipients = $message->recipientCopies->where('type', 'cc');
                            $ownBcc = $message->recipientCopies
                                ->where('type', 'bcc')
                                ->filter(fn ($copy) => auth()->id() === $message->sender_id || $copy->user_id === auth()->id());
                            $previewable = in_array(strtolower(pathinfo($message->attachment_name ?? '', PATHINFO_EXTENSION)), ['pdf', 'jpg', 'jpeg', 'png'], true);
                        @endphp
                        <article class="card mail-message-card {{ $message->sender_id === auth()->id() ? 'mail-message-card-outgoing' : 'mail-message-card-incoming' }}">
                            <div class="card-header d-flex justify-content-between gap-3">
                                <div>
                                    <strong>{{ $message->sender_id === null ? 'Library System' : ($message->sender?->name ?? 'Former staff member') }}</strong>
                                    <div class="mail-recipient-details">
                                        <span>To: {{ $toRecipients->pluck('user.name')->filter()->implode(', ') ?: 'Former staff member' }}</span>
                                        @if ($ccRecipients->isNotEmpty())
                                            <span>CC: {{ $ccRecipients->pluck('user.name')->filter()->implode(', ') }}</span>
                                        @endif
                                        @if ($ownBcc->isNotEmpty())
                                            <span>BCC: {{ $message->sender_id === auth()->id()
                                                ? $ownBcc->pluck('user.name')->filter()->implode(', ')
                                                : 'you' }}</span>
                                        @endif
                                    </div>
                                </div>
                                <time class="text-muted text-nowrap">{{ $message->created_at->format('d M Y, H:i') }}</time>
                            </div>
                            <div class="card-body">
                                <div class="mail-message-body">{!! $message->safe_body !!}</div>
                                @if ($message->attachment_path)
                                    <div class="mail-attachment-chip mt-3">
                                        <span>{{ $message->attachment_name }}</span>
                                        @if ($previewable)
                                            <a href="{{ route('staff-mail.attachments.preview', $message) }}"
                                               target="_blank" rel="noopener">Preview</a>
                                        @endif
                                        <a href="{{ route('staff-mail.attachments.download', $message) }}">Download</a>
                                    </div>
                                    @if ($previewable)
                                        <details class="mt-3">
                                            <summary class="text-success-emphasis">Preview attachment</summary>
                                            @if (strtolower(pathinfo($message->attachment_name ?? '', PATHINFO_EXTENSION)) === 'pdf')
                                                <iframe class="mail-preview-frame mt-2"
                                                        title="Preview of {{ $message->attachment_name }}"
                                                        src="{{ route('staff-mail.attachments.preview', $message) }}"></iframe>
                                            @else
                                                <img class="mail-preview-image"
                                                     src="{{ route('staff-mail.attachments.preview', $message) }}"
                                                     alt="Preview of {{ $message->attachment_name }}">
                                            @endif
                                        </details>
                                    @endif
                                @endif
                            </div>
                        </article>
                    @endforeach
                </div>

                @if ($canReply)
                    <form action="{{ route('staff-mail.reply', $conversation) }}" method="POST" enctype="multipart/form-data"
                          class="card card-body mail-compose-card mb-4">
                        @csrf
                        <h2 class="h5">Reply all</h2>
                        <p class="small text-muted">Replies go to the latest message’s To and CC recipients. BCC recipients remain private.</p>
                        <div class="mb-3 mail-reply-editor">
                            <label for="reply-body" class="form-label">Message</label>
                            <textarea id="reply-body" name="body" class="form-control" rows="5" maxlength="50000"
                                      data-mail-editor required>{{ old('body') }}</textarea>
                            @error('body') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        @include('staff-mail.partials.editor', ['editorId' => 'reply-body'])
                        <div class="mb-3">
                            <label for="attachment" class="form-label">Attachment (optional, max 10 MB)</label>
                            <input id="attachment" name="attachment" type="file" class="form-control"
                                   accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.jpg,.jpeg,.png">
                            @error('attachment') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div><button type="submit" class="btn btn-primary">Send reply to all</button></div>
                    </form>
                @else
                    <div class="alert alert-secondary">Replies are unavailable because there are no other active participants.</div>
                @endif
            </main>
        </div>
    </div>
@endsection

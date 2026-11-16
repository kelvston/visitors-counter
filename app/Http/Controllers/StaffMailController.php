<?php

namespace App\Http\Controllers;

use App\Models\MailConversation;
use App\Models\MailMessage;
use App\Models\MailMessageRecipient;
use App\Models\User;
use App\Support\StaffMailBody;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Exists;
use Illuminate\Validation\Rules\File;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StaffMailController extends Controller
{
    public function inbox(Request $request): View
    {
        return $this->mailbox($request, 'inbox');
    }

    public function sent(Request $request): View
    {
        return $this->mailbox($request, 'sent');
    }

    public function create(Request $request): View
    {
        $recipients = $this->availableRecipients($request->user());
        $unreadCount = $request->user()->mailRecipientCopies()->whereNull('read_at')->count();

        return view('staff-mail.create', compact('recipients', 'unreadCount'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validate([
            'to' => ['required', 'array', 'min:1', 'max:50'],
            'to.*' => ['required', 'integer', 'distinct', $this->recipientRule($user)],
            'cc' => ['nullable', 'array', 'max:50'],
            'cc.*' => ['required', 'integer', 'distinct', $this->recipientRule($user)],
            'bcc' => ['nullable', 'array', 'max:50'],
            'bcc.*' => ['required', 'integer', 'distinct', $this->recipientRule($user)],
            'subject' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:50000'],
            'attachment' => [
                'nullable',
                File::types(['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'jpg', 'jpeg', 'png'])
                    ->max(10 * 1024),
            ],
        ]);
        $this->ensureDistinctRecipientGroups($validated);
        $body = $this->sanitizeBody($validated['body']);
        $this->ensureNonEmptyBody($body);
        [$attachmentPath, $attachmentName] = $this->storeAttachment($request);

        $conversation = DB::transaction(function () use (
            $user,
            $validated,
            $body,
            $attachmentPath,
            $attachmentName
        ): MailConversation {
            $toIds = array_map('intval', $validated['to']);
            $conversation = MailConversation::create([
                'subject' => $validated['subject'],
                'sender_id' => $user->id,
                'recipient_id' => $toIds[0],
            ]);

            $message = $conversation->messages()->create([
                'sender_id' => $user->id,
                'recipient_id' => $toIds[0],
                'body' => $body,
                'attachment_path' => $attachmentPath,
                'attachment_name' => $attachmentName,
            ]);
            $this->createRecipientCopies($message, $validated);

            return $conversation;
        });

        return redirect()->route('staff-mail.sent')->with('status', 'Message sent.');
    }

    private function availableRecipients(User $user): \Illuminate\Database\Eloquent\Collection
    {
        return User::query()
            ->where('id', '!=', $user->id)
            ->orderBy('name')
            ->get(['id', 'name', 'email']);
    }

    private function recipientRule(User $user): Exists
    {
        return Rule::exists('users', 'id')->where(fn ($query) => $query
            ->where('id', '!=', $user->id)
            ->whereNull('deleted_at'));
    }

    public function show(Request $request, MailConversation $conversation): View
    {
        $user = $request->user();
        $this->authorizeParticipant($conversation, $user->id);

        $conversation->messages()
            ->whereHas('recipientCopies', fn (Builder $query) => $query
                ->where('user_id', $user->id)
                ->whereNull('read_at'))
            ->with('recipientCopies')
            ->get()
            ->each(function (MailMessage $message) use ($user): void {
                $message->recipientCopies()
                    ->where('user_id', $user->id)
                    ->whereNull('read_at')
                    ->update(['read_at' => now()]);
            });

        $conversation->load([
            'sender',
            'recipient',
            'messages' => fn ($query) => $query->with(['sender', 'recipient', 'recipientCopies.user'])->oldest(),
        ]);

        $canReply = $conversation->messages
            ->flatMap(fn (MailMessage $message) => $message->recipientCopies->pluck('user'))
            ->push($conversation->sender)
            ->filter()
            ->unique('id')
            ->contains(fn (User $participant) => $participant->id !== $user->id
                && ! $participant->trashed());
        $unreadCount = $user->mailRecipientCopies()->whereNull('read_at')->count();

        return view('staff-mail.show', compact('conversation', 'canReply', 'unreadCount'));
    }

    public function reply(Request $request, MailConversation $conversation): RedirectResponse
    {
        $user = $request->user();
        $this->authorizeParticipant($conversation, $user->id);
        $validated = $request->validate([
            'body' => ['required', 'string', 'max:50000'],
            'attachment' => [
                'nullable',
                File::types(['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'jpg', 'jpeg', 'png'])
                    ->max(10 * 1024),
            ],
        ]);

        $latestMessage = $conversation->messages()
            ->with('recipientCopies.user')
            ->latest('id')
            ->firstOrFail();
        $recipientsById = $latestMessage->recipientCopies
            ->whereIn('type', ['to', 'cc'])
            ->filter(fn (MailMessageRecipient $copy) => $copy->user
                && ! $copy->user->trashed())
            ->keyBy('user_id');

        if ($latestMessage->sender_id !== $user->id) {
            $sender = User::withTrashed()->find($latestMessage->sender_id);
            if ($sender && ! $sender->trashed()) {
                $recipientsById->put($sender->id, new MailMessageRecipient([
                    'user_id' => $sender->id,
                    'type' => 'to',
                ]));
            }
        }

        $recipientsById->forget($user->id);
        abort_if($recipientsById->isEmpty(), 403, 'There are no active recipients for this reply.');

        $body = $this->sanitizeBody($validated['body']);
        $this->ensureNonEmptyBody($body);
        [$attachmentPath, $attachmentName] = $this->storeAttachment($request);

        $toIds = $recipientsById
            ->filter(fn (MailMessageRecipient $copy) => $copy->type === 'to')
            ->keys()
            ->values()
            ->all();
        $ccIds = $recipientsById
            ->filter(fn (MailMessageRecipient $copy) => $copy->type === 'cc')
            ->keys()
            ->values()
            ->all();

        if ($toIds === []) {
            $toIds[] = $recipientsById->keys()->first();
            $ccIds = array_values(array_diff($ccIds, $toIds));
        }

        DB::transaction(function () use (
            $conversation,
            $user,
            $toIds,
            $ccIds,
            $body,
            $attachmentPath,
            $attachmentName
        ): void {
            $message = $conversation->messages()->create([
                'sender_id' => $user->id,
                'recipient_id' => $toIds[0],
                'body' => $body,
                'attachment_path' => $attachmentPath,
                'attachment_name' => $attachmentName,
            ]);
            $message->recipientCopies()->createMany([
                ...array_map(fn (int $id) => ['user_id' => $id, 'type' => 'to'], $toIds),
                ...array_map(fn (int $id) => ['user_id' => $id, 'type' => 'cc'], $ccIds),
            ]);
        });

        return redirect()->route('staff-mail.show', $conversation)->with('status', 'Reply sent.');
    }

    public function downloadAttachment(Request $request, MailMessage $message): StreamedResponse
    {
        $this->authorizeParticipant($message->conversation, $request->user()->id);
        abort_unless(
            $message->attachment_path && Storage::disk('local')->exists($message->attachment_path),
            404
        );

        return Storage::disk('local')->download(
            $message->attachment_path,
            basename($message->attachment_name ?: 'attachment')
        );
    }

    public function previewAttachment(Request $request, MailMessage $message): BinaryFileResponse
    {
        $this->authorizeParticipant($message->conversation, $request->user()->id);
        abort_unless(
            $message->attachment_path && Storage::disk('local')->exists($message->attachment_path),
            404
        );

        $mimeType = match (strtolower(pathinfo($message->attachment_name ?? '', PATHINFO_EXTENSION))) {
            'pdf' => 'application/pdf',
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            default => null,
        };
        abort_unless($mimeType, 404, 'This attachment type cannot be previewed in the browser.');

        return response()->file(Storage::disk('local')->path($message->attachment_path), [
            'Content-Type' => $mimeType,
            'X-Content-Type-Options' => 'nosniff',
            'Content-Disposition' => HeaderUtils::makeDisposition(
                'inline',
                basename($message->attachment_name ?: 'preview')
            ),
        ]);
    }

    private function mailbox(Request $request, string $folder): View
    {
        $userId = $request->user()->id;

        $conversations = MailConversation::query()
            ->whereHas('messages', fn (Builder $query) => $folder === 'inbox'
                ? $query->whereHas('recipientCopies', fn (Builder $copies) => $copies->where('user_id', $userId))
                : $query->where('sender_id', $userId))
            ->with(['sender', 'recipient', 'latestMessage.sender', 'latestMessage.recipientCopies.user'])
            ->withCount([
                'messages as unread_count' => fn (Builder $query) => $query
                    ->whereHas('recipientCopies', fn (Builder $copies) => $copies
                        ->where('user_id', $userId)
                        ->whereNull('read_at')),
            ])
            ->orderByDesc('updated_at')
            ->paginate(15);

        $unreadCount = $request->user()->mailRecipientCopies()->whereNull('read_at')->count();

        return view('staff-mail.index', compact('conversations', 'folder', 'unreadCount'));
    }

    private function authorizeParticipant(MailConversation $conversation, int $userId): void
    {
        abort_unless(
            $conversation->sender_id === $userId
                || $conversation->messages()
                    ->whereHas('recipientCopies', fn (Builder $query) => $query->where('user_id', $userId))
                    ->exists(),
            404
        );
    }

    private function ensureDistinctRecipientGroups(array $validated): void
    {
        $recipientIds = array_merge($validated['to'], $validated['cc'] ?? [], $validated['bcc'] ?? []);
        if (count($recipientIds) !== count(array_unique($recipientIds))) {
            throw ValidationException::withMessages([
                'cc' => 'A staff member can only appear once in To, CC, or BCC.',
            ]);
        }

        if (count($recipientIds) > 50) {
            throw ValidationException::withMessages([
                'to' => 'A message can have no more than 50 recipients.',
            ]);
        }
    }

    private function createRecipientCopies(MailMessage $message, array $validated): void
    {
        $copies = [];
        foreach (['to', 'cc', 'bcc'] as $type) {
            foreach ($validated[$type] ?? [] as $recipientId) {
                $copies[] = ['user_id' => (int) $recipientId, 'type' => $type];
            }
        }

        $message->recipientCopies()->createMany($copies);
    }

    private function sanitizeBody(string $body): string
    {
        return app(StaffMailBody::class)->sanitize($body);
    }

    private function ensureNonEmptyBody(string $body): void
    {
        $plainText = html_entity_decode(strip_tags($body), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        if (trim(str_replace("\u{00A0}", ' ', $plainText)) === '') {
            throw ValidationException::withMessages(['body' => 'The message must contain some text.']);
        }
    }

    private function storeAttachment(Request $request): array
    {
        if (! $request->hasFile('attachment')) {
            return [null, null];
        }

        $file = $request->file('attachment');
        $path = $file->store('staff-mail/attachments', 'local');
        if (! $path) {
            throw new \RuntimeException('Unable to store the mail attachment.');
        }

        return [$path, mb_substr(basename($file->getClientOriginalName()), 0, 255)];
    }
}

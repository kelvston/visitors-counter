<?php

namespace App\Models;

use App\Support\StaffMailBody;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MailMessage extends Model
{
    protected $touches = ['conversation'];

    protected $fillable = [
        'conversation_id',
        'sender_id',
        'recipient_id',
        'body',
        'attachment_path',
        'attachment_name',
        'read_at',
    ];

    protected function casts(): array
    {
        return [
            'read_at' => 'datetime',
        ];
    }

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(MailConversation::class, 'conversation_id');
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id')->withTrashed();
    }

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recipient_id')->withTrashed();
    }

    public function recipientCopies(): HasMany
    {
        return $this->hasMany(MailMessageRecipient::class, 'mail_message_id');
    }

    public function getSafeBodyAttribute(): string
    {
        return app(StaffMailBody::class)->sanitize($this->body);
    }
}

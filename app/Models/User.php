<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'staff_category',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'can_manage_academic_minutes' => 'boolean',
            'can_manage_administrative_minutes' => 'boolean',
            'can_manage_proposals' => 'boolean',
            'can_manage_calendar' => 'boolean',
            'can_train_chatbot' => 'boolean',
            'can_access_settings_navigation' => 'boolean',
        ];
    }

    public function isAdmin(): bool
    {
        return (bool) $this->is_admin;
    }

    public function isAcademicStaff(): bool
    {
        return $this->staff_category === 'academic' && ! $this->isAdmin();
    }

    public function canManageMinutesFor(string $section): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        return match ($section) {
            'academic' => (bool) $this->can_manage_academic_minutes,
            'administrative' => (bool) $this->can_manage_administrative_minutes,
            default => false,
        };
    }

    public function canManageProposals(): bool
    {
        return $this->isAdmin() || (bool) $this->can_manage_proposals;
    }

    public function canManageCalendar(): bool
    {
        return $this->isAdmin() || (bool) $this->can_manage_calendar;
    }

    public function canTrainChatbot(): bool
    {
        return $this->isAdmin() || (bool) $this->can_train_chatbot;
    }

    public function canAccessSettingsNavigation(): bool
    {
        return $this->isAdmin() || (bool) $this->can_access_settings_navigation;
    }

    public function receivedMailMessages(): HasMany
    {
        return $this->hasMany(MailMessage::class, 'recipient_id');
    }

    public function mailRecipientCopies(): HasMany
    {
        return $this->hasMany(MailMessageRecipient::class);
    }
}

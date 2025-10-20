<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'avatar',
        'security_question',
        'security_answer',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'security_answer', // 🔒 Never expose security answer
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get available security questions.
     */
    public static function getSecurityQuestions(): array
    {
        return [
            'What was your first pet\'s name?',
            'What is your mother\'s maiden name?',
            'What was the name of your first school?',
            'What is your favorite book?',
            'What city were you born in?',
            'What is your favorite color?',
            'What was your childhood nickname?',
            'What is your father\'s middle name?',
        ];
    }

    /**
     * Verify the provided security answer.
     */
    public function verifySecurityAnswer(string $answer): bool
    {
        return Hash::check($answer, $this->security_answer);
    }

    /**
     * Automatically hash the security answer when set.
     */
    public function setSecurityAnswerAttribute(string $value): void
    {
        $this->attributes['security_answer'] = Hash::make($value);
    }

    // ======================
    // ROLE CHECK METHODS
    // ======================

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isAccountant(): bool
    {
        return $this->role === 'accountant';
    }

    public function isEmployee(): bool
    {
        return $this->role === 'employee';
    }

    // ======================
    // RELATIONSHIPS
    // ======================

    public function requisitions()
    {
        return $this->hasMany(Requisition::class);
    }

    // ======================
    // AVATAR URL ACCESSORS
    // ======================

    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return asset('storage/avatars/' . $this->avatar);
        }

        $name = urlencode($this->name);
        return "https://ui-avatars.com/api/?name={$name}&color=7F9CF5&background=EBF4FF&size=128";
    }

    public function getAvatarSmallUrlAttribute(): string
    {
        if ($this->avatar) {
            return asset('storage/avatars/' . $this->avatar);
        }

        $name = urlencode($this->name);
        return "https://ui-avatars.com/api/?name={$name}&size=32"; // ✅ FIXED: Removed extra spaces
    }
}

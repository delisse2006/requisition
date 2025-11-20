<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'data',
        'read',
        'read_at',
        'sent_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'data' => 'array',
        'read' => 'boolean',
        'read_at' => 'datetime',
        'sent_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    // ======================
    // RELATIONSHIPS
    // ======================

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ======================
    // SCOPES
    // ======================

    public function scopeUnread($query)
    {
        return $query->where('read', false);
    }

    public function scopeRead($query)
    {
        return $query->where('read', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // ======================
    // METHODS
    // ======================

    public function markAsRead()
    {
        $this->update(['read' => true, 'read_at' => now()]);
    }

    public function markAsUnread()
    {
        $this->update(['read' => false, 'read_at' => null]);
    }

    public function markAsSent()
    {
        $this->update(['sent_at' => now()]);
    }

    /**
     * Create a notification if an identical one hasn't been created recently.
     * This helps avoid duplicate notifications when both controllers and observers run.
     *
     * @param array $attributes
     * @param int $ttlMinutes
     * @return self|null
     */
    public static function createUnique(array $attributes, int $ttlMinutes = 1)
    {
        $userId = $attributes['user_id'] ?? null;
        $title = $attributes['title'] ?? null;

        if ($userId && $title) {
            $cutoff = now()->subMinutes($ttlMinutes);
            $exists = self::where('user_id', $userId)
                ->where('title', $title)
                ->where('created_at', '>=', $cutoff)
                ->exists();
            if ($exists) {
                return null;
            }
        }

        return self::create($attributes);
    }

    // ======================
    // ATTRIBUTE ACCESSORS
    // ======================

    public function getCreatedAtHumanAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    public function getIsUnreadAttribute()
    {
        return !$this->read;
    }

    public function getIconAttribute()
    {
        $iconMap = [
            'requisition_submitted' => 'fas fa-box',
            'status_updated' => 'fas fa-sync-alt',
            'receipt_confirmed' => 'fas fa-check-circle',
            'requisition_approved' => 'fas fa-thumbs-up',
            'payment_completed' => 'fas fa-money-bill-wave',
            'user_created' => 'fas fa-user-plus',
            'user_updated' => 'fas fa-user-edit',
            'user_deleted' => 'fas fa-user-minus',
            'system_alert' => 'fas fa-exclamation-triangle',
        ];
        return $iconMap[$this->type] ?? 'fas fa-bell';
    }

    public function getBadgeColorAttribute()
    {
        $colorMap = [
            'requisition_submitted' => 'primary',
            'status_updated' => 'info',
            'receipt_confirmed' => 'success',
            'payment_completed' => 'success',
            'user_created' => 'success',
            'user_updated' => 'warning',
            'user_deleted' => 'danger',
            'system_alert' => 'danger',
        ];
        return $colorMap[$this->type] ?? 'secondary';
    }
}
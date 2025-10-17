<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Requisition extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'requisition_no',
        'item_name',
        'description',
        'quantity',
        'urgency',
        'status',
        'receipt_path',
        'notes',
        'received_confirmed',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'received_confirmed' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * The user who submitted the requisition.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to get requisitions by status.
     */
    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Check if the requisition is in a valid state to be updated to the next status.
     */
    public function canTransitionTo(string $nextStatus): bool
    {
        $transitions = [
            'pending' => ['bought'],
            'bought'  => ['done'],
            'done'    => ['paid'],
            'paid'    => [],
        ];

        return in_array($nextStatus, $transitions[$this->status] ?? []);
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Requisition extends Model
{
    use HasFactory;

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
        'received_confirmed'
    ];

    protected $casts = [
        'received_confirmed' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
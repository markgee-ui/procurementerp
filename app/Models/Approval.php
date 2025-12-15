<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Approval extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_requisition_id',
        'user_id',
        'stage',
        'status',
        'notes',
    ];

    /**
     * Get the Purchase Requisition that owns the approval record.
     */
    public function requisition(): BelongsTo
    {
        return $this->belongsTo(PurchaseRequisition::class, 'purchase_requisition_id');
    }

    /**
     * Get the User who performed the approval action.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
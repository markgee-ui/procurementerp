<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceOrderItem extends Model
{
    protected $fillable = [
        'service_order_id',
        'description',
        'unit_price',
        'discount',
        'line_total'
    ];

    /**
     * Relationship back to the parent Service Order
     */
    public function serviceOrder(): BelongsTo
    {
        return $this->belongsTo(ServiceOrder::class);
    }

    /**
     * Relationship to the actual Service/Product
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}

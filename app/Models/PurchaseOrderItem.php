<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderItem extends Model
{
    use HasFactory;
    
    // Define the table name if it's not the plural of the model name (purchase_order_items)
    // protected $table = 'purchase_order_items';
    
    protected $fillable = [
        'purchase_order_id',
        'product_id', // Links to the specific product in your products table
        'quantity',
        'unit_price', // Price at the time of order creation
        'discount',   // Discount percentage applied
        'line_total', // Calculated total for this line
    ];

    /**
     * Get the purchase order this item belongs to.
     */
    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    /**
     * Get the product details.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

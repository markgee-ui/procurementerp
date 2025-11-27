<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    use HasFactory;
    
    // Define the table name if it's not the plural of the model name (purchase_orders)
    // protected $table = 'purchase_orders';

    protected $fillable = [
        'supplier_id',
        'order_number', // A unique number, often generated
        'order_date',
        'required_by_date',
        'total_amount', // The grand total calculated in the controller
        'status', // e.g., 'Draft', 'Pending Approval', 'Issued', 'Completed'
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'order_date' => 'date',         // Cast as a date object
        'required_by_date' => 'date',   // Cast as a date object (if it exists)
        'total_amount' => 'decimal:2', // Good practice for financial fields
    ];

    /**
     * Get the supplier that issued the purchase order.
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Get the line items associated with the purchase order.
     */
    public function items()
    {
        // Assuming you create a PurchaseOrderItem model
        return $this->hasMany(PurchaseOrderItem::class);
    }
}
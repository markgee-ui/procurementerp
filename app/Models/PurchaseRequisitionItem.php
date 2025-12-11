<?php

// app/Models/PurchaseRequisitionItem.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseRequisitionItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_requisition_id',
        'boq_material_id',
        'boq_activity_id', // Renamed to match the migration
        'item_name',
        'unit',
        'qty_requested',
        'unit_cost',
        'cost_estimate',
    ];

    public function requisition(): BelongsTo
    {
        return $this->belongsTo(PurchaseRequisition::class, 'purchase_requisition_id');
    }

    public function boqMaterial(): BelongsTo
    {
        return $this->belongsTo(BoqMaterial::class, 'boq_material_id');
    }
    
    // Updated relationship name
    public function boqActivity(): BelongsTo 
    {
        return $this->belongsTo(BoqActivity::class, 'boq_activity_id');
    }
}
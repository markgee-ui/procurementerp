<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoqMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'boq_activity_id',
        'item',
        'specs',
        'unit',
        'qty',
        'rate',
        'remarks',
    ];

    // --- Relationships ---

    /**
     * The BOQ Activity this material is associated with.
     */
    public function activity()
    {
        return $this->belongsTo(BoqActivity::class, 'boq_activity_id');
    }

    /**
     * All supplier-specific products that fulfill this internal material specification.
     * This requires a 'boq_material_id' foreign key on the 'products' table.
     */
    public function products()
    {
        return $this->hasMany(Product::class, 'boq_material_id');
    }

    /**
     * Get all suppliers who stock this material (via the Products relationship).
     * This uses the Has Many Through relationship: BoqMaterial -> Product -> Supplier.
     * This is the relationship used for eager loading in the Procurement Controller: 
     * $requisition->load('items.boqMaterial.suppliers')
     */
    public function suppliers()
{
    // This assumes: 
    // 1. The 'products' table has 'boq_material_id' and 'supplier_id'
    // 2. The local key is 'id' (BoqMaterial's ID)
    // 3. The related model is App\Models\Supplier
    return $this->belongsToMany(
        Supplier::class, 
        'products',           // Intermediate table name is 'products'
        'boq_material_id',    // Foreign key on products table linking back to BoqMaterial
        'supplier_id'         // Foreign key on products table linking to Supplier
    )->distinct(); // Ensure only unique suppliers are returned
}
    
}
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
        // Path: BoqMaterial -> Product -> Supplier
        return $this->hasManyThrough(
            Supplier::class,    // The final model we want
            Product::class,     // The intermediate model
            'boq_material_id',  // Foreign key on the intermediate (Product) table
            'id',               // Key on the final (Supplier) model
            'id',               // Local key on the BoqMaterial model
            'supplier_id'       // Key on the intermediate (Product) model that links to Supplier
        );
    }
}
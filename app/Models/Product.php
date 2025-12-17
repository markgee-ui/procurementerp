<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'supplier_id', 
        'boq_material_id', // <-- NEW: Assuming you add this FK to the products table
        'item', 
        'description', // Acts as the Product Code / Specific Description
        'unit_price', 
        'unit'
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
    
    /**
     * Link back to the internal material definition this product fulfills.
     */
    public function boqMaterial()
    {
        return $this->belongsTo(BoqMaterial::class, 'boq_material_id');
    }
}
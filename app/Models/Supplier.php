<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name', 
        'location', 
        'address', 
        'contact',
        'kra_pin', 
        'sales_person_contact', 
        'shop_photo_path', 
        'account_number', 
        'bank_name', 
        'paybill_number', 
        'till_number',
    ];

    /**
     * Get the products associated with the supplier.
     * A Supplier has many specific Product offerings.
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }
    
    /**
     * Get the BOQ materials this supplier can provide.
     * This is a Many-to-Many relationship defined through the 'products' table.
     * * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function boqMaterials()
    {
        return $this->belongsToMany(
            BoqMaterial::class, 
            'products',           // Intermediate table name
            'supplier_id',        // Foreign key on intermediate table linking back to Supplier
            'boq_material_id'     // Foreign key on intermediate table linking to BoqMaterial
        )->distinct(); // Ensures we get a list of unique materials, not one entry per product.
    }
}
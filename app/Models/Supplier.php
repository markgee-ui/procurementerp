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
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
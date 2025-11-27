<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'supplier_id', 'item', 'description', 'unit_price', 'unit'
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}

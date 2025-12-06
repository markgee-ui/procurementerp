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
        'rate', // 💡 NEW FIELD
        'remarks',
    ];

    public function activity()
    {
        return $this->belongsTo(BoqActivity::class, 'boq_activity_id');
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoqActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'boq_id',
        'name',
        'budget',
    ];

    public function boq()
    {
        return $this->belongsTo(Boq::class,'boq_id');
    }

    public function materials()
    {
        return $this->hasMany(BoqMaterial::class,'boq_activity_id');
    }
}
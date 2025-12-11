<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Boq extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_name',
        'project_budget',
        'user_id',
    ];

    public function activities()
    {
        return $this->hasMany(BoqActivity::class,'boq_id');
    }
}
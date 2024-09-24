<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Entity extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'type',
        'measurement_type',
        'measurement_amount',
        'price',
        'config',
        'user_id'
    ];
    
    protected $visible = [
        'id',
        'title',
        'type',
        'measurement_type',
        'measurement_amount',
        'price',
        'config',
        'ingredients',
        'user_id'
    ];

    public function ingredients()
    {
        return $this->hasMany(EntityMap::class, 'parent_id')->with('child');
    }    

    public function parent()
    {
        return $this->hasOne(EntityMap::class, 'child_id');
    }
}

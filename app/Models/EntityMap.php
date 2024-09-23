<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EntityMap extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_id',
        'child_id',
        'measurement_type',
        'measurement_amount',
    ];
    
    protected $visible = [
        'id',
        'parent_id',
        'child_id',
        'measurement_type',
        'measurement_amount',
        'child',
    ];

    public function parent()
    {
        return $this->belongsTo(Entity::class, 'parent_id');
    }

    public function child()
    {
        return $this->belongsTo(Entity::class, 'child_id');
    }    
}

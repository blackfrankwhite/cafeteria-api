<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseRecord extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'entity_id',
        'purchase_id',
        'amount',
        'price',
    ];
}

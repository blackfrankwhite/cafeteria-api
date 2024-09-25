<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'entity_id',
        'purchase_id',
        'amount',
        'price',
    ];
}

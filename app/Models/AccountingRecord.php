<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountingRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'accounting_id',
        'entity_id',
        'amount',
        'price',
    ];
}

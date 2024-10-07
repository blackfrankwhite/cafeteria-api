<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountingRecord extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'accounting_id',
        'entity_id',
        'amount',
        'price',
    ];
}

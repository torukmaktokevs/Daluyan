<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'tenant_user_id',
        'apartment_id',
        'amount',
        'reference',
        'method',
        'status',
    ];
}

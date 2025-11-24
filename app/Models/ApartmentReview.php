<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApartmentReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'apartment_id',
        'tenant_user_id',
        'rating',
        'comment',
    ];
}

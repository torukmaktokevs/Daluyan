<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApartmentApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'apartment_id',
        'host_user_id',
        'tenant_user_id',
        'visit_date',
        'movein_date',
        'message',
        'status',
        'total_price',
    ];

    protected $casts = [
        'visit_date' => 'date',
        'movein_date' => 'date',
    ];

    public function apartment()
    {
        return $this->belongsTo(Apartment::class);
    }

    public function host()
    {
        return $this->belongsTo(User::class, 'host_user_id');
    }

    public function tenant()
    {
        return $this->belongsTo(User::class, 'tenant_user_id');
    }
}

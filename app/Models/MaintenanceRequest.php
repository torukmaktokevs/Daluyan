<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'apartment_id', 'tenant_user_id', 'host_user_id', 'title', 'description', 'status', 'priority'
    ];

    public function apartment()
    {
        return $this->belongsTo(Apartment::class);
    }

    public function tenant()
    {
        return $this->belongsTo(User::class, 'tenant_user_id');
    }

    public function host()
    {
        return $this->belongsTo(User::class, 'host_user_id');
    }
}

<?php
// app/Models/Apartment.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Apartment extends Model
{
    use HasFactory;

    protected $fillable = [
        'host_user_id', 'title', 'address', 'description', 'price',
        'bedrooms', 'bathrooms', 'area', 'amenities', 'status'
    ];

    protected $casts = [
        'amenities' => 'array',
    ];

    // ADD THIS METHOD:
    public function files()
    {
        return $this->morphMany(File::class, 'fileable');
    }

    public function host()
    {
        return $this->belongsTo(User::class, 'host_user_id');
    }
}
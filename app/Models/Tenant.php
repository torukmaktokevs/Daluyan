<?php
// app/Models/Tenant.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    use HasFactory;

    // ... existing code ...

    // ADD THIS METHOD:
    public function files()
    {
        return $this->morphMany(File::class, 'fileable');
    }
}
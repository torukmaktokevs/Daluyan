<?php
// app/Models/DocumentType.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentType extends Model
{
    use HasFactory;

    // These are the fields that can be mass-assigned (filled from forms)
    protected $fillable = [
        'name', 'description', 'is_required', 'allowed_extensions', 'max_size'
    ];

    // This automatically converts data types
    protected $casts = [
        'allowed_extensions' => 'array', // Converts JSON to PHP array
        'is_required' => 'boolean',      // Converts 0/1 to true/false
        'max_size' => 'integer'          // Ensures it's an integer
    ];

    // RELATIONSHIP: One DocumentType can have many Files
    public function files()
    {
        return $this->hasMany(File::class);
    }
}
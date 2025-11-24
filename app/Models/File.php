<?php
// app/Models/File.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class File extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'original_name', 'path', 'extension', 'size',
        'mime_type', 'document_type_id', 'fileable_id', 'fileable_type',
        'uploaded_by', 'description', 'is_public', 'expires_at'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_public' => 'boolean',
        'size' => 'decimal:2'
    ];

    // POLYMORPHIC RELATIONSHIP: File can belong to Apartment, Tenant, or Lease
    public function fileable()
    {
        return $this->morphTo();
    }

    public function documentType()
    {
        return $this->belongsTo(DocumentType::class);
    }

    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function shares()
    {
        return $this->hasMany(FileShare::class);
    }

    // CUSTOM ATTRIBUTES (these don't exist in database)
    public function getFullPathAttribute()
    {
        return Storage::disk('local')->path($this->path);
    }

    public function getUrlAttribute()
    {
        return route('files.download', $this->id);
    }

    public function getFormattedSizeAttribute()
    {
        $size = $this->size;
        $units = ['KB', 'MB', 'GB'];
        
        for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }
        
        return round($size, 2) . ' ' . $units[$i];
    }

    // HELPER METHODS
    public function isExpired()
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function isImage()
    {
        return str_starts_with($this->mime_type, 'image/');
    }

    public function isPdf()
    {
        return $this->mime_type === 'application/pdf';
    }
}
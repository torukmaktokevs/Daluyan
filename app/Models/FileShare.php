<?php
// app/Models/FileShare.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class FileShare extends Model
{
    use HasFactory;

    protected $fillable = [
        'file_id', 'token', 'password', 'expires_at', 'max_downloads', 'is_active'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_active' => 'boolean'
    ];

    // MODEL EVENT: Runs when creating a new FileShare
    public static function boot()
    {
        parent::boot();

        static::creating(function ($fileShare) {
            $fileShare->token = Str::random(32);
        });
    }

    public function file()
    {
        return $this->belongsTo(File::class);
    }

    public function isExpired()
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function canBeDownloaded()
    {
        if (!$this->is_active) return false;
        if ($this->isExpired()) return false;
        if ($this->max_downloads && $this->download_count >= $this->max_downloads) return false;
        
        return true;
    }

    public function incrementDownloadCount()
    {
        $this->download_count++;
        $this->save();
    }
}
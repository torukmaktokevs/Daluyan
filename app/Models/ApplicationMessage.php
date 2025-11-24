<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApplicationMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'from_user_id',
        'to_user_id',
        'message',
        'attachment',
    ];

    public function application()
    {
        return $this->belongsTo(ApartmentApplication::class, 'application_id');
    }

    public function fromUser()
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function toUser()
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }
}

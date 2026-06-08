<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    protected $fillable = [
        'consultation_id', 'sender_type', 'sender_id', 'body', 'attachment_path', 'read_at',
    ];

    protected $casts = ['read_at' => 'datetime'];

    public function consultation(): BelongsTo
    {
        return $this->belongsTo(Consultation::class);
    }
}

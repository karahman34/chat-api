<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Message extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'conversation_id',
        'message',
        'file',
        'sender',
        'created_at',
        'updated_at',
    ];

    /**
     * Get the conversations.
     *
     * @return  BelongsTo
     */
    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    /**
     * Get full file URL.
     *
     * @return  string
     */
    public function getFileUrl()
    {
        return is_null($this->file)
            ? null
            : Storage::url($this->file);
    }
}

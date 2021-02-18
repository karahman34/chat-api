<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'receiver_id',
        'unread_messages',
    ];

    /**
     * Get User Model.
     *
     * @return  BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Get conversation's messages.
     *
     * @return  HasMany
     */
    public function messages()
    {
        return $this->hasMany(Message::class);
    }


    /**
     * Get Receiver Model.
     *
     * @return  BelongsTo
     */
    public function receiver()
    {
        return $this->belongsTo(User::class);
    }
}

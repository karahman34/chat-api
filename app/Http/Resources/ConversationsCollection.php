<?php

namespace App\Http\Resources;

use App\Http\Resources\ConversationResource;
use App\Http\Resources\ReceiverResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ConversationsCollection extends ResourceCollection
{
    public $conversations;
    public $messages;

    public function __construct($conversations, $messages)
    {
        $this->conversations = $conversations;
        $this->messages = $messages;
    }

    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->conversations->map(function ($conversation) {
            $last_message = $this->messages->firstWhere('conversation_id', $conversation->id);
            
            return new ConversationResource($conversation, $last_message);
        });
    }
}

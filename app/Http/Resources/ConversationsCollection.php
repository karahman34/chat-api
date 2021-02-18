<?php

namespace App\Http\Resources;

use App\Models\Conversation;
use App\Models\Member;
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
        return $this->conversations->map(function ($item) {
            return [
                'id' => $item->id,
                'user_id' => $item->user_id,
                'unread_messages' => $item->unread_messages,
                'created_at' => $item->created_at,
                'updated_at' => $item->updated_at,
                'message' => $this->messages->firstWhere('conversation_id', $item->id),
                'receiver' => [
                    'id' => $item->receiver->id,
                    'avatar' => $item->receiver->getAvatarUrl(),
                    'username' => $item->receiver->username,
                ]
            ];
        });
    }
}

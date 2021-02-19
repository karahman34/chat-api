<?php

namespace App\Http\Resources;

use App\Http\Resources\ReceiverResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ConversationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user' => [
                'id' => $this->user->id,
                'avatar' => $this->user->getAvatarUrl(),
                'username' => $this->user->username,
            ],
            'receiver' => new ReceiverResource($this->receiver),
            'unread_messages' => (int) $this->unread_messages,
            'created_at' => $this->created_at,
            'messages' => new MessagesCollection($this->messages)
        ];
    }
}

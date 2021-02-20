<?php

namespace App\Http\Resources;

use App\Http\Resources\MessageResource;
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
        $data = [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'receiver' => new ReceiverResource($this->receiver),
            'unread_messages' => (int) $this->unread_messages,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'messages' => null,
            'last_message' => new MessageResource($this->lastMessage),
        ];

        return $data;
    }
}

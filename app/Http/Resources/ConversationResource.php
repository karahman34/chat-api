<?php

namespace App\Http\Resources;

use App\Http\Resources\ReceiverResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ConversationResource extends JsonResource
{
    private $last_message;

    public function __construct($conversation, $last_message = null)
    {
        parent::__construct($conversation);

        $this->last_message = $last_message;
    }

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
        ];

        if (is_null($this->last_message)) {
            $data['messages'] = new MessagesCollection($this->messages);
        } else {
            $data['messages'] = null;
            $data['last_message'] = $this->last_message;
        }

        return $data;
    }
}

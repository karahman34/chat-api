<?php

namespace App\Http\Resources;

use App\Http\Resources\ConversationResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ConversationsCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->resource->map(function ($conversation) {
            return new ConversationResource($conversation);
        });
    }
}

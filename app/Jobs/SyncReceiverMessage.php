<?php

namespace App\Jobs;

use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncReceiverMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $receiver_id;
    public $sender_id;
    public $message;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($receiver_id, $sender_id, Message $message)
    {
        $this->receiver_id = $receiver_id;
        $this->sender_id = $sender_id;
        $this->message = $message;
    }

    /**
     * Get the conversation Model.
     *
     * @return  Conversation    $conversation
     */
    private function getConversation()
    {
        $conversation = Conversation::where('user_id', $this->receiver_id)
                                        ->where('receiver_id', $this->sender_id)
                                        ->first();

        if (!$conversation) {
            $conversation = Conversation::create([
               'user_id' => $this->receiver_id
           ]);
        }

        return $conversation;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $conversation = $this->getConversation();

        $payload = $this->message->toArray();
        $payload['sender'] = 'receiver';
        $conversation->messages()->create($payload);
        
        $conversation->increment('unread_messages');
    }
}

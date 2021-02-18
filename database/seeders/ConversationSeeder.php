<?php

namespace Database\Seeders;

use App\Jobs\SyncReceiverMessage;
use App\Models\Conversation;
use Illuminate\Database\Seeder;

class ConversationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $xconversation = Conversation::create([
            'user_id' => 1,
            'receiver_id' => 2,
        ]);

        $yconversation = Conversation::create([
            'user_id' => 2,
            'receiver_id' => 1,
        ]);

        $messages = [
            ['sender_id' => 1, 'receiver_id' => 2, 'message' => 'Hallo'],
            ['sender_id' => 2, 'receiver_id' => 1, 'message' => 'Lagi apa ini bro ?'],
            ['sender_id' => 1, 'receiver_id' => 2, 'message' => 'Lagi diem aja nih'],
            ['sender_id' => 2, 'receiver_id' => 1, 'message' => 'Walah sekali2 keluar dong bro...'],
        ];

        foreach ($messages as $payload) {
            $now = now();
            $payload['sender'] = 'me';
            $payload['created_at'] = $now;
            $payload['updated_at'] = $now;

            $payload['sender_id'] === $xconversation->user_id
                ? $message = $xconversation->messages()->create(collect($payload)->only(['file', 'message', 'created_at', 'updated_at'])->toArray())
                : $message = $yconversation->messages()->create(collect($payload)->only(['file', 'message', 'created_at', 'updated_at'])->toArray());

            SyncReceiverMessage::dispatch($payload['receiver_id'], $payload['sender_id'], $message);
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Events\MessageCreated;
use App\Helpers\Transformer;
use App\Http\Resources\ConversationResource;
use App\Http\Resources\ConversationsCollection;
use App\Http\Resources\MessageResource;
use App\Jobs\SyncReceiverMessage;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ConversationController extends Controller
{
    /**
     * Get the last conversations list.
     *
     * @return  JsonResponse
     */
    public function index()
    {
        try {
            $conversations = Conversation::with(['receiver:id,username,avatar,last_online'])
                                            ->where('user_id', Auth::id())
                                            ->whereHas('messages')
                                            ->orderByDesc('updated_at')
                                            ->get();

            $messages = collect([]);
            if ($conversations->count() > 0) {
                $messages = Message::select('conversation_id', 'message', 'file')
                                    ->whereIn('conversation_id', [$conversations->pluck('id')])
                                    ->orderByDesc('created_at')
                                    ->get();
            }

            return (new ConversationsCollection($conversations, $messages))
                    ->additional(Transformer::meta(true, 'Success to get conversations list.'));
        } catch (\Throwable $th) {
            return Transformer::failed('Failed to get conversations list.');
        }
    }

    /**
     * Get conversations.
     *
     * @param   string|int  $receiverId
     *
     * @return  JsonResponse
     */
    public function show($receiverId)
    {
        try {
            $conversation = $this->getConversation($receiverId);
            $conversation->load(['messages' => function ($query) {
                $query->orderBy('created_at');
            }, 'receiver:id,username,avatar,last_online']);

            return Transformer::success('Success to get conversation.', new ConversationResource($conversation));
        } catch (\Throwable $th) {
            return Transformer::failed('Failed to get conversation.');
        }
    }

    /**
     * Get receiver last online.
     *     
     * @param  string|int $receiverId
     * @return JsonResponse
     */
    public function getReceiverLastOnline($receiverId)
    {
        try {
            $conversation = Conversation::select('id', 'receiver_id')
                                            ->with('receiver:id,last_online')
                                            ->where('user_id', Auth::id())
                                            ->where('receiver_id', $receiverId)
                                            ->first();

            if (!$conversation) {
                return Transformer::failed('Conversation not found.', null, 404);
            }

            return Transformer::success('Success to get receiver last online.', $conversation->receiver->last_online);
        } catch (Exception $e) {
            return Transformer::failed('Failed to get receiver last online.', $conversation->receiver->last_online);
        }
    }

    /**
     * Mark Read Messages.
     *
     * @param   Conversation  $conversation
     *
     * @return  JsonResponse
     */
    public function markMessageRead(Conversation $conversation)
    {
        try {
            if ($conversation->user_id !== Auth::id()) {
                return Transformer::failed('Forbidden', null, 403);
            }

            $conversation->update([
                'unread_messages' => 0
            ]);

            return Transformer::success('Success to read messages.');
        } catch (\Throwable $th) {
            return Transformer::failed('Failed to read messages.');
        }
    }

    /**
     * Get the conversation Model.
     *
     * @param   string  $receiver_id
     *
     * @return  Conversation    $conversation
     */
    private function getConversation($receiver_id)
    {
        $conversation = Conversation::where('user_id', Auth::id())
                                        ->where('receiver_id', $receiver_id)
                                        ->first();

        if (!$conversation) {
            $conversation = Conversation::create([
               'user_id' => Auth::id(),
               'receiver_id' => $receiver_id
           ]);
        }

        return $conversation;
    }

    /**
     * Add message.
     *
     * @param   Request  $request
     *
     * @return  JsonResponse
     */
    public function addMessage(Request $request)
    {
        $request->validate([
            'message' => 'nullable|string',
            'file' => 'nullable|file|max:16384',
            'receiver_id' => 'required|string'
        ]);

        if (strlen($request->input('message')) === 0 && !$request->hasFile('file')) {
            return Transformer::failed('The message or file field should be present.', null, 422);
        }

        try {
            $payload = $request->only('message', 'receiver_id');
            $payload['sender'] = 'me';
            $payload['created_at'] = now();
            $payload['updated_at'] = now();

            if ($request->hasFile('file')) {
                $payload['file'] = $request->file('file')->store('conversations');
            }

            $conversation = $this->getConversation($request->input('receiver_id'));

            $message = $conversation->messages()->create($payload);

            SyncReceiverMessage::dispatch($payload['receiver_id'], Auth::id(), $message);

            $sender = $conversation->user()->select('id', 'username', 'avatar')->first()->only(['id', 'username', 'avatar']);

            event(new MessageCreated(
                $sender,
                $payload['receiver_id'],
                new MessageResource($message)
            ));

            return Transformer::success('Success to create new message.', new MessageResource($message), 201);
        } catch (\Throwable $th) {
            return Transformer::failed('Failed to create new message.');
        }
    }

    /**
     * Delete message.
     *
     * @param   Message  $message
     *
     * @return  JsonResponse
     */
    public function deleteMessage(Message $message)
    {
        try {
            if ($message->conversation->user_id !== Auth::id()) {
                return Transformer::failed('Forbidden', null, 403);
            }

            if (!is_null($message->file)) {
                Storage::delete($message->file);
            }

            $message->delete();

            return Transformer::success('Message has been deleted successfully.', $message);
        } catch (\Throwable $th) {
            return Transformer::failed('Failed to delete Message');
        }
    }

    /**
     * Delete conversation.
     *
     * @param   Conversation  $conversation
     *
     * @return  JsonResponse
     */
    public function deleteAllMessage(Conversation $conversation)
    {
        try {
            if ($conversation->user_id !== Auth::id()) {
                return Transformer::failed('Forbidden', null, 403);
            }
            
            foreach ($conversation->messages as $message) {
                if (!is_null($message->file)) {
                    Storage::delete($message->file);
                }

                $message->delete();
            }

            $conversation->update([
                'unread_messages' => 0
            ]);

            return Transformer::success('Success to delete all conversations.');
        } catch (\Throwable $th) {
            return Transformer::failed('Failed to delete conversations.');
        }
    }
}

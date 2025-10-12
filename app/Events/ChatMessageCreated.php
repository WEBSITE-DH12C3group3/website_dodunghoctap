<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;

class ChatMessageCreated implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public int $sessionId;
    public array $message;

    public function __construct(Message $message)
    {
        $this->sessionId = $message->session_id;
        $this->message = [
            'message_id' => $message->message_id,
            'session_id' => $message->session_id,
            'sender_id'  => $message->sender_id,
            'content'    => $message->content,
            'is_read'    => (bool)$message->is_read,
            'created_at' => optional($message->created_at)->toISOString(),
        ];
    }

    public function broadcastOn()
    {
        return new PrivateChannel('chat.session.' . $this->sessionId);
    }

    public function broadcastAs()
    {
        return 'chat.message.created';
    }
}

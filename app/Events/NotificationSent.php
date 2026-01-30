<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificationSent implements ShouldBroadcastNow  
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $id;
    public $type;
    public $title;
    public $body;
    public $url;

    public function __construct($id, $type, $title, $body, $url = null)
    {
        $this->id = $id;
        $this->type = $type;
        $this->title = $title;
        $this->body = $body;
        $this->url = $url;
    }

    public function broadcastOn()
    {
        return new Channel('notifications');  
    }
}

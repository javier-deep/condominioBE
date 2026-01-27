<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Events\MessageSent;
use App\Models\Message;

class ChatController extends Controller
{
    public function sendMessage(Request $request)
    {
        $messageData = [
            'user' => $request->user,
            'message' => $request->message,
        ];  

      
        broadcast(new MessageSent($messageData));

        return response()->json(['success' => true, 'message' => $messageData]);
    }
}
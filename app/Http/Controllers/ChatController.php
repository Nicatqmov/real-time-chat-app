<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Message;

class ChatController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $allUsers = User::where('id', '!=', $user->id)->get();
        $selectedUser = null;
        $messages = collect();
        $unreadCounts = Message::where('receiver_id', $user->id)
            ->where('is_read', false)
            ->selectRaw('user_id, COUNT(*) as unread_count')
            ->groupBy('user_id')
            ->pluck('unread_count', 'user_id');

        if ($request->filled('receiver_id')) {
            $selectedUser = User::where('id', $request->receiver_id)
                ->where('id', '!=', $user->id)
                ->first();

            if ($selectedUser) {
                $messages = Message::where(function ($query) use ($user, $selectedUser) {
                    $query->where('user_id', $user->id)
                        ->where('receiver_id', $selectedUser->id);
                })->orWhere(function ($query) use ($user, $selectedUser) {
                    $query->where('user_id', $selectedUser->id)
                        ->where('receiver_id', $user->id);
                })->orderBy('created_at')->get();

                Message::where('user_id', $selectedUser->id)
                    ->where('receiver_id', $user->id)
                    ->where('is_read', false)
                    ->update(['is_read' => true]);

                $unreadCounts->forget($selectedUser->id);
            }
        }

        return view('chat', compact('messages', 'user', 'allUsers', 'selectedUser', 'unreadCounts'));
    }

    public function send(Request $request)
    {
        $validated = $request->validate([
            'receiver_id' => ['required', 'exists:users,id', Rule::notIn([Auth::id()])],
            'message' => ['required', 'string'],
        ]);

        $message = Message::create([
            'user_id' => Auth::id(),
            'receiver_id' => $validated['receiver_id'],
            'content' => $validated['message'],
            'is_read' => false,
        ]);

        event(new MessageSent($message));

        if ($message) {
            return response()->json([
                'success' => true,
                'message' => 'message sent',
                'data' => [
                    'id' => $message->id,
                    'user_id' => $message->user_id,
                    'receiver_id' => $message->receiver_id,
                    'content' => $message->content,
                ],
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'can not send message',
        ], 500);
    }
}

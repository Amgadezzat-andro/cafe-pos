<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ChatController extends Controller
{
    /**
     * Show chat conversation with a specific user
     */
    public function index(User $user): View
    {
        $currentUser = auth()->user();

        // Fetch messages between current user and selected user
        $messages = Message::where(function ($query) use ($currentUser, $user) {
            $query->where('sender_id', $currentUser->id)
                  ->where('receiver_id', $user->id);
        })->orWhere(function ($query) use ($currentUser, $user) {
            $query->where('sender_id', $user->id)
                  ->where('receiver_id', $currentUser->id);
        })->orderBy('created_at', 'asc')->get();

        // Mark messages from the other user as read
        Message::where('sender_id', $user->id)
               ->where('receiver_id', $currentUser->id)
               ->where('read', false)
               ->update(['read' => true]);

        // Get list of users to chat with (admins for cashiers, cashiers for admins)
        $chatPartners = $this->getChatPartners();

        return view('chat.index', compact('messages', 'user', 'chatPartners'));
    }

    /**
     * Store a new message
     */
    public function store(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'message' => 'required|string|max:5000',
        ]);

        $message = Message::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $user->id,
            'message' => $request->message,
        ]);

        // Broadcast message event for real-time updates
        MessageSent::dispatch($message);

        return redirect()->route('chat.index', $user)->with('success', 'Message sent!');
    }

    /**
     * Get unread message count for current user
     */
    public function unreadCount()
    {
        $count = Message::where('receiver_id', auth()->id())
                       ->where('read', false)
                       ->count();

        return response()->json(['unread' => $count]);
    }

    /**
     * Get unread message count by sender
     */
    public function unreadByPartner()
    {
        $unreadCounts = Message::where('receiver_id', auth()->id())
                              ->where('read', false)
                              ->groupBy('sender_id')
                              ->selectRaw('sender_id, COUNT(*) as count')
                              ->pluck('count', 'sender_id');

        return response()->json($unreadCounts);
    }

    /**
     * Get chat partners list with unread info
     */
    public function partners()
    {
        $partners = $this->getChatPartners();
        $unreadCounts = Message::where('receiver_id', auth()->id())
                              ->where('read', false)
                              ->groupBy('sender_id')
                              ->selectRaw('sender_id, COUNT(*) as count')
                              ->pluck('count', 'sender_id');

        $partnersWithUnread = $partners->map(function ($partner) use ($unreadCounts) {
            $partner->unread_count = $unreadCounts->get($partner->id, 0);
            return $partner;
        });

        return response()->json($partnersWithUnread);
    }

    /**
     * Get chat partners list (admins for cashiers, cashiers for admins)
     */
    private function getChatPartners()
    {
        $currentUser = auth()->user();

        if ($currentUser->hasRole('admin')) {
            // Admin sees all cashiers
            return User::where('role', 'cashier')
                      ->orderBy('name')
                      ->get();
        } else {
            // Cashier sees all admins
            return User::where('role', 'admin')
                      ->orderBy('name')
                      ->get();
        }
    }
}

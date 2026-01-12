# Chat System Architecture Diagram

## System Overview

```
┌─────────────────────────────────────────────────────────────────────┐
│                         CHAT SYSTEM FLOW                            │
└─────────────────────────────────────────────────────────────────────┘

┌──────────────────┐
│   User A         │
│ (Sends Message)  │
└────────┬─────────┘
         │
         ├─→ POST /chat/{user}
         │      └─→ ChatController@store
         │           ├─→ Validate input
         │           ├─→ Create Message record
         │           └─→ MessageSent::dispatch($message)
         │
         ├─→ MessageSent Event
         │      ├─→ Broadcast to:
         │      │    ├─ private-chat-{user_b_id}
         │      │    └─ private-notifications-{user_b_id}
         │      └─→ Data: {id, sender_id, message, created_at}
         │
         └─→ Response: Redirect + Flash Message
              
                    PUSHER CLOUD (Real-time)
                           ↓
         ┌─────────────────────────────────────┐
         │  Channels:                          │
         │  ├─ private-chat-{user_id}         │
         │  └─ private-notifications-{user_id}│
         └──────────────┬──────────────────────┘
                        ↓
         ┌──────────────────────┐
         │   User B             │
         │ (Receives Message)   │
         └──────────┬───────────┘
                    │
                    ├─→ Echo.js Listener
                    │      └─→ channel.bind('message-sent')
                    │           ├─→ loadUnreadCounts()
                    │           ├─→ Update badges
                    │           └─→ Reload chat (if viewing)
                    │
                    ├─→ Browser JavaScript
                    │      ├─→ updateNotificationBadge()
                    │      ├─→ loadUnreadCounts()
                    │      └─→ scrollToBottom()
                    │
                    └─→ Message appears instantly!
```

## Component Architecture

```
┌──────────────────────────────────────────────────────────┐
│                    FRONTEND (Blade)                      │
├──────────────────────────────────────────────────────────┤
│                                                          │
│  ┌─────────────────────────────────────────────────┐   │
│  │  Navigation Bar                                  │   │
│  ├─────────────────────────────────────────────────┤   │
│  │  ┌──────────────────────────────────────────┐   │   │
│  │  │ <x-chat-notification-badge />            │   │   │
│  │  │ ├─ Chat Icon                              │   │   │
│  │  │ ├─ Unread Badge (Updates: 3 sec)          │   │   │
│  │  │ └─ Pusher Listener (Real-time)            │   │   │
│  │  └──────────────────────────────────────────┘   │   │
│  └─────────────────────────────────────────────────┘   │
│                                                          │
│  ┌─────────────────────────────────────────────────┐   │
│  │  Chat View (chat/index.blade.php)              │   │
│  ├─────────────────────────────────────────────────┤   │
│  │  ┌──────────────────┐ ┌────────────────────┐   │   │
│  │  │  Contacts List   │ │  Messages Area     │   │   │
│  │  ├──────────────────┤ ├────────────────────┤   │   │
│  │  │ ┌──────────────┐ │ │ User A [Message]   │   │   │
│  │  │ │ [Badge]User1 │ │ │ User B [Message]   │   │   │
│  │  │ │ [Badge]User2 │ │ │ User A [Message]   │   │   │
│  │  │ │ [Badge]User3 │ │ │                    │   │   │
│  │  │ └──────────────┘ │ │ [Input] [Send]     │   │   │
│  │  │                  │ │ Updates: 2 sec     │   │   │
│  │  └──────────────────┘ │ Real-time Pusher   │   │   │
│  │  Updates: 2 sec       └────────────────────┘   │   │
│  │  Real-time Pusher                              │   │
│  └─────────────────────────────────────────────────┘   │
│                                                          │
│  ┌─────────────────────────────────────────────────┐   │
│  │  JavaScript (chat/index.blade.php)             │   │
│  ├─────────────────────────────────────────────────┤   │
│  │  ├─ Auto-scroll to bottom                      │   │
│  │  ├─ loadUnreadCounts() → fetch unread counts   │   │
│  │  ├─ Pusher: private-chat-{userId}              │   │
│  │  │  ├─ Event: message-sent                      │   │
│  │  │  └─ Action: reload if sender_id matches     │   │
│  │  ├─ Pusher: private-notifications-{userId}     │   │
│  │  │  └─ Event: message-received                  │   │
│  │  └─ Polling: Every 2 seconds (fallback)        │   │
│  └─────────────────────────────────────────────────┘   │
│                                                          │
└──────────────────────────────────────────────────────────┘

┌──────────────────────────────────────────────────────────┐
│                    BACKEND (PHP/Laravel)                │
├──────────────────────────────────────────────────────────┤
│                                                          │
│  ┌─────────────────────────────────────────────────┐   │
│  │  ChatController                                 │   │
│  ├─────────────────────────────────────────────────┤   │
│  │  ├─ index($user)                                │   │
│  │  │  ├─ Get messages with $user                  │   │
│  │  │  ├─ Mark as read                             │   │
│  │  │  └─ Return view('chat.index')                │   │
│  │  │                                              │   │
│  │  ├─ store($request, $user)                      │   │
│  │  │  ├─ Validate message                         │   │
│  │  │  ├─ Save to database                         │   │
│  │  │  ├─ MessageSent::dispatch($message)          │   │
│  │  │  └─ Redirect back                            │   │
│  │  │                                              │   │
│  │  ├─ unreadCount()                               │   │
│  │  │  └─ Count unread for auth user               │   │
│  │  │     → JSON: {unread: number}                 │   │
│  │  │                                              │   │
│  │  ├─ unreadByPartner()                           │   │
│  │  │  └─ Count unread grouped by sender           │   │
│  │  │     → JSON: {user_id: count, ...}            │   │
│  │  │                                              │   │
│  │  └─ partners()                                  │   │
│  │     └─ Get contacts with unread info           │   │
│  │        → JSON: [{id, name, unread_count}, ...]│   │
│  └─────────────────────────────────────────────────┘   │
│                                                          │
│  ┌─────────────────────────────────────────────────┐   │
│  │  Message Model                                  │   │
│  ├─────────────────────────────────────────────────┤   │
│  │  ├─ Attributes:                                 │   │
│  │  │  ├─ sender_id (FK → users)                   │   │
│  │  │  ├─ receiver_id (FK → users)                 │   │
│  │  │  ├─ message (text)                           │   │
│  │  │  ├─ read (boolean)                           │   │
│  │  │  └─ timestamps                               │   │
│  │  │                                              │   │
│  │  └─ Relationships:                              │   │
│  │     ├─ sender() → User                          │   │
│  │     └─ receiver() → User                        │   │
│  └─────────────────────────────────────────────────┘   │
│                                                          │
│  ┌─────────────────────────────────────────────────┐   │
│  │  MessageSent Event (Broadcasting)              │   │
│  ├─────────────────────────────────────────────────┤   │
│  │  ├─ shouldBroadcast = true                      │   │
│  │  ├─ broadcastOn():                              │   │
│  │  │  ├─ private-chat-{receiver_id}               │   │
│  │  │  └─ private-notifications-{receiver_id}      │   │
│  │  │                                              │   │
│  │  └─ broadcastWith():                            │   │
│  │     ├─ id, sender_id, sender_name               │   │
│  │     ├─ receiver_id, message                     │   │
│  │     └─ created_at (formatted)                   │   │
│  └─────────────────────────────────────────────────┘   │
│                                                          │
└──────────────────────────────────────────────────────────┘

┌──────────────────────────────────────────────────────────┐
│                    DATABASE (MySQL)                     │
├──────────────────────────────────────────────────────────┤
│                                                          │
│  Table: messages                                        │
│  ┌──────────────────────────────────────────────────┐  │
│  │ id (PK)        | BIGINT                           │  │
│  │ sender_id (FK) | BIGINT → users.id                │  │
│  │ receiver_id(FK)| BIGINT → users.id                │  │
│  │ message        | LONGTEXT                         │  │
│  │ read           | BOOLEAN (default: false)         │  │
│  │ created_at     | TIMESTAMP                        │  │
│  │ updated_at     | TIMESTAMP                        │  │
│  │                                                  │  │
│  │ Indexes:                                        │  │
│  │ ├─ PRIMARY KEY (id)                             │  │
│  │ ├─ INDEX sender_id                              │  │
│  │ └─ INDEX receiver_id                            │  │
│  └──────────────────────────────────────────────────┘  │
│                                                          │
└──────────────────────────────────────────────────────────┘

┌──────────────────────────────────────────────────────────┐
│                 COMMUNICATION LAYER                      │
├──────────────────────────────────────────────────────────┤
│                                                          │
│  ┌─ Mode 1: Polling (Fallback)                        │
│  │  ├─ Request: GET /chat/unread-count (3 sec)       │
│  │  ├─ Request: GET /chat/unread-by-partner (2 sec)  │
│  │  └─ Request: page reload on chat (implicit)       │
│  │                                                    │
│  └─ Mode 2: WebSockets (Real-time via Pusher)         │
│     ├─ Pusher Channels:                               │
│     │  ├─ private-chat-{userId}                       │
│     │  │  └─ Event: message-sent (from broadcast)    │
│     │  │     Triggers: page reload if match, or        │
│     │  │              updateUnreadCounts()            │
│     │  │                                              │
│     │  └─ private-notifications-{userId}              │
│     │     └─ Event: message-received                  │
│     │        Triggers: updateNotificationBadge()      │
│     │                                                 │
│     ├─ Channel Authorization:                         │
│     │  ├─ routes/channels.php                         │
│     │  ├─ privateChannel('chat-{userId}')             │
│     │  └─ privateChannel('notifications-{userId}')    │
│     │                                                 │
│     └─ Configuration:                                 │
│        ├─ config/broadcasting.php                     │
│        ├─ PUSHER_APP_ID, KEY, SECRET                  │
│        └─ PUSHER_CLUSTER                              │
│                                                        │
└──────────────────────────────────────────────────────────┘
```

## Message Life Cycle

```
Step 1: User Sends Message
┌─────────────────────────┐
│ <form method="POST">    │
│   <input name="message" │
│   <button>Send</button> │
└──────────┬──────────────┘
           │ POST /chat/{user}
           ↓
┌──────────────────────────────┐
│ ChatController@store()       │
│ ├─ Validate input            │
│ ├─ $message->create(...)     │
│ └─ MessageSent::dispatch()   │
└──────────┬───────────────────┘
           │ Event dispatched
           ↓
Step 2: Event Broadcasting
┌────────────────────────────────────────┐
│ MessageSent::dispatch($message)        │
│ ├─ shouldBroadcast = true              │
│ ├─ broadcastOn() returns:              │
│ │  ├─ private-chat-{receiver_id}       │
│ │  └─ private-notifications-{receiver} │
│ └─ Sent to Pusher                      │
└──────────┬─────────────────────────────┘
           │ Broadcast event
           ↓
Step 3: Reception
┌────────────────────────────────────┐
│ Pusher Channels (Cloud)            │
│ ├─ Receives event                  │
│ ├─ Routes to subscriber            │
│ └─ Sends to all listeners          │
└──────────┬───────────────────────────┘
           │ WebSocket message
           ↓
Step 4: JavaScript Listener
┌─────────────────────────────────────┐
│ channel.bind('message-sent', fn) {  │
│   if(data.sender_id === chatUserId) │
│     location.reload()                │
│   else                               │
│     loadUnreadCounts()               │
│ }                                    │
└──────────┬────────────────────────────┘
           │ Update DOM
           ↓
Step 5: UI Update
┌──────────────────────────────────┐
│ ✅ Message appears in chat       │
│ ✅ Badge updates (unread count)  │
│ ✅ Contact indicates has message │
│ ✅ Auto-scroll to bottom         │
└──────────────────────────────────┘
```

## Feature Comparison Matrix

```
                    Polling Mode    WebSocket Mode
                    ─────────────   ──────────────
Network Efficiency  ⭐⭐             ⭐⭐⭐⭐⭐
Latency             2-3 seconds     Real-time
Message Speed       ⭐⭐⭐           ⭐⭐⭐⭐⭐
Server Load         Medium          Low
Setup Complexity    None            Easy (Pusher)
Browser Support     100%            99%+
Cost                Free            Free-$$$
Database Load       Higher          Lower (fewer queries)
Scalability         Limited         Excellent
Production Ready    ✅ Yes          ✅ Yes
```

## Data Flow Diagram

```
┌─────────┐
│ User A  │
└────┬────┘
     │ "Hello" (message text)
     ↓
┌────────────────────────────────────────┐
│ POST /chat/{userB}                     │
│ Content-Type: application/x-www-form   │
│ Body: message=Hello                    │
└────┬───────────────────────────────────┘
     ↓
┌────────────────────────────────────────┐
│ ChatController@store()                 │
│ ├─ Validate (required, max 5000)       │
│ ├─ Message::create([...])              │
│ │  ├─ sender_id = 1                    │
│ │  ├─ receiver_id = 2                  │
│ │  ├─ message = "Hello"                │
│ │  └─ read = false                     │
│ └─ MessageSent::dispatch($msg)         │
└────┬───────────────────────────────────┘
     ↓ [Database]
┌────────────────────────────────────────┐
│ INSERT INTO messages                   │
│ (sender_id, receiver_id, message...)   │
│ VALUES (1, 2, "Hello"...)              │
└────┬───────────────────────────────────┘
     ↓ [Event] MessageSent
┌────────────────────────────────────────┐
│ broadcastOn()                          │
│ ├─ private-chat-2 (for receiver)       │
│ └─ private-notifications-2             │
└────┬───────────────────────────────────┘
     ↓ [Pusher/Redis]
┌────────────────────────────────────────┐
│ Broadcasting Server                    │
│ ├─ Validates channel auth              │
│ ├─ Prepares data                       │
│ └─ Sends to subscribers                │
└────┬───────────────────────────────────┘
     ↓
┌────────┐      ┌──────────────────────┐
│ User B │ ←─── │ WebSocket Connection │
└─┬──────┘      └──────────────────────┘
  │
  ├─→ Pusher.js receives event
  ├─→ Echo listener: 'message-sent'
  ├─→ Callback executes
  ├─→ loadUnreadCounts() OR
  ├─→ location.reload()
  │
  └─→ ✅ UI Updates!
      ├─ New message in thread
      ├─ Badge shows count
      ├─ Contact indicator
      └─ Auto-scrolls to bottom
```

## Routes Map

```
Authentication Required (all):
├─ GET  /chat/{user}
│  └─ View conversation with user
│
├─ POST /chat/{user}
│  └─ Send message to user
│
├─ GET  /chat/unread-count
│  └─ Total unread: { unread: N }
│
├─ GET  /chat/unread-by-partner
│  └─ Per-user unread: { userId: count }
│
└─ GET  /chat/partners
   └─ Contacts list: [{id, name, unread_count}]
```

---

**Architecture Version:** 1.0
**Last Updated:** 2024-01-12
**Status:** ✅ Complete & Deployed

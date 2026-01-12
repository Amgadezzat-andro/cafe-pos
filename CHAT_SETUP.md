# Chat System - Real-Time Enhancement Setup Guide

## Overview
The chat system has been enhanced with real-time capabilities using WebSockets (Pusher) and includes:

✅ **Completed Features:**
1. **Notification Badge** - Shows unread message count in the header navigation
2. **Unread Message Indicators** - Red badges on contact avatars showing unread count
3. **WebSocket Support** - Ready for Pusher/Ably real-time messaging
4. **Polling Fallback** - Graceful degradation if WebSockets not configured
5. **Message Broadcasting** - Events dispatched when messages are sent
6. **Channel Authorization** - Secure private channels with auth

## Architecture

### Backend Components:
- **MessageSent Event** (`app/Events/MessageSent.php`): Broadcasts message creation to receiver
- **ChatController** (`app/Http/Controllers/ChatController.php`): 
  - `index()`: Display chat conversation
  - `store()`: Save and broadcast message
  - `unreadCount()`: Get total unread messages
  - `unreadByPartner()`: Get unread count per contact
  - `partners()`: Get all contacts with unread info
  
- **Broadcasting Config** (`config/broadcasting.php`): Pusher configuration
- **Channel Authorization** (`routes/channels.php`): Private channel auth rules

### Frontend Components:
- **Chat Notification Badge** (`resources/views/components/chat-notification-badge.blade.php`):
  - Displays in header navigation
  - Shows unread message count
  - Updates via polling (3-second intervals)
  - Listens to Pusher events when available
  
- **Chat View** (`resources/views/chat/index.blade.php`):
  - Dual-pane layout (contacts + messages)
  - Unread badges on contact avatars
  - Real-time message updates (with fallback to reload)
  - Polling every 2 seconds for unread counts
  - Pusher event listeners when configured

## Setup Instructions

### Option 1: Pusher (Recommended for Production)

1. **Create a Pusher Account**
   - Go to https://pusher.com
   - Sign up for free tier
   - Create a new app in Channels (WebSockets)
   - Note: app_id, app_key, app_secret, cluster

2. **Update .env**
   ```
   BROADCAST_DRIVER=pusher
   BROADCAST_CONNECTION=pusher
   PUSHER_APP_ID=your_app_id
   PUSHER_APP_KEY=your_app_key
   PUSHER_APP_SECRET=your_app_secret
   PUSHER_HOST=api-mt.pusher.com
   PUSHER_PORT=443
   PUSHER_SCHEME=https
   PUSHER_CLUSTER=mt
   ```

3. **Install Pusher PHP SDK** (already done)
   ```bash
   composer require pusher/pusher-php-server
   ```

4. **Install Pusher JS Client** (already done)
   ```bash
   npm install laravel-echo pusher-js
   ```

5. **Test Setup**
   - Run migrations: `php artisan migrate`
   - Start Laravel: `php artisan serve`
   - Send a message from one user to another
   - Verify real-time delivery on recipient's screen

### Option 2: Redis (For Development with Redis Server)

1. **Ensure Redis is Running**
   ```bash
   redis-server
   ```

2. **Update .env**
   ```
   BROADCAST_DRIVER=redis
   BROADCAST_CONNECTION=redis
   REDIS_HOST=127.0.0.1
   REDIS_PORT=6379
   ```

3. **Install Laravel WebSocket Package**
   ```bash
   composer require beyondcode/laravel-websockets
   php artisan websockets:serve
   ```

### Option 3: Polling Only (Current Default)

If WebSockets are not configured:
- System falls back to polling every 2-3 seconds
- Less efficient but fully functional
- No additional setup required
- Works in development with `BROADCAST_DRIVER=log`

## Database Migration

Run migrations to create the messages table:
```bash
php artisan migrate
```

This creates:
- `messages` table with:
  - sender_id (foreign key to users)
  - receiver_id (foreign key to users)
  - message (text)
  - read (boolean)
  - created_at, updated_at timestamps

## Routes

```php
// All authenticated users (admin + cashier)
GET  /chat/{user}                  → ChatController@index (display chat)
POST /chat/{user}                  → ChatController@store (send message)
GET  /chat/unread-count            → ChatController@unreadCount (total unread)
GET  /chat/unread-by-partner       → ChatController@unreadByPartner (per contact)
GET  /chat/partners                → ChatController@partners (contacts with unread)
```

## Features Explained

### 1. Notification Badge (Header)
- Located in navigation bar next to user dropdown
- Shows total unread message count
- Updates every 3 seconds (polling)
- Real-time update via Pusher when configured
- Click to navigate to first available chat

### 2. Unread Indicators (Contact List)
- Red badges on each contact's avatar
- Shows unread message count for that contact
- Updates every 2 seconds (polling)
- Automatically hidden when unread = 0
- Real-time update via Pusher when configured

### 3. Message Broadcasting
- When user sends message → `MessageSent` event dispatched
- Event broadcasts to receiver's private channel: `private-chat-{userId}`
- Also broadcasts to notifications channel: `private-notifications-{userId}`
- Receiver gets real-time notification update

### 4. Auto-Scroll
- Messages container auto-scrolls to bottom on page load
- Helps user see latest messages immediately

### 5. Message Formatting
- Messages display sender name, content, and timestamp
- Sent messages appear on right (blue)
- Received messages appear on left (white)
- Timestamps show in HH:MM format

## JavaScript Events & Functions

### `updateNotificationBadge()`
Updates the header badge with current unread count
```javascript
updateNotificationBadge(); // Manual call
setInterval(updateNotificationBadge, 3000); // Auto every 3 seconds
```

### `loadUnreadCounts()`
Updates contact list badges with unread counts per partner
```javascript
loadUnreadCounts(); // Manual call
setInterval(loadUnreadCounts, 2000); // Auto every 2 seconds
```

### `scrollToBottom()`
Auto-scrolls messages container to show latest messages
```javascript
scrollToBottom(); // Called on page load
```

### Pusher Event Listeners
```javascript
// Listen for incoming messages on chat channel
channel.bind('message-sent', function(data) {
    // data contains: id, sender_id, sender_name, message, created_at
});

// Listen for notification updates
channel.bind('message-received', function(data) {
    // Update badges and counts
});
```

## Testing Real-Time Features

### With Pusher:
1. Open chat interface in two browser windows (different users)
2. Send message from User A
3. Message appears immediately in User B's window (no page refresh)
4. Badge updates in real-time

### With Polling:
1. Open chat interface in two browser windows
2. Send message from User A
3. Message appears within 2-3 seconds in User B's window
4. Badge updates within 3 seconds

## Troubleshooting

### Badge Not Showing
- Check browser console for JavaScript errors
- Verify route exists: `GET /chat/unread-count`
- Confirm `route('chat.unread-count')` resolves correctly

### Pusher Not Working
- Verify Pusher credentials in .env
- Check `config/broadcasting.php` has correct settings
- Confirm Pusher app is active on Pusher.com
- Look for errors in browser console (Network tab)

### Messages Not Showing
- Verify messages table migration ran: `php artisan migrate:status`
- Check database has messages table with correct columns
- Verify auth working: Check `auth()->id()` in controller

### Auto-Scroll Not Working
- Ensure messages div has class `bg-gray-50`
- Check z-index of message container
- Verify messages are loading (check Network tab)

## Performance Considerations

### Polling Intervals
- Notification badge: 3 seconds (header updates less frequently)
- Unread indicators: 2 seconds (contact list updates more frequently)
- Adjust intervals in JavaScript if needed

### Database Queries
- `unreadByPartner()` uses GROUP BY for efficiency
- Adds proper indexes on sender_id, receiver_id, read columns
- Consider pagination if chat gets heavy usage

### Pusher Limits
- Free tier: 100 messages/day per app
- Pro tier: Unlimited
- Monitor usage on Pusher dashboard

## Future Enhancements

1. **Typing Indicators** - Show when other user is typing
2. **Message Reactions** - Add emoji reactions to messages
3. **Message Search** - Search past conversations
4. **File Sharing** - Send images/files in chat
5. **User Status** - Show online/offline status
6. **Message Archive** - Save/export conversations
7. **Chat Rooms** - Group conversations (admin + multiple cashiers)

## Code Examples

### Send Message from Frontend
```html
<form action="{{ route('chat.store', $user) }}" method="POST">
    @csrf
    <input type="text" name="message" required>
    <button type="submit">Send</button>
</form>
```

### Get Unread Count
```javascript
fetch('{{ route("chat.unread-count") }}')
    .then(r => r.json())
    .then(data => console.log(data.unread));
```

### Get Unread by Partner
```javascript
fetch('{{ route("chat.unread-by-partner") }}')
    .then(r => r.json())
    .then(data => {
        // data = { user_id: count, ... }
        console.log(data);
    });
```

## Security Notes

- All chat routes require `auth` middleware
- Private Pusher channels use `Route::privateChannel()` with auth checks
- Messages only visible to sender and receiver
- Database queries filtered by user IDs
- CSRF protection on message form

---

**Last Updated:** 2024
**Framework:** Laravel 12.46.0
**Database:** MySQL
**Broadcasting:** Pusher/Redis/Log (configurable)

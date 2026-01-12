<div class="relative" id="chat-notification">
    <a href="{{ route('chat.index', \App\Models\User::where('role', auth()->user()->role === 'admin' ? 'cashier' : 'admin')->first() ?? auth()->user()) }}" 
       class="relative inline-flex items-center justify-center w-10 h-10 rounded-full hover:bg-gray-100 transition">
        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V4a2 2 0 012-2h14a2 2 0 012 2v10a2 2 0 01-2 2h-5l-5 5v-5z"></path>
        </svg>
        <span id="unread-badge" class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full hidden">
            0
        </span>
    </a>
</div>

<script src="https://js.pusher.com/8.0/pusher.min.js"></script>
<script>
    // Note: currentUserId defined in chat view if present, using badgeUserId here
    const badgeUserId = {{ auth()->id() }};

    // Update notification badge
    function updateNotificationBadge() {
        fetch('{{ route("chat.unread-count") }}')
            .then(response => response.json())
            .then(data => {
                const badge = document.getElementById('unread-badge');
                if (data.unread > 0) {
                    badge.textContent = data.unread > 99 ? '99+' : data.unread;
                    badge.classList.remove('hidden');
                } else {
                    badge.classList.add('hidden');
                }
            })
            .catch(err => console.error('Failed to fetch unread count:', err));
    }

    // Check for new messages on page load and every 3 seconds
    document.addEventListener('DOMContentLoaded', updateNotificationBadge);
    setInterval(updateNotificationBadge, 3000);

    // If Pusher is available and configured, listen for real-time updates
    if (typeof Pusher !== 'undefined' && '{{ config("broadcasting.connections.pusher.key") }}') {
        try {
            const pusher = new Pusher('{{ config("broadcasting.connections.pusher.key") }}', {
                cluster: '{{ config("broadcasting.connections.pusher.cluster", "mt") }}',
                encrypted: true,
                forceTLS: true
            });

            const channel = pusher.subscribe('private-notifications-' + badgeUserId);
            channel.bind('message-received', function(data) {
                console.log('New message notification:', data);
                updateNotificationBadge();
            });

            console.log('Pusher notification listener initialized');
        } catch (e) {
            console.warn('Pusher not fully configured:', e.message);
        }
    }
</script>

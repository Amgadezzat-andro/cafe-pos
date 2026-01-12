<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Chat with ' . $user->name) }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                <!-- Chat Partners Sidebar -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow">
                        <div class="p-4 border-b">
                            <h3 class="font-semibold text-gray-900">Contacts</h3>
                        </div>
                        <div class="divide-y max-h-96 overflow-y-auto" id="partners-list">
                            @foreach ($chatPartners as $partner)
                                <a href="{{ route('chat.index', $partner) }}" 
                                   class="p-3 hover:bg-gray-50 flex items-center gap-2 @if($partner->id === $user->id) bg-blue-50 @endif partner-item"
                                   data-partner-id="{{ $partner->id }}">
                                    <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center text-white font-semibold relative">
                                        {{ substr($partner->name, 0, 1) }}
                                        <span class="unread-badge absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center hidden" data-unread="{{ $partner->id }}"></span>
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-semibold text-sm text-gray-900">{{ $partner->name }}</p>
                                        <p class="text-xs text-gray-500">{{ ucfirst($partner->role?->value) }}</p>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Chat Messages Area -->
                <div class="lg:col-span-3">
                    <div class="bg-white rounded-lg shadow flex flex-col h-96">
                        <!-- Header -->
                        <div class="p-4 border-b bg-gradient-to-r from-blue-500 to-blue-600">
                            <h3 class="font-semibold text-white">{{ $user->name }}</h3>
                            <p class="text-xs text-blue-100">{{ ucfirst($user->role?->value) }}</p>
                        </div>

                        <!-- Messages -->
                        <div class="flex-1 overflow-y-auto p-4 space-y-3 bg-gray-50">
                            @forelse ($messages as $message)
                                <div class="@if($message->sender_id === auth()->id()) flex justify-end @else flex justify-start @endif">
                                    <div class="@if($message->sender_id === auth()->id()) bg-blue-500 text-white @else bg-white border @endif rounded-lg px-4 py-2 max-w-xs">
                                        <p class="text-sm">{{ $message->message }}</p>
                                        <p class="text-xs @if($message->sender_id === auth()->id()) text-blue-100 @else text-gray-500 @endif mt-1">
                                            {{ $message->created_at->format('H:i') }}
                                        </p>
                                    </div>
                                </div>
                            @empty
                                <div class="flex items-center justify-center h-full">
                                    <p class="text-gray-500">No messages yet. Start the conversation!</p>
                                </div>
                            @endforelse
                        </div>

                        <!-- Message Input -->
                        <div class="p-4 border-t bg-white">
                            <form action="{{ route('chat.store', $user) }}" method="POST" class="flex gap-2">
                                @csrf
                                <input type="text" 
                                       name="message" 
                                       placeholder="Type a message..." 
                                       class="flex-1 px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                       required>
                                <button type="submit" 
                                        class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                                    Send
                                </button>
                            </form>
                            @error('message')
                                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Info Card -->
                    <div class="mt-4 bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <p class="text-sm text-blue-900">
                            💬 <strong>Tip:</strong> Keep conversations brief and professional. Use this chat for quick updates and questions.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://js.pusher.com/8.0/pusher.min.js"></script>
    <script>
        // Initialize Pusher and Echo for real-time updates
        const currentUserId = {{ auth()->id() }};
        const chatUserId = {{ $user->id }};

        // Auto-scroll to bottom on page load
        document.addEventListener('DOMContentLoaded', function() {
            scrollToBottom();
            loadUnreadCounts();
            setupRealtimeListeners();
            // Update unread counts every 2 seconds as fallback
            setInterval(loadUnreadCounts, 2000);
        });

        function scrollToBottom() {
            const messagesDiv = document.querySelector('.bg-gray-50');
            if (messagesDiv) {
                setTimeout(() => {
                    messagesDiv.scrollTop = messagesDiv.scrollHeight;
                }, 100);
            }
        }

        function loadUnreadCounts() {
            fetch('{{ route("chat.unread-by-partner") }}')
                .then(response => {
                    if (!response.ok) throw new Error('HTTP ' + response.status);
                    return response.json();
                })
                .then(data => {
                    // Update badges for each partner
                    document.querySelectorAll('[data-unread]').forEach(badge => {
                        const partnerId = badge.getAttribute('data-unread');
                        const count = data[partnerId] || 0;
                        if (count > 0) {
                            badge.textContent = count > 9 ? '9+' : count;
                            badge.classList.remove('hidden');
                        } else {
                            badge.classList.add('hidden');
                        }
                    });
                })
                .catch(err => {
                    console.warn('Error loading unread counts:', err);
                    // Silently fail - polling will retry
                });
        }

        function setupRealtimeListeners() {
            // Check if Pusher is available and configured
            const pusherKey = '{{ config("broadcasting.connections.pusher.key") }}';
            if (typeof Pusher !== 'undefined' && pusherKey && pusherKey !== '') {
                try {
                    const pusher = new Pusher(pusherKey, {
                        cluster: '{{ config("broadcasting.connections.pusher.cluster", "mt") }}',
                        encrypted: true,
                        forceTLS: true
                    });

                    // Subscribe to chat channel for current user
                    const chatChannel = pusher.subscribe('private-chat-' + currentUserId);
                    
                    // Listen for incoming messages
                    chatChannel.bind('message-sent', function(data) {
                        // If message is from the person we're chatting with, reload
                        if (data.sender_id === chatUserId) {
                            // Reload page to get latest messages
                            setTimeout(() => location.reload(), 500);
                        } else {
                            // Just update unread counts for other partners
                            loadUnreadCounts();
                        }
                    });

                    // Subscribe to notifications channel
                    const notifyChannel = pusher.subscribe('private-notifications-' + currentUserId);
                    notifyChannel.bind('message-received', function(data) {
                        loadUnreadCounts();
                    });

                    console.log('Real-time listeners initialized with Pusher');
                } catch (e) {
                    console.warn('Pusher not configured. Using polling fallback.', e.message);
                }
            } else {
                console.log('Broadcasting not configured. Using polling fallback.');
            }
        }
    </script>
</x-app-layout>

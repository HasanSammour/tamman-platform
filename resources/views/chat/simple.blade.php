@extends('layouts.app')

@section('title', 'Simple Chat')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4>Simple Chat Test</h4>
                        <div id="connectionStatus" class="badge bg-secondary">Connecting...</div>
                    </div>

                    <div class="card-body">
                        <div id="messages"
                            style="height: 400px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; margin-bottom: 20px; background: #f9fafb;">
                            @foreach($messages as $message)
                                <div class="message {{ $message->sender_id == auth()->id() ? 'text-end' : 'text-start' }}"
                                    style="margin-bottom: 15px;">
                                    <strong>{{ $message->sender->name }}</strong>
                                    <p
                                        style="margin: 5px 0; padding: 8px 12px; background: {{ $message->sender_id == auth()->id() ? '#7c3aed' : 'white' }}; color: {{ $message->sender_id == auth()->id() ? 'white' : '#1f2937' }}; border-radius: 12px; display: inline-block; max-width: 80%;">
                                        {{ $message->content }}
                                    </p>
                                    <br>
                                    <small>{{ $message->created_at->diffForHumans() }}</small>
                                </div>
                            @endforeach
                        </div>

                        <div>
                            <select id="receiver_id" class="form-control mb-2"
                                style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #ddd;">
                                <option value="">-- Select User --</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}
                                        ({{ $user->roles->pluck('name')->first() ?? 'No role' }})</option>
                                @endforeach
                            </select>
                            <textarea id="message_content" class="form-control mb-2" rows="3"
                                placeholder="Type your message..."
                                style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #ddd;"></textarea>
                            <button id="sendBtn" class="btn btn-primary"
                                style="background: #7c3aed; border: none; padding: 10px 20px; border-radius: 8px; color: white; cursor: pointer;">Send
                                Message</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .text-end {
            text-align: right;
        }

        .text-start {
            text-align: left;
        }

        #messages {
            scroll-behavior: smooth;
        }

        .badge-connected {
            background: #10b981 !important;
        }

        .badge-disconnected {
            background: #ef4444 !important;
        }
    </style>
@endpush

@push('scripts')
    <!-- Pusher JS -->
    <script src="https://js.pusher.com/8.4.0/pusher.min.js"></script>

    <script>
        // Enable Pusher logging for debugging
        Pusher.logToConsole = true;

        // Get current user ID from Laravel
        const currentUserId = {{ auth()->id() }};
        const pusherKey = 'e513976241e1c2eefac9';
        const pusherCluster = 'mt1';

        console.log('=== CHAT DEBUG INFO ===');
        console.log('Current User ID:', currentUserId);
        console.log('Pusher Key:', pusherKey);
        console.log('Pusher Cluster:', pusherCluster);
        console.log('=======================');

        // Initialize Pusher
        let pusher;
        let channel;
        let connectionStatus = document.getElementById('connectionStatus');

        try {
            pusher = new Pusher(pusherKey, {
                cluster: pusherCluster,
                forceTLS: true,
                enabledTransports: ['ws', 'wss', 'xhr_streaming', 'xhr_polling'],
                disabledTransports: []
            });

            // Connection events
            pusher.connection.bind('connecting', function () {
                console.log('Pusher: Connecting...');
                connectionStatus.textContent = 'Connecting...';
                connectionStatus.className = 'badge bg-secondary';
            });

            pusher.connection.bind('connected', function () {
                console.log('Pusher: Connected successfully!');
                connectionStatus.textContent = 'Connected';
                connectionStatus.className = 'badge badge-connected';
            });

            pusher.connection.bind('disconnected', function () {
                console.log('Pusher: Disconnected');
                connectionStatus.textContent = 'Disconnected';
                connectionStatus.className = 'badge badge-disconnected';
            });

            pusher.connection.bind('error', function (err) {
                console.error('Pusher: Error', err);
                connectionStatus.textContent = 'Error';
                connectionStatus.className = 'badge badge-disconnected';
            });

            // Subscribe to private channel for current user
            const channelName = 'private-chat.' + currentUserId;
            console.log('Subscribing to channel:', channelName);

            channel = pusher.subscribe(channelName);

            channel.bind('pusher:subscription_succeeded', function () {
                console.log('Successfully subscribed to:', channelName);
            });

            channel.bind('pusher:subscription_error', function (status) {
                console.error('Subscription failed for:', channelName, 'Status:', status);
            });

            // Bind to MessageSent event
            channel.bind('MessageSent', function (data) {
                console.log('🔔 MESSAGE RECEIVED VIA PUSHER:', data);
                console.log('Message details:', {
                    id: data.id,
                    sender_id: data.sender_id,
                    sender_name: data.sender_name,
                    content: data.content,
                    created_at: data.created_at
                });

                const messagesDiv = document.getElementById('messages');
                if (messagesDiv) {
                    const messageDiv = document.createElement('div');
                    messageDiv.className = 'text-start';
                    messageDiv.style.marginBottom = '15px';
                    messageDiv.innerHTML = `
                                <strong>${escapeHtml(data.sender_name)}</strong>
                                <p style="margin: 5px 0; padding: 8px 12px; background: white; color: #1f2937; border-radius: 12px; display: inline-block; max-width: 80%;">${escapeHtml(data.content)}</p>
                                <br>
                                <small>Just now</small>
                            `;
                    messagesDiv.appendChild(messageDiv);
                    messagesDiv.scrollTop = messagesDiv.scrollHeight;

                    // Play notification sound (optional)
                    // new Audio('/notification.mp3').play();
                }
            });

        } catch (error) {
            console.error('Pusher initialization error:', error);
            connectionStatus.textContent = 'Init Failed';
            connectionStatus.className = 'badge badge-disconnected';
        }

        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    </script>

    <!-- Send Message Script -->
    <script>
        const receiverSelect = document.getElementById('receiver_id');
        const messageContent = document.getElementById('message_content');
        const sendBtn = document.getElementById('sendBtn');
        const messagesDiv = document.getElementById('messages');

        // Scroll to bottom on load
        if (messagesDiv) {
            messagesDiv.scrollTop = messagesDiv.scrollHeight;
        }

        sendBtn.addEventListener('click', async () => {
            const receiver_id = receiverSelect.value;
            const content = messageContent.value.trim();

            console.log('Sending message:', { receiver_id, content });

            if (!receiver_id || !content) {
                alert('Please select a user and enter a message');
                return;
            }

            sendBtn.disabled = true;
            sendBtn.textContent = 'Sending...';

            try {
                const response = await fetch('{{ route("chat.send") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        receiver_id: parseInt(receiver_id),
                        content: content
                    })
                });

                const data = await response.json();
                console.log('Send response:', data);

                if (data.success) {
                    // Add message to chat immediately (optimistic update)
                    const messageDiv = document.createElement('div');
                    messageDiv.className = 'text-end';
                    messageDiv.style.marginBottom = '15px';
                    messageDiv.innerHTML = `
                                <strong>You</strong>
                                <p style="margin: 5px 0; padding: 8px 12px; background: #7c3aed; color: white; border-radius: 12px; display: inline-block; max-width: 80%;">${escapeHtml(data.message.content)}</p>
                                <br>
                                <small>Just now</small>
                            `;
                    messagesDiv.appendChild(messageDiv);
                    messageContent.value = '';
                    messagesDiv.scrollTop = messagesDiv.scrollHeight;
                } else {
                    alert('Error: ' + (data.message || 'Unknown error'));
                }
            } catch (error) {
                console.error('Fetch error:', error);
                alert('Network error: ' + error.message);
            } finally {
                sendBtn.disabled = false;
                sendBtn.textContent = 'Send Message';
            }
        });

        // Allow Enter key to send
        messageContent.addEventListener('keypress', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendBtn.click();
            }
        });

        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    </script>
@endpush
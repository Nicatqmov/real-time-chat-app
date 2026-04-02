<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="pusher-key" content="{{ env('PUSHER_APP_KEY') }}">
    <meta name="pusher-cluster" content="{{ env('PUSHER_APP_CLUSTER') }}">
    <title>Chat</title>
    <style>
        * {
            box-sizing: border-box;
        }

        html,
        body {
            height: 100%;
            margin: 0;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background: #efeae2;
            color: #111b21;
            overflow: hidden;
        }

        .page-shell {
            max-width: 1180px;
            height: 100vh;
            margin: 0 auto;
            padding: 16px;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .flash-box {
            padding: 12px 14px;
            border-radius: 10px;
            margin-bottom: 12px;
        }

        .flash-success {
            background: #dff2df;
            color: #1f6b1f;
            border: 1px solid #b9dfb9;
        }

        .flash-error {
            background: #ffe2e2;
            color: #a12626;
            border: 1px solid #f1b7b7;
        }

        .chat-layout {
            display: flex;
            flex: 1;
            min-height: 0;
            background: #f7f9fa;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 18px 50px rgba(17, 27, 33, 0.12);
        }

        .sidebar {
            width: 320px;
            background: #ffffff;
            border-right: 1px solid #d9e1e7;
            display: flex;
            flex-direction: column;
            min-height: 0;
        }

        .sidebar-top {
            padding: 20px;
            border-bottom: 1px solid #e3e9ee;
            background: #f0f2f5;
        }

        .sidebar-title {
            margin: 0 0 14px;
            font-size: 22px;
            font-weight: 700;
        }

        .current-user {
            font-size: 14px;
            color: #54656f;
            margin-bottom: 14px;
        }

        .logout-button {
            border: 0;
            border-radius: 999px;
            background: #128c7e;
            color: white;
            padding: 10px 18px;
            cursor: pointer;
            font-size: 14px;
        }

        .users-list {
            padding: 10px 0;
            overflow-y: auto;
            flex: 1;
        }

        .user-link {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 14px 20px;
            color: #111b21;
            text-decoration: none;
            border-bottom: 1px solid #f0f2f5;
            transition: background 0.2s ease;
        }

        .user-name {
            min-width: 0;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .notification-badge {
            min-width: 22px;
            height: 22px;
            padding: 0 7px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #25d366;
            color: white;
            font-size: 12px;
            font-weight: 700;
            flex-shrink: 0;
        }

        .user-link:hover {
            background: #f5f6f6;
        }

        .user-link.active {
            background: #d9fdd3;
        }

        .chat-panel {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
            min-height: 0;
        }

        .chat-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 18px 24px;
            background: #f0f2f5;
            border-bottom: 1px solid #d9e1e7;
        }

        .chat-title {
            margin: 0;
            font-size: 18px;
            font-weight: 700;
        }

        .chat-subtitle {
            margin-top: 4px;
            color: #667781;
            font-size: 14px;
        }

        .chat-board {
            flex: 1;
            min-height: 0;
            padding: 24px;
            overflow-y: auto;
            background:
                linear-gradient(rgba(239, 234, 226, 0.96), rgba(239, 234, 226, 0.96)),
                radial-gradient(circle at top left, rgba(18, 140, 126, 0.08), transparent 38%),
                radial-gradient(circle at bottom right, rgba(37, 211, 102, 0.08), transparent 32%);
        }

        .message-row {
            display: flex;
            margin-bottom: 12px;
        }

        .message-row.sent {
            justify-content: flex-end;
        }

        .message-row.received {
            justify-content: flex-start;
        }

        .message-bubble {
            max-width: min(72%, 520px);
            padding: 11px 14px;
            border-radius: 14px;
            font-size: 15px;
            line-height: 1.5;
            white-space: pre-wrap;
            overflow-wrap: anywhere;
            word-break: break-word;
            box-shadow: 0 1px 1px rgba(17, 27, 33, 0.08);
        }

        .message-row.sent .message-bubble {
            background: #d9fdd3;
            border-bottom-right-radius: 4px;
        }

        .message-row.received .message-bubble {
            background: #ffffff;
            border-bottom-left-radius: 4px;
        }

        .empty-chat {
            color: #667781;
            font-size: 15px;
        }

        .chat-input-bar {
            display: flex;
            gap: 12px;
            padding: 18px 24px;
            background: #f0f2f5;
            border-top: 1px solid #d9e1e7;
        }

        .chat-input {
            flex: 1;
            min-width: 0;
            border: 1px solid #d1d7db;
            border-radius: 999px;
            padding: 13px 18px;
            font-size: 15px;
            outline: none;
            background: white;
        }

        .chat-input:focus {
            border-color: #128c7e;
        }

        .send-button {
            border: 0;
            border-radius: 999px;
            background: #128c7e;
            color: white;
            padding: 13px 22px;
            cursor: pointer;
            font-size: 15px;
            font-weight: 600;
        }

        .send-button:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        @media (max-width: 1100px) {
            .page-shell {
                padding: 12px;
            }

            .sidebar {
                width: 280px;
            }

            .chat-board {
                padding: 20px;
            }
        }

        @media (max-width: 900px) {
            .page-shell {
                height: 100vh;
                min-height: 100vh;
                overflow: hidden;
                padding: 10px;
            }

            .chat-layout {
                flex-direction: column;
                overflow: hidden;
                border-radius: 14px;
            }

            .sidebar {
                width: 100%;
                max-height: 260px;
                border-right: 0;
                border-bottom: 1px solid #d9e1e7;
                min-height: 180px;
            }

            .chat-panel {
                min-height: 0;
            }

            .chat-board {
                min-height: 0;
                padding: 18px;
            }

            .chat-header {
                padding: 16px 18px;
            }

            .chat-input-bar {
                padding: 14px 16px;
            }

            .message-bubble {
                max-width: 82%;
            }
        }

        @media (max-width: 640px) {
            .page-shell {
                padding: 0;
            }

            .chat-layout {
                height: 100%;
                min-height: 100%;
                border-radius: 0;
                box-shadow: none;
                overflow: hidden;
            }

            .sidebar-top {
                padding: 16px;
            }

            .sidebar-title {
                font-size: 20px;
                margin-bottom: 10px;
            }

            .current-user {
                font-size: 13px;
                margin-bottom: 12px;
            }

            .logout-button {
                width: 100%;
                padding: 11px 16px;
            }

            .user-link {
                padding: 13px 16px;
                font-size: 14px;
            }

            .chat-header {
                padding: 14px 16px;
            }

            .chat-title {
                font-size: 17px;
            }

            .chat-subtitle {
                font-size: 13px;
            }

            .chat-board {
                min-height: 0;
                padding: 14px 12px;
            }

            .message-row {
                margin-bottom: 10px;
            }

            .message-bubble {
                max-width: 88%;
                padding: 10px 12px;
                font-size: 14px;
                border-radius: 12px;
            }

            .chat-input-bar {
                gap: 8px;
                padding: 12px;
            }

            .chat-input {
                padding: 12px 14px;
                font-size: 14px;
            }

            .send-button {
                padding: 12px 16px;
                font-size: 14px;
                min-width: 74px;
            }
        }
    </style>
</head>
<body>
    <div class="page-shell" data-current-user-id="{{ $user->id }}">
        @if (session('success'))
            <div class="flash-box flash-success">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="flash-box flash-error">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="flash-box flash-error">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <div class="chat-layout">
            <aside class="sidebar">
                <div class="sidebar-top">
                    <h1 class="sidebar-title">Chats</h1>
                    <div class="current-user">Logged in as {{ $user->name }}</div>

                    <form method="POST" action="/logout">
                        @csrf
                        <button type="submit" class="logout-button">
                            Logout
                        </button>
                    </form>
                </div>

                <div class="users-list">
                    @forelse ($allUsers as $chatUser)
                        <a
                            href="/?receiver_id={{ $chatUser->id }}"
                            class="user-link {{ optional($selectedUser)->id === $chatUser->id ? 'active' : '' }}"
                            data-user-id="{{ $chatUser->id }}"
                        >
                            <span class="user-name">{{ $chatUser->name }}</span>

                            @if (($unreadCounts[$chatUser->id] ?? 0) > 0)
                                <span class="notification-badge" data-badge-for="{{ $chatUser->id }}">
                                    {{ $unreadCounts[$chatUser->id] }}
                                </span>
                            @endif
                        </a>
                    @empty
                        <div class="user-link">No users found.</div>
                    @endforelse
                </div>
            </aside>

            <section class="chat-panel">
                <div class="chat-header">
                    <div>
                        <h2 class="chat-title">{{ $selectedUser?->name ?? 'Choose a chat' }}</h2>
                        <div class="chat-subtitle" id="selected-user-name">
                            {{ $selectedUser ? 'Conversation is ready' : 'Select a user to start chatting' }}
                        </div>
                    </div>
                </div>

                <div id="chat" class="chat-board">
                    @if ($selectedUser)
                        @forelse ($messages as $message)
                            <div class="message-row {{ $message->user_id === $user->id ? 'sent' : 'received' }}">
                                <div class="message-bubble">
                                    {{ $message->content }}
                                </div>
                            </div>
                        @empty
                            <div class="empty-chat">No messages yet.</div>
                        @endforelse
                    @else
                        <div class="empty-chat">Select a user to see the conversation.</div>
                    @endif
                </div>

                <div class="chat-input-bar">
                    <input type="hidden" id="receiver-id" name="receiver_id" value="{{ $selectedUser?->id }}">

                    <input
                        type="text"
                        id="chat-input"
                        name="message"
                        placeholder="{{ $selectedUser ? 'Type a message' : 'Select a user first' }}"
                        class="chat-input"
                        {{ $selectedUser ? '' : 'disabled' }}
                    >

                    <button id="submit-button" class="send-button" {{ $selectedUser ? '' : 'disabled' }}>
                        Send
                    </button>
                </div>
            </section>
        </div>
    </div>
    <script src="https://js.pusher.com/8.4.0/pusher.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/laravel-echo/1.16.1/echo.iife.js"></script>
    <script src="{{ asset('assets/send_message.js') }}"></script>
</body>
</html>

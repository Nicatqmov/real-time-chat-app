# Real-Time Chat App

A simple real-time chat application built with Laravel, Blade, MySQL, JavaScript, and Pusher.

This project includes:

- user registration and login
- one-to-one messaging
- real-time incoming messages with Pusher
- unread message notifications
- WhatsApp-style chat layout
- responsive chat interface

## Features

- Authentication pages for login and sign up
- Protected chat page for authenticated users
- User list on the left side
- Conversation view on the right side
- Sent messages on the right, received messages on the left
- Send message with button or `Enter`
- Auto-scroll to the latest message
- Unread message badges in the chats list
- Browser title unread count update
- Responsive layout for desktop, tablet, and mobile

## Tech Stack

- Laravel 13
- PHP 8.3+
- MySQL
- Blade
- Vanilla JavaScript
- Pusher

## Project Structure

- `app/Http/Controllers/AuthController.php`
  Handles login, signup, and logout
- `app/Http/Controllers/ChatController.php`
  Handles chat page loading and sending messages
- `app/Events/MessageSent.php`
  Broadcasts new chat messages in real time
- `resources/views/chat.blade.php`
  Main chat UI
- `public/assets/send_message.js`
  Frontend message sending and realtime receiving logic
- `routes/web.php`
  Web routes for auth and chat

## Setup

1. Clone the project

```bash
git clone <your-repo-url>
cd real-time-chat-app
```

2. Install PHP dependencies

```bash
composer install
```

3. Install frontend dependencies

```bash
npm install
```

4. Create the environment file

```bash
copy .env.example .env
```

5. Generate the application key

```bash
php artisan key:generate
```

6. Configure your database in `.env`

Example:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=real_time_chat
DB_USERNAME=root
DB_PASSWORD=
```

7. Configure Pusher in `.env`

```env
BROADCAST_CONNECTION=pusher

PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_app_key
PUSHER_APP_SECRET=your_app_secret
PUSHER_APP_CLUSTER=your_app_cluster
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
```

8. Run migrations

```bash
php artisan migrate
```

9. Start the app

```bash
php artisan serve
```

10. Optional: run Vite during development

```bash
npm run dev
```

## Authentication Routes

- `GET /login`
- `POST /login`
- `GET /signup`
- `POST /signup`
- `POST /logout`

## Chat Routes

- `GET /`
  Opens the chat page for authenticated users
- `POST /chat/send`
  Sends a message

## How Realtime Works

1. User A sends a message.
2. The message is saved in the `messages` table.
3. Laravel dispatches the `MessageSent` event.
4. Pusher broadcasts the event to the receiver's private channel.
5. User B receives the message instantly in the browser without refreshing.

## Unread Notifications

Unread messages are tracked with the `is_read` column in the `messages` table.

- New incoming messages are created with `is_read = false`
- When a chat is opened, that conversation's unread messages are marked as read
- Unread counts are shown in the chats list
- The browser title updates with the total unread count

## Notes

- The sender id is taken from `Auth::id()` on the server for safety
- The app currently supports one-to-one chat
- If Pusher SSL gives local certificate issues on Windows, local config may need CA/cURL setup

## Future Improvements

- last message preview in chat list
- sort chats by latest activity
- typing indicator
- online/offline user status
- message timestamps in UI
- message delete/edit support
- file/image sharing

## License

This project is open-source and available under the MIT License.

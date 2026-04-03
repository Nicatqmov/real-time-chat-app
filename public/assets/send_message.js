const input = document.getElementById('chat-input')
const submitButton = document.getElementById('submit-button')
const screen = document.querySelector("#chat");
const receiverIdInput = document.getElementById('receiver-id');
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
const pusherKey = document.querySelector('meta[name="pusher-key"]')?.getAttribute('content');
const pusherCluster = document.querySelector('meta[name="pusher-cluster"]')?.getAttribute('content');
const currentUserId = document.querySelector('[data-current-user-id]')?.dataset.currentUserId;
const baseTitle = document.title;

function scrollToBottom() {
    screen.scrollTop = screen.scrollHeight;
}

function appendMessage(content, side) {
    const wrapper = document.createElement('div');
    wrapper.className = `message-row ${side === 'right' ? 'sent' : 'received'}`;

    const bubble = document.createElement('div');
    bubble.className = 'message-bubble';
    bubble.textContent = content;

    wrapper.appendChild(bubble);
    screen.appendChild(wrapper);
    scrollToBottom();

    return wrapper;
}

function clearPlaceholderMessages() {
    if (screen.textContent.includes('No messages yet.') || screen.textContent.includes('Select a user to see the conversation.')) {
        screen.innerHTML = '';
    }
}

function updatePageTitle() {
    const badges = document.querySelectorAll('.notification-badge');
    let totalUnread = 0;

    badges.forEach(function (badge) {
        totalUnread += Number(badge.textContent) || 0;
    });

    document.title = totalUnread > 0 ? `(${totalUnread}) ${baseTitle}` : baseTitle;
}

function incrementUnreadBadge(senderId) {
    const userLink = document.querySelector(`[data-user-id="${senderId}"]`);

    if (!userLink) {
        return;
    }

    let badge = userLink.querySelector('.notification-badge');

    if (!badge) {
        badge = document.createElement('span');
        badge.className = 'notification-badge';
        badge.setAttribute('data-badge-for', senderId);
        badge.textContent = '0';
        userLink.appendChild(badge);
    }

    badge.textContent = String((Number(badge.textContent) || 0) + 1);
    updatePageTitle();
}

scrollToBottom();
updatePageTitle();

if (window.Pusher && pusherKey && pusherCluster && currentUserId) {
    window.Pusher = window.Pusher || Pusher;

    const EchoConstructor = window.Echo || window.LaravelEcho;

    if (EchoConstructor) {
        window.Echo = new EchoConstructor({
        broadcaster: 'pusher',
        key: pusherKey,
        cluster: pusherCluster,
        forceTLS: true,
        authEndpoint: '/broadcasting/auth',
        auth: {
            headers: {
                'X-CSRF-TOKEN': csrfToken,
            },
        },
    });

        const channelName = `chat.${currentUserId}`;
        const privateChannel = window.Echo.private(channelName);

        privateChannel.subscribed(function () {
            console.log(`Subscribed to private-${channelName}`);
        });

        privateChannel.error(function (error) {
            console.log('Private channel subscription error:', error);
        });

        const handleIncomingMessage = function (event) {
            console.log('Incoming realtime message:', event);

            if (receiverIdInput.value === String(event.user_id)) {
                clearPlaceholderMessages();
                appendMessage(event.content, 'left');
                return;
            }

            incrementUnreadBadge(event.user_id);
        };

        privateChannel.listen('.message.sent', handleIncomingMessage);
        privateChannel.listen('MessageSent', handleIncomingMessage);
    }
}

input.addEventListener('keydown', function (event) {
    if (event.key === 'Enter') {
        event.preventDefault();
        submitButton.click();
    }
});

submitButton.addEventListener('click', async function (e) {
    e.preventDefault();

    if (receiverIdInput.value === '') {
        alert('Please select a user first.');
        return;
    }

    const message = input.value.trim();

    if (message === '') {
        return;
    }

    clearPlaceholderMessages();
    const pendingMessageElement = appendMessage(message, 'right');
    input.value = '';
    submitButton.disabled = true;

    try {
        const payload = new URLSearchParams({
            _token: csrfToken,
            receiver_id: receiverIdInput.value,
            message: message,
        });

        const response = await fetch('/chat/send', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
            },
            body: payload,
        });

        const rawText = await response.text();
        console.log('Send response status:', response.status);
        console.log('Send response headers content-type:', response.headers.get('content-type'));
        console.log('Send response body:', rawText);

        let data = null;

        try {
            data = rawText ? JSON.parse(rawText) : null;
        } catch (parseError) {
            console.error('Failed to parse send response as JSON:', parseError);
            throw new Error(`Non-JSON response: ${response.status}`);
        }

        if (!response.ok || !data.success) {
            pendingMessageElement.remove();
            alert(data.message || 'Can not send message');
            return;
        }
    } catch (error) {
        console.error('Send message request failed:', error);
        pendingMessageElement.remove();
        alert('Something went wrong while sending the message.');
    } finally {
        submitButton.disabled = false;
        input.focus();
    }
});

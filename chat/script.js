document.addEventListener('DOMContentLoaded', function() {
    const messageForm = document.getElementById('message-form');
    const messageContainer = document.getElementById('message-container');
    const scrollToBottomBtn = document.getElementById('scroll-to-bottom');

    // Mesajları al ve güncelle
    function fetchMessages() {
        fetch('fetch_messages.php')
            .then(response => response.json())
            .then(data => {
                messageContainer.innerHTML = '';
                data.forEach(message => {
                    const messageElement = document.createElement('p');
                    messageElement.innerHTML = `<strong>${message.username}:</strong> ${message.message} <span class="timestamp">${message.created_at}</span>`;
                    messageContainer.appendChild(messageElement);
                });
                // Yeni mesajlar geldiğinde kaydır
                messageContainer.scrollTop = messageContainer.scrollHeight;
            })
            .catch(error => console.error('Mesajlar alınamadı:', error));
    }

    // Sayfa yüklendiğinde mesajları getir
    fetchMessages();
    // Her 5 saniyede bir mesajları yenile
    setInterval(fetchMessages, 5000);

    // Mesaj gönderme işlemi
    messageForm.addEventListener('submit', function(event) {
        event.preventDefault();
        const messageInput = messageForm.querySelector('textarea[name="message"]');
        const message = messageInput.value;

        fetch('send_message.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ message: message })
        })
        .then(response => response.text())
        .then(text => {
            if (text === 'Mesaj gönderildi!') {
                messageInput.value = '';
                fetchMessages();
            } else {
                console.error(text);
            }
        })
        .catch(error => console.error('Mesaj gönderilemedi:', error));
    });

    // Scroll to bottom button click
    scrollToBottomBtn.addEventListener('click', function() {
        messageContainer.scrollTop = messageContainer.scrollHeight;
    });
});

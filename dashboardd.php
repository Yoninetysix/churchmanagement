<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Chatbot</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
        }
        .chat-container {
            width: 400px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        .chat-box {
            width: 100%;
            height: 300px;
            overflow-y: auto;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            padding: 10px;
            border-radius: 5px;
        }
        .user-message, .bot-message {
            padding: 5px 10px;
            margin: 5px;
            border-radius: 5px;
            max-width: 80%;
        }
        .user-message {
            background-color: #3498db;
            color: #fff;
            align-self: flex-end;
        }
        .bot-message {
            background-color: #ecf0f1;
            color: #333;
        }
        input[type="text"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-top: 10px;
        }
        button {
            padding: 10px;
            background-color: #3498db;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
            width: 100%;
        }
    </style>
</head>
<body>
    <div class="chat-container">
        <h2>AI Chatbot</h2>
        <div class="chat-box" id="chat-box"></div>
        <input type="text" id="user-input" placeholder="Ask me anything..." onkeydown="sendMessage(event)">
        <button onclick="sendMessage(event)">Send</button>
    </div>

    <script>
        function sendMessage(event) {
            if (event.key === 'Enter' || event.type === 'click') {
                const userInput = document.getElementById('user-input').value.trim();
                if (userInput !== "") {
                    // Append the user's message
                    appendMessage(userInput, 'user');
                    document.getElementById('user-input').value = '';  // Clear input field

                    // Send the message to the backend (PHP)
                    fetch('chatbot_backend.php', {
                        method: 'POST',
                        body: new URLSearchParams({
                            'user_message': userInput
                        })
                    })
                    .then(response => response.json())  // Parse JSON response
                    .then(data => {
                        if (data.response) {
                            appendMessage(data.response, 'bot');
                        } else {
                            appendMessage('Sorry, I could not understand that.', 'bot');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        appendMessage('Error: Unable to connect to the server.', 'bot');
                    });
                }
            }
        }

        // Function to append messages to the chat box
        function appendMessage(message, sender) {
            const chatBox = document.getElementById('chat-box');
            const messageDiv = document.createElement('div');
            messageDiv.classList.add(sender === 'user' ? 'user-message' : 'bot-message');
            messageDiv.innerText = message;
            chatBox.appendChild(messageDiv);
            chatBox.scrollTop = chatBox.scrollHeight; // Scroll to the bottom
        }
    </script>
</body>
</html>

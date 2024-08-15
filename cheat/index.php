<?php
session_start();

function registerUser($username, $password) {
    $users = [];
    if (file_exists('users.json')) {
        $json = file_get_contents('users.json');
        $users = json_decode($json, true);
    }

    if (isset($users[$username])) {
        return false; // Kullanıcı zaten var
    }

    $users[$username] = password_hash($password, PASSWORD_DEFAULT);
    file_put_contents('users.json', json_encode($users, JSON_PRETTY_PRINT));
    return true;
}

function loginUser($username, $password) {
    if (file_exists('users.json')) {
        $json = file_get_contents('users.json');
        $users = json_decode($json, true);

        if (isset($users[$username]) && password_verify($password, $users[$username])) {
            $_SESSION['username'] = $username;
            return true;
        }
    }
    return false;
}

function clearMessages() {
    if (file_exists('messages.json')) {
        file_put_contents('messages.json', json_encode([], JSON_PRETTY_PRINT));
    }
}

$registerError = '';
$loginError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['register'])) {
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);
        if ($username !== '' && $password !== '') {
            if (!registerUser($username, $password)) {
                $registerError = 'Kullanıcı adı zaten alınmış.';
            } else {
                header('Location: index.php');
                exit;
            }
        }
    } elseif (isset($_POST['login'])) {
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);
        if ($username !== '' && $password !== '') {
            if (!loginUser($username, $password)) {
                $loginError = 'Geçersiz kullanıcı adı veya şifre.';
            } else {
                header('Location: index.php');
                exit;
            }
        }
    } elseif (isset($_POST['logout'])) {
        session_destroy();
        header('Location: index.php');
        exit;
    } elseif (isset($_POST['message'])) {
        $username = $_SESSION['username'];
        $message = trim($_POST['message']);

        if ($message !== '') {
            $messages = [];
            if (file_exists('messages.json')) {
                $json = file_get_contents('messages.json');
                $messages = json_decode($json, true);
            }

            $newMessage = [
                'username' => $username,
                'message' => $message
            ];

            $messages[] = $newMessage;

            file_put_contents('messages.json', json_encode($messages, JSON_PRETTY_PRINT));

            // AJAX isteği ile mesajları dön
            if (isset($_POST['ajax'])) {
                header('Content-Type: application/json');
                echo json_encode($newMessage);
                exit;
            }

            header('Location: index.php');
            exit;
        } elseif (isset($_POST['clear'])) {
            clearMessages();
            if (isset($_POST['ajax'])) {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'cleared']);
                exit;
            }
            header('Location: index.php');
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Application</title>
    <link rel="stylesheet" href="css/login.css" id="login-stylesheet">
    <link rel="stylesheet" href="css/register.css" id="register-stylesheet">
    <link rel="stylesheet" href="css/chat.css" id="chat-stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

<?php if (!isset($_SESSION['username'])): ?>

    <div class="container" id="login-form">
        <h2>Login</h2>
        <?php if ($loginError): ?>
            <p class="error"><?= $loginError ?></p>
        <?php endif; ?>
        <form method="POST" action="index.php">
            <input type="text" name="username" placeholder="Enter username" required>
            <input type="password" name="password" placeholder="Enter password" required>
            <input type="submit" name="login" value="Login">
        </form>
        <p class="toggle-button" onclick="toggleForms()">Don't have an account? Register</p>
    </div>

    <div class="container" id="register-form" style="display:none;">
        <h2>Register</h2>
        <?php if ($registerError): ?>
            <p class="error"><?= $registerError ?></p>
        <?php endif; ?>
        <form method="POST" action="index.php">
            <input type="text" name="username" placeholder="Enter username" required>
            <input type="password" name="password" placeholder="Enter password" required>
            <input type="submit" name="register" value="Register">
        </form>
        <p class="toggle-button" onclick="toggleForms()">Already have an account? Login</p>
    </div>

<?php else: ?>

    <div class="chat-container">
        <div class="chat-header">Chat Application</div>
        
        <div class="message-box" id="message-box">
            <?php
            $messages = [];
            if (file_exists('messages.json')) {
                $json = file_get_contents('messages.json');
                $messages = json_decode($json, true);
            }

            foreach ($messages as $message) {
                echo '<div class="message"><strong>' . htmlspecialchars($message['username']) . ':</strong> ' . htmlspecialchars($message['message']) . '</div>';
            }
            ?>
        </div>
        
        <form id="message-form">
            <div class="input-container">
                <input type="text" name="message" placeholder="Enter your message" required>
                <input type="submit" value="Send">
            </div>
        </form>
        
        <form action="index.php" method="POST">
            <input type="submit" name="logout" value="Logout">
        </form>
    </div>

<?php endif; ?>

<script>
    function toggleForms() {
        var loginForm = document.getElementById('login-form');
        var registerForm = document.getElementById('register-form');
        var loginStylesheet = document.getElementById('login-stylesheet');
        var registerStylesheet = document.getElementById('register-stylesheet');

        if (loginForm.style.display === 'none') {
            loginForm.style.display = 'block';
            registerForm.style.display = 'none';
            loginStylesheet.disabled = false;
            registerStylesheet.disabled = true;
        } else {
            loginForm.style.display = 'none';
            registerForm.style.display = 'block';
            loginStylesheet.disabled = true;
            registerStylesheet.disabled = false;
        }
    }

    $(document).ready(function() {
        function scrollToBottom() {
            $('#message-box').scrollTop($('#message-box')[0].scrollHeight);
        }

        $('#message-form').submit(function(e) {
            e.preventDefault(); // Sayfanın yenilenmesini engelle
            
            var formData = $(this).serialize() + '&ajax=true'; // AJAX isteği için ek parametre

            $.ajax({
                type: 'POST',
                url: 'index.php',
                data: formData,
                success: function(response) {
                    if (response) {
                        var message = $('<div class="message"></div>');
                        message.html('<strong>' + response.username + ':</strong> ' + response.message);
                        $('#message-box').append(message);
                        scrollToBottom(); // En alta kaydır
                        $('#message-form')[0].reset(); // Giriş alanını temizle
                    }
                }
            });
        });

        function loadMessages() {
            $.get('messages.json', function(data) {
                $('#message-box').empty();
                data.forEach(function(message) {
                    var messageElement = $('<div class="message"></div>');
                    messageElement.html('<strong>' + message.username + ':</strong> ' + message.message);
                    $('#message-box').append(messageElement);
                });
                scrollToBottom();
            });
        }

        // Sayfa yüklendiğinde mesajları yükle
        loadMessages();

        // Her 5 saniyede bir mesajları yenile
        setInterval(loadMessages, 5000);
    });
</script>

</body>
</html>

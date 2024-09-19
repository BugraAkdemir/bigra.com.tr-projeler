<?php
session_start();
$dsn = 'mysql:host=localhost;dbname=adminpassword;charset=utf8';
$username = 'bigracom_bugra'; // Kullanıcı adını buraya girin
$password = 'bugra2005bugra'; // Şifreyi buraya girin
$dbname = "bigracom_chatapp";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Bağlantı hatası: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['register'])) {
        $username = $conn->real_escape_string($_POST['username']);
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

        // Kullanıcı adı kontrolü
        $sql = "SELECT * FROM users WHERE username='$username'";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $error = "Bu kullanıcı adı zaten mevcut.";
        } else {
            $sql = "INSERT INTO users (username, password) VALUES ('$username', '$password')";
            if ($conn->query($sql) === TRUE) {
                $success = "Kayıt başarılı!";
            } else {
                $error = "Kayıt yapılamadı: " . $conn->error;
            }
        }
    }

    if (isset($_POST['login'])) {
        $username = $conn->real_escape_string($_POST['username']);
        $password = $_POST['password'];

        $sql = "SELECT * FROM users WHERE username='$username'";
        $result = $conn->query($sql);

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
            } else {
                $error = "Şifre yanlış.";
            }
        } else {
            $error = "Kullanıcı bulunamadı.";
        }
    }

    if (isset($_POST['search'])) {
        $searchTerm = $conn->real_escape_string($_POST['search_term']);
        $sql = "SELECT m.message, u.username, m.created_at FROM messages m JOIN users u ON m.user_id = u.id WHERE m.message LIKE '%$searchTerm%' ORDER BY m.created_at ASC";
    } else {
        $sql = "SELECT m.message, u.username, m.created_at FROM messages m JOIN users u ON m.user_id = u.id ORDER BY m.created_at ASC";
    }

    $messages = [];
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $messages = $result->fetch_all(MYSQLI_ASSOC);
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Uygulaması</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #eef2f3;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .container {
            width: 100%;
            max-width: 800px;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #333;
        }
        form {
            margin-bottom: 20px;
        }
        input[type="text"],
        input[type="password"],
        textarea {
            width: 100%;
            padding: 10px;
            margin: 5px 0 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            background-color: #007bff;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
        }
        button:hover {
            background-color: #0056b3;
        }
        .message-container {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            max-height: 400px;
            overflow-y: auto;
            position: relative;
        }
        .message-container p {
            background: #fff;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 10px;
        }
        .message-container p:last-of-type {
            margin-bottom: 0;
        }
        strong {
            color: #007bff;
        }
        .timestamp {
            font-size: 0.85em;
            color: #666;
        }
        .scroll-to-bottom {
            position: absolute;
            right: 15px;
            bottom: 15px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            display: none;
        }
        .scroll-to-bottom.show {
            display: flex;
        }
        .scroll-to-bottom::before {
            content: '↓';
            font-size: 20px;
        }
        a {
            color: #007bff;
            text-decoration: none;
            display: inline-block;
            margin-top: 10px;
            text-align: center;
        }
        a:hover {
            text-decoration: underline;
        }
        @media (max-width: 600px) {
            form {
                padding: 10px;
            }
            button {
                padding: 8px;
            }
            input[type="text"],
            input[type="password"],
            textarea {
                padding: 8px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Chat Uygulaması</h1>

        <?php if (!isset($_SESSION['user_id'])): ?>
            <h2>Giriş Yap</h2>
            <?php if (isset($error)): ?>
                <p style="color: red;"><?php echo $error; ?></p>
            <?php endif; ?>
            <?php if (isset($success)): ?>
                <p style="color: green;"><?php echo $success; ?></p>
            <?php endif; ?>
            <form method="POST">
                <input type="text" name="username" placeholder="Kullanıcı adı" required>
                <input type="password" name="password" placeholder="Şifre" required>
                <button type="submit" name="login">Giriş Yap</button>
            </form>

            <h2>Kayıt Ol</h2>
            <form method="POST">
                <input type="text" name="username" placeholder="Kullanıcı adı" required>
                <input type="password" name="password" placeholder="Şifre" required>
                <button type="submit" name="register">Kayıt Ol</button>
            </form>

        <?php else: ?>
            <h2>Hoş geldin, <?php echo $_SESSION['username']; ?>!</h2>

            <!-- <h2>Mesaj Ara</h2>
            <form method="POST">
                <input type="text" name="search_term" placeholder="Aramak istediğiniz mesaj..." required>
                <button type="submit" name="search">Ara</button>
            </form> -->

            <div class="message-container" id="message-container">
                <h2>Mesajlar</h2>
                <?php if (empty($messages)): ?>
                    <p>Henüz mesaj yok.</p>
                <?php else: ?>
                    <?php foreach ($messages as $message): ?>
                        <p><strong><?php echo $message['username']; ?>:</strong> <?php echo $message['message']; ?>
                            <span class="timestamp"><?php echo $message['created_at']; ?></span>
                        </p>
                    <?php endforeach; ?>
                <?php endif; ?>
                <button class="scroll-to-bottom" id="scroll-to-bottom"></button>
            </div>

            <h2>Mesaj Gönder</h2>
            <form id="message-form">
                <textarea name="message" placeholder="Mesajınızı yazın..." required></textarea>
                <button type="submit">Gönder</button>
            </form>

            <a href="logout.php">Çıkış Yap</a>

            <script src="script.js"></script>
        <?php endif; ?>
    </div>
</body>
</html>

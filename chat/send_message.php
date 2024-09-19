<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    echo 'Kullanıcı oturum açmamış!';
    exit;
}

$dsn = 'mysql:host=localhost;dbname=adminpassword;charset=utf8';
$username = 'bigracom_bugra'; // Kullanıcı adını buraya girin
$password = 'bugra2005bugra'; // Şifreyi buraya girin
$dbname = "bigracom_chatapp";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Bağlantı hatası: " . $conn->connect_error);
}

if (isset($_POST['message'])) {
    $user_id = $_SESSION['user_id'];
    $message = $conn->real_escape_string($_POST['message']);

    $sql = "INSERT INTO messages (user_id, message, created_at) VALUES ('$user_id', '$message', NOW())";

    if ($conn->query($sql) === TRUE) {
        echo 'Mesaj gönderildi!';
    } else {
        echo 'Mesaj gönderilemedi: ' . $conn->error;
    }
}

$conn->close();
?>

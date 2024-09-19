<?php
header('Content-Type: application/json');
$dsn = 'mysql:host=localhost;dbname=adminpassword;charset=utf8';
$username = 'bigracom_bugra'; // Kullanıcı adını buraya girin
$password = 'bugra2005bugra'; // Şifreyi buraya girin
$dbname = "bigracom_chatapp";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Bağlantı hatası: " . $conn->connect_error);
}

$sql = "SELECT m.message, u.username, m.created_at FROM messages m JOIN users u ON m.user_id = u.id ORDER BY m.created_at ASC";
$result = $conn->query($sql);

$messages = [];
if ($result->num_rows > 0) {
    $messages = $result->fetch_all(MYSQLI_ASSOC);
}

echo json_encode($messages);
$conn->close();
?>

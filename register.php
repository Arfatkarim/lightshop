<?php
require_once "db_connect.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"] ?? "");
    $username = trim($_POST["username"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if (!$name || !$username || !$email || !$password) {
        die("All fields are required.");
    }

    // Hash password
    $hash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (name, username, email, password) VALUES (?, ?, ?, ?)");
    if (!$stmt) { die("Prepare failed: " . $conn->error); }
    $stmt->bind_param("ssss", $name, $username, $email, $hash);

    if ($stmt->execute()) {
        header("Location: login.html");
        exit;
    } else {
        // friendly message if username/email duplicate
        if ($conn->errno == 1062) {
            echo "Username or email already exists.";
        } else {
            echo "Error: " . $conn->error;
        }
    }
}
?>

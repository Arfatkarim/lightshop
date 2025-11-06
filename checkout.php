<?php
session_start();
require_once "db_connect.php";

if (empty($_SESSION["cart"])) {
    echo "Your cart is empty. <a href='shop.php'>Shop now</a>";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (empty($_SESSION["user"])) { echo "Please login first."; exit; }
    $address = trim($_POST['address'] ?? "");
    if (!$address) { echo "Address required."; exit; }

    // calculate total
    $total = 0.0;
    $ids = array_keys($_SESSION["cart"]);
    $placeholders = implode(",", array_fill(0, count($ids), "?"));
    $types = str_repeat("i", count($ids));
    $stmt = $conn->prepare("SELECT id,price FROM products WHERE id IN ($placeholders)");
    $stmt->bind_param($types, ...$ids);
    $stmt->execute();
    $res = $stmt->get_result();
    while($r = $res->fetch_assoc()) {
        $total += $r['price'] * ($_SESSION['cart'][$r['id']] ?? 0);
    }

    $stmt = $conn->prepare("INSERT INTO orders (user_id, total, address) VALUES (?, ?, ?)");
    $uid = $_SESSION['user']['id'];
    $stmt->bind_param("ids", $uid, $total, $address);
    if ($stmt->execute()) {
        $_SESSION['cart'] = [];
        header("Location: thanks.php");
        exit;
    } else {
        echo "Order error: " . $stmt->error;
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Checkout - LightShop</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<header class="topbar"><div class="brand">LightShop</div></header>
<main class="container">
  <h1>Checkout</h1>
  <div class="card">
    <form method="POST" action="checkout.php">
      <label>Full name</label>
      <input name="name" value="<?= isset($_SESSION['user'])?htmlspecialchars($_SESSION['user']['name']):'' ?>" disabled>
      <label>Address</label>
      <textarea name="address" placeholder="Street, City, ZIP" required></textarea>
      <div class="right">
        <button class="btn" type="submit">Place Order</button>
      </div>
    </form>
  </div>
</main>
</body>
</html>

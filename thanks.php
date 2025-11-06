<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Thank you - LightShop</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<main class="centered">
  <div class="card">
    <h2>Thank you <?= isset($_SESSION["user"]) ? htmlspecialchars($_SESSION["user"]["name"]) : "Customer" ?>!</h2>
    <p>Your order has been received. We will contact you soon.</p>
    <p><a href="shop.php" class="btn">Back to shop</a></p>
  </div>
</main>
</body>
</html>

<?php
session_start();
require_once "db_connect.php";

// Add to cart (POST)
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["product_id"])) {
    $pid = (int)$_POST["product_id"];
    if ($pid > 0) {
        if (!isset($_SESSION["cart"][$pid])) $_SESSION["cart"][$pid] = 0;
        $_SESSION["cart"][$pid] += 1;
    }
    header("Location: shop.php");
    exit;
}

// fetch products
$res = $conn->query("SELECT id, name, price, image FROM products");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Shop - LightShop</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
  <header class="topbar">
    <div class="brand">LightShop</div>
    <nav>
      <?php if(isset($_SESSION['user'])): ?>
        <span class="muted">Hello, <?=htmlspecialchars($_SESSION['user']['name'])?></span>
        <a class="link" href="cart.php">Cart (<?=array_sum($_SESSION['cart'] ?? [])?>)</a>
        <a class="link" href="logout.php">Logout</a>
      <?php else: ?>
        <a class="link" href="login.html">Login</a>
        <a class="link" href="register.html">Register</a>
      <?php endif; ?>
    </nav>
  </header>

  <main class="container">
    <h1 class="page-title">Products</h1>
    <section class="grid">
      <?php while($p = $res->fetch_assoc()): ?>
        <article class="card product">
          <?php
          // Replace images
          $image = $p['image'];
          if ($p['name'] === "GFG Bag") {
              $image = "assets/img/OIP.webp";            // GFG Bag
          } elseif ($p['name'] === "GFG T-shirt") {
              $image = "assets/img/OIP (1).webp";       // GFG T-shirt
          } elseif ($p['name'] === "GFG Hoodie") {
              $image = "assets/img/Hoodies.webp";       // GFG Hoodie
          }
          ?>
          <img src="<?=htmlspecialchars($image ?: 'assets/img/placeholder.png')?>" alt="<?=htmlspecialchars($p['name'])?>">
          <h3><?=htmlspecialchars($p['name'])?></h3>
          <p class="price">$<?=number_format($p['price'],2)?></p>
          <form method="POST" class="inline" action="shop.php">
            <input type="hidden" name="product_id" value="<?= (int)$p['id'] ?>">
            <button class="btn small">Add to cart</button>
          </form>
        </article>
      <?php endwhile; ?>
    </section>
  </main>
</body>
</html>

<?php
session_start();
require_once "db_connect.php";

// remove item
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["remove"])) {
    $rid = (int)$_POST["remove"];
    unset($_SESSION["cart"][$rid]);
    header("Location: cart.php");
    exit;
}

// update qty
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["update"])) {
    foreach($_POST["qty"] as $id => $q) {
        $id = (int)$id; $q = (int)$q;
        if ($q <= 0) { unset($_SESSION["cart"][$id]); }
        else { $_SESSION["cart"][$id] = $q; }
    }
    header("Location: cart.php");
    exit;
}

// fetch products in cart
$items = [];
$total = 0.0;
if (!empty($_SESSION["cart"])) {
    $ids = array_keys($_SESSION["cart"]);
    $placeholders = implode(",", array_fill(0, count($ids), "?"));
    $types = str_repeat("i", count($ids));
    $stmt = $conn->prepare("SELECT id,name,price,image FROM products WHERE id IN ($placeholders)");
    $stmt->bind_param($types, ...$ids);
    $stmt->execute();
    $res = $stmt->get_result();
    while($row = $res->fetch_assoc()) {
        $row['qty'] = $_SESSION['cart'][$row['id']] ?? 0;
        $row['total'] = $row['qty'] * $row['price'];
        $total += $row['total'];

        // force correct images
        if($row['name'] === 'GFG Bag') $row['image'] = 'assets/img/OIP.webp';
        if($row['name'] === 'GFG T-shirt') $row['image'] = 'assets/img/OIP (1).webp'; // renamed
        if($row['name'] === 'GFG Hoodie') $row['image'] = 'assets/img/Hoodies.webp';


        $items[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Cart - LightShop</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<header class="topbar">
  <div class="brand">LightShop</div>
  <nav>
    <a class="link" href="shop.php">Continue shopping</a>
    <a class="link" href="logout.php">Logout</a>
  </nav>
</header>

<main class="container">
  <h1 class="page-title">Your Cart</h1>

  <?php if(empty($items)): ?>
    <div class="card"><p>Your cart is empty. <a href="shop.php">Shop now</a></p></div>
  <?php else: ?>
    <form method="POST" action="cart.php">
      <table class="cart-table">
        <thead>
          <tr>
            <th>Product</th>
            <th>Qty</th>
            <th>Price</th>
            <th>Total</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
        <?php foreach($items as $it): ?>
          <tr>
            <td>
              <img class="mini" src="<?=htmlspecialchars($it['image'] ?: 'assets/img/placeholder.png')?>" alt="">
              <strong><?=htmlspecialchars($it['name'])?></strong>
            </td>
            <td>
              <input type="number" name="qty[<?= (int)$it['id'] ?>]" value="<?= (int)$it['qty'] ?>" min="0" class="num">
            </td>
            <td>$<?=number_format($it['price'],2)?></td>
            <td>$<?=number_format($it['total'],2)?></td>
            <td>
              <button name="remove" value="<?= (int)$it['id'] ?>" class="btn small outline">Remove</button>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
      <div class="right">
        <strong>Grand Total: $<?= number_format($total,2) ?></strong>
        <div class="actions">
          <button type="submit" name="update" value="1" class="btn">Update Cart</button>
          <a class="btn ghost" href="checkout.php">Proceed to Checkout</a>
        </div>
      </div>
    </form>
  <?php endif; ?>
</main>
</body>
</html>

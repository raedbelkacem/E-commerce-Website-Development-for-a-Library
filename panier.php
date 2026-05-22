<?php
session_start();

include "layout/header.php";

// Sample products data - in real app, fetch from database
$products = [
    1 => ['name' => 'Digital Camera', 'price' => 299.99],
    2 => ['name' => 'Smartwatch', 'price' => 199.99],
    3 => ['name' => 'Wireless Headphones', 'price' => 149.99],
];

// Initialize cart in session if not set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle add to cart via GET
if (isset($_GET['action']) && $_GET['action'] === 'add' && isset($_GET['id'])) {
    $add_id = intval($_GET['id']);
    if (isset($products[$add_id])) {
        if (isset($_SESSION['cart'][$add_id])) {
            $_SESSION['cart'][$add_id]++;
        } else {
            $_SESSION['cart'][$add_id] = 1;
        }
        $_SESSION['message'] = "Produit ajouté au panier avec succès.";
    }
    header("Location: panier.php");
    exit;
}

// Handle update quantity or remove item
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update'])) {
        foreach ($_POST['quantities'] as $product_id => $quantity) {
            $quantity = (int)$quantity;
            if ($quantity <= 0) {
                unset($_SESSION['cart'][$product_id]);
            } else {
                $_SESSION['cart'][$product_id] = $quantity;
            }
        }
    } elseif (isset($_POST['clear'])) {
        $_SESSION['cart'] = [];
    }
}

// Calculate total
$total = 0.0;
foreach ($_SESSION['cart'] as $product_id => $quantity) {
    if (isset($products[$product_id])) {
        $total += $products[$product_id]['price'] * $quantity;
    }
}
?>

<div class="container py-5">
    <h2>Shopping Cart</h2>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_SESSION['message']) ?></div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <?php if (empty($_SESSION['cart'])): ?>
        <p>Your cart is empty.</p>
        <a href="index.php" class="btn btn-primary">Continue Shopping</a>
    <?php else: ?>
        <form method="post" class="mb-3">
            <table class="table table-bordered align-middle">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th style="width: 120px;">Quantity</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($_SESSION['cart'] as $product_id => $quantity): 
                        if (!isset($products[$product_id])) continue;
                        $product = $products[$product_id];
                        $line_total = $product['price'] * $quantity;
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($product['name']) ?></td>
                        <td>$<?= number_format($product['price'], 2) ?></td>
                        <td>
                            <input type="number" name="quantities[<?= $product_id ?>]" value="<?= $quantity ?>" min="0" class="form-control">
                        </td>
                        <td>$<?= number_format($line_total, 2) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="3" class="text-end">Total:</th>
                        <th>$<?= number_format($total, 2) ?></th>
                    </tr>
                </tfoot>
            </table>

            <div class="d-flex justify-content-between">
                <button type="submit" name="update" class="btn btn-primary">Update Cart</button>
                <button type="submit" name="clear" class="btn btn-danger" onclick="return confirm('Are you sure you want to clear the cart?');">Clear Cart</button>
            </div>
        </form>

        <a href="checkout.php" class="btn btn-success">Proceed to Checkout</a>
    <?php endif; ?>
</div>

<?php
include "layout/footer.php";
?>

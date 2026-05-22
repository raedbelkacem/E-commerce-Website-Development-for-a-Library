<?php
session_start(); 


$servername = "localhost"; 
$username = "root"; 
$password = ""; 
$dbname = "log_page"; 


$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    
    die("Connection failed: " . $conn->connect_error);
}


$sql = "SELECT id, name, description, price, quantity, image, category, created_at FROM product"; 
$result = $conn->query($sql); 

$products = [];
if ($result && $result->num_rows > 0) { 
    while ($row = $result->fetch_assoc()) { 
        $products[] = $row; 
    }
}


$search = '';
$category_filter = '';


if (isset($_GET['search'])) {
    $search = strtolower(trim($_GET['search'])); 
}


if (isset($_GET['category'])) {
    $category_filter = strtolower(trim($_GET['category'])); 
}


$filtered_products = array_filter($products, function ($product) use ($search, $category_filter) {
   
    $name_match = $search === '' || strpos(strtolower($product['name']), $search) !== false;
   
    $category_match = $category_filter === '' || strtolower($product['category']) === $category_filter;
    return $name_match && $category_match; 
});

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $product_id = (int)$_POST['product_id']; 

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

   
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id]++;
    } else {
        $_SESSION['cart'][$product_id] = 1; 
    }

  
    header("Location: index.php");
    exit;
}

include "layout/header.php"; 

$cart_count = isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0;
?>


<section class="py-5" style="background-color: #000;">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-lg-7 text-white">
        <small class="text-uppercase">TechPulse</small>
        <h1 class="display-4 fw-bold">"Explore the Future of Tech – Gadgets, Devices, and More!"</h1>
        <a href="shop.php" class="btn btn-warning text-uppercase fw-bold">Shop Now</a>
      </div>
      <div class="col-lg-4">
        <img src="/image/robot.png" alt="Cordless Vacuum" class="img-fluid" />
      </div>
    </div>
  </div>
</section>


<section class="py-5">
  <div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2>Products</h2>
      <a href="cart.php" class="btn btn-outline-primary">Cart (<?php echo $cart_count; ?>)</a>
    </div>

    <div class="row g-4">
      <?php if (count($filtered_products) > 0): ?>
        <?php foreach ($filtered_products as $product): ?>
          <div class="col-md-4">
            <div class="card h-100 shadow-sm">
              <img src="<?php echo htmlspecialchars($product['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($product['name']); ?>">
              <div class="card-body">
                <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                <p class="card-text">$<?php echo number_format($product['price'], 2); ?></p>
                <form method="post" class="d-inline">
                  <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>" />
                  <button type="submit" class="btn btn-primary">Buy</button>
                </form>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p>No products found matching your search.</p>
      <?php endif; ?>
    </div>
  </div>
</section>

<?php include "layout/footer.php"; ?>

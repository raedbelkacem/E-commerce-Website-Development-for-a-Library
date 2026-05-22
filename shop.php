<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "log_page"; // Ensure this is your actual database name

$conn = new mysqli($servername, $username, $password, $dbname);


$categories_stmt = $conn->prepare("SELECT DISTINCT category FROM product ORDER BY category");
$categories_stmt->execute();
$categories = $categories_stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';

$query = "SELECT id, name, description, price, image, quantity, category FROM product";
$params = [];
$conditions = [];

if ($category !== '') {
    $conditions[] = "category = ?";
    $params[] = $category;
}

if ($search !== '') {
    $conditions[] = "name LIKE ?";
    $params[] = "%$search%";
}

if (count($conditions) > 0) {
    $query .= " WHERE " . implode(" AND ", $conditions);
}

$query .= " ORDER BY name";

$stmt = $conn->prepare($query);

if (count($params) > 0) {
    $types = '';
    foreach ($params as $param) {
        $types .= is_int($param) ? 'i' : 's';
    }
    // Fix bind_param to pass parameters by reference
    $bind_names[] = $types;
    for ($i=0; $i<count($params); $i++) {
        $bind_name = 'bind' . $i;
        $$bind_name = $params[$i];
        $bind_names[] = &$$bind_name;
    }
    call_user_func_array([$stmt, 'bind_param'], $bind_names);
}

$stmt->execute();
$products = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

?>

<?php include 'layout/header.php'; ?>

<div class="container" style="margin-top:20px;">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Accueil</a></li>
            <li class="breadcrumb-item active" aria-current="page">Boutique</li>
        </ol>
    </nav>

    <div class="row">
        <!-- Sidebar -->
        <aside class="col-md-3">
            <form method="GET" action="shop.php" class="mb-3">
                <label for="search" class="form-label">Rechercher dans les résultats</label>
                <div class="input-group">
                    <input type="text" id="search" name="search" class="form-control" placeholder="Taper pour rechercher..." value="<?php echo htmlspecialchars($search); ?>">
                    <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i></button>
                </div>
            </form>

            <div class="card p-3">
                <h5 class="mb-3" style="border-bottom: 2px solid #007bff; padding-bottom: 5px;">Catégories de produits</h5>
                    <ul class="list-group list-group-flush">
                    <?php foreach ($categories as $cat): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <a href="shop.php?category=<?php echo urlencode($cat['category']); ?>" class="text-decoration-none"><?php echo htmlspecialchars($cat['category']); ?></a>
                        </li>
                    <?php endforeach; ?>
                    </ul>
            </div>
        </aside>

        <!-- Main content -->
        <section class="col-md-9">
            <?php if (count($products) === 0): ?>
                <p>Aucun produit trouvé.</p>
            <?php else: ?>
                <?php foreach ($products as $product): ?>
                    <div class="card mb-4 p-3" style="display: flex; flex-direction: row; align-items: center;">
                        <div style="flex: 1;">
                            <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" style="max-width: 150px; max-height: 150px; object-fit: contain;">
                        </div>
                        <div style="flex: 3; padding-left: 20px;">
                            <h5 style="color: #007bff;"><?php echo htmlspecialchars($product['name']); ?></h5>
                            <p style="color: #333;"><?php echo htmlspecialchars(mb_strimwidth($product['description'], 0, 150, '...')); ?></p>
                        </div>
                        <div style="flex: 1; background-color: #f8f9fa; padding: 15px; border-radius: 5px; text-align: center;">
                            <h6 style="color: #007bff;">Disponibilité</h6>
                            <?php
                                // Example availability display logic based on quantity
                                if ($product['quantity'] > 0) {
                                    echo '<span class="badge bg-success">En stock</span>';
                                } else {
                                    echo '<span class="text-success">Produit sur commande<br>Délais:</span>';
                                }
                            ?>
                            <div class="mt-2">
                                <span style="color: red; font-weight: bold; font-size: 1.2em;"><?php echo number_format($product['price'], 3, ',', ' '); ?> DT</span>
                            </div>
                            <div class="mt-3 d-flex justify-content-center gap-2">
                                <a href="panier.php?action=add&id=<?php echo $product['id']; ?>" class="btn btn-outline-primary" title="Ajouter au panier"><i class="bi bi-cart"></i></a>
                                <a href="product.php?id=<?php echo $product['id']; ?>" class="btn btn-outline-secondary" title="Voir le produit"><i class="bi bi-eye"></i></a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>
    </div>
</div>

<?php include 'layout/footer.php'; ?>

<!-- Bootstrap Icons CDN for icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

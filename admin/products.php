<?php
session_start();

// Vérifie que l'utilisateur est admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: /login.php");
    exit;
}

include "../layout/header.php";

// Connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "log_page";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connexion échouée: " . $conn->connect_error);
}

$message = "";

// === Suppression de produit ===
if (isset($_POST['delete_id'])) {
    $delete_id = intval($_POST['delete_id']);
    $stmt = $conn->prepare("DELETE FROM product WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    if ($stmt->execute()) {
        $message .= "<br>Produit supprimé avec succès.";
    } else {
        $message .= "<br>Erreur lors de la suppression : " . $stmt->error;
    }
    $stmt->close();
}

// === Ajout de produits ===
elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['products']) && is_array($_POST['products'])) {
    $targetDir = "C:/Users/raed/OneDrive/Bureau/log-page/img/";
    $webDir = "/img/";

    if (!is_dir($targetDir)) {
        if (!mkdir($targetDir, 0755, true)) {
            $message .= "<br>Erreur : impossible de créer le dossier.";
        }
    }

    if (!is_writable($targetDir)) {
        $message .= "<br>Erreur : le dossier n'est pas accessible en écriture.";
    }

    foreach ($_POST['products'] as $index => $product) {
        $name = htmlspecialchars(trim($product['name'] ?? ''));
        $price = floatval($product['price'] ?? 0);
        $description = htmlspecialchars(trim($product['description'] ?? ''));
        $category = htmlspecialchars(trim($product['category'] ?? ''));
        $image_path = '';

        // Corrected $_FILES handling for nested file inputs
        $uploadError = $_FILES['products']['error'][$index]['image_file'] ?? UPLOAD_ERR_NO_FILE;

        if ($uploadError === UPLOAD_ERR_NO_FILE) {
            $message .= "<br>Erreur : aucun fichier sélectionné pour le produit " . htmlspecialchars($name);
            continue;
        }

        if ($uploadError === UPLOAD_ERR_OK) {
            $tmp_name = $_FILES['products']['tmp_name'][$index]['image_file'];
            $originalName = basename($_FILES['products']['name'][$index]['image_file']);
            $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
            $newName = uniqid("img_") . '.' . $extension;
            $target_file = $targetDir . $newName;
            $web_path = $webDir . $newName;

            $maxSize = 2 * 1024 * 1024; // 2MB
            if ($_FILES['products']['size'][$index]['image_file'] > $maxSize) {
                $message .= "<br>Erreur : taille maximale de 2MB dépassée pour le produit " . htmlspecialchars($name);
                continue;
            }

            $allowedMimeTypes = ['image/jfif','image/jpeg', 'image/png', 'image/gif'];
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $tmp_name);
            finfo_close($finfo);

            if (!in_array($mimeType, $allowedMimeTypes)) {
                $message .= "<br>Erreur : type de fichier non autorisé ($mimeType) pour le produit " . htmlspecialchars($name);
                continue;
            }

            if (move_uploaded_file($tmp_name, $target_file)) {
                $image_path = $web_path;
                $message .= "<br>Image téléchargée avec succès pour le produit " . htmlspecialchars($name);
            } else {
                $message .= "<br>Erreur lors du déplacement de l'image pour le produit " . htmlspecialchars($name);
                continue;
            }
        } else {
            $message .= "<br>Erreur d'upload inattendue (code $uploadError) pour le produit " . htmlspecialchars($name);
            continue;
        }

        // Vérifie si le produit existe déjà (nom unique)
        $checkStmt = $conn->prepare("SELECT id FROM product WHERE name = ?");
        $checkStmt->bind_param("s", $name);
        $checkStmt->execute();
        $checkStmt->store_result();

        if ($checkStmt->num_rows > 0) {
            $message .= "<br>Erreur : un produit avec ce nom existe déjà: " . htmlspecialchars($name);
            $checkStmt->close();
            continue;
        }
        $checkStmt->close();

        // Insertion
        if ($image_path) {
            $stmt = $conn->prepare("INSERT INTO product (name, price, image, description, category) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sdsss", $name, $price, $image_path, $description, $category);
            if ($stmt->execute()) {
                $message .= "<br>Produit ajouté avec succès: " . htmlspecialchars($name);
            } else {
                $message .= "<br>Erreur d'insertion pour le produit " . htmlspecialchars($name) . ": " . $stmt->error;
            }
            $stmt->close();
        }
    }
}

// === Récupération des produits ===
$result = $conn->query("SELECT * FROM product");
$products = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}
$conn->close();
?>

<div class="container py-5">
    <h2>Gestion des produits</h2>

    <?php if ($message): ?>
        <div class="alert alert-info"><?= $message ?></div>
    <?php endif; ?>

    <!-- Formulaire d’ajout -->
    <form method="post" class="mb-4" enctype="multipart/form-data" id="multiProductForm">
        <div id="productContainer">
            <div class="product-entry border p-3 mb-3">
                <div class="mb-3"><label>Nom</label>
                    <input type="text" name="products[0][name]" class="form-control" required />
                </div>
                <div class="mb-3"><label>Prix</label>
                    <input type="number" step="0.01" name="products[0][price]" class="form-control" required />
                </div>
                <div class="mb-3"><label>Image</label>
                    <input type="file" name="products[0][image_file]" class="form-control" accept="image/*" required />
                </div>
                <div class="mb-3"><label>Description</label>
                    <textarea name="products[0][description]" class="form-control"></textarea>
                </div>
                <div class="mb-3"><label>Catégorie</label>
                    <input type="text" name="products[0][category]" class="form-control" />
                </div>
                <button type="button" class="btn btn-danger remove-product-btn">Supprimer</button>
            </div>
        </div>
        <button type="button" class="btn btn-secondary mb-3" id="addProductBtn">Ajouter un autre produit</button>
        <br />
        <button type="submit" class="btn btn-primary">Ajouter les produits</button>
    </form>

    <script>
        let productIndex = 1;
        document.getElementById('addProductBtn').addEventListener('click', function () {
            const container = document.getElementById('productContainer');
            const newEntry = document.createElement('div');
            newEntry.classList.add('product-entry', 'border', 'p-3', 'mb-3');
            newEntry.innerHTML = `
                <div class="mb-3"><label>Nom</label>
                    <input type="text" name="products[\${productIndex}][name]" class="form-control" required />
                </div>
                <div class="mb-3"><label>Prix</label>
                    <input type="number" step="0.01" name="products[\${productIndex}][price]" class="form-control" required />
                </div>
                <div class="mb-3"><label>Image</label>
                    <input type="file" name="products[\${productIndex}][image_file]" class="form-control" accept="image/*" required />
                </div>
                <div class="mb-3"><label>Description</label>
                    <textarea name="products[\${productIndex}][description]" class="form-control"></textarea>
                </div>
                <div class="mb-3"><label>Catégorie</label>
                    <input type="text" name="products[\${productIndex}][category]" class="form-control" />
                </div>
                <button type="button" class="btn btn-danger remove-product-btn">Supprimer</button>
            `;
            container.appendChild(newEntry);
            productIndex++;

            newEntry.querySelector('.remove-product-btn').addEventListener('click', function () {
                newEntry.remove();
            });
        });

        document.querySelectorAll('.remove-product-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                btn.closest('.product-entry').remove();
            });
        });
    </script>

    <!-- Liste des produits -->
    <h3>Produits existants</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th><th>Nom</th><th>Prix</th><th>Catégorie</th><th>Description</th><th>Image</th><th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $p): ?>
            <tr>
                <td><?= htmlspecialchars($p['id']) ?></td>
                <td><?= htmlspecialchars($p['name']) ?></td>
                <td>$<?= number_format($p['price'], 2) ?></td>
                <td><?= htmlspecialchars($p['category']) ?></td>
                <td><?= htmlspecialchars(mb_strimwidth($p['description'], 0, 50, "...")) ?></td>
                <td><img src="<?= htmlspecialchars($p['image']) ?>" alt="" style="height:50px;" onerror="this.onerror=null;this.src='/img/placeholder.png';" /></td>
                <td>
                    <a href="edit_product.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-warning">Modifier</a>
                    <form method="post" action="products.php" style="display:inline;" onsubmit="return confirm('Êtes-vous sûr ?');">
                        <input type="hidden" name="delete_id" value="<?= $p['id'] ?>" />
                        <button type="submit" class="btn btn-sm btn-danger">Supprimer</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include "../layout/footer.php"; ?>

<?php
ob_start();
session_start();
// Vérifie que l'utilisateur est admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: /login.php");
    exit;
}
include "../layout/navbar.php";

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
$product = null;

// Récupérer l'ID du produit depuis GET 
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: products.php");
    exit;
}

$product_id = intval($_GET['id']);

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update'])) {
        // Mise à jour du produit
        $name = htmlspecialchars(trim($_POST['name'] ?? ''));
        $price = floatval($_POST['price'] ?? 0);
        $description = htmlspecialchars(trim($_POST['description'] ?? ''));
        $category = htmlspecialchars(trim($_POST['category'] ?? ''));

        // Gestion de l'image
        $image_path = null;
        $targetDir = "C:/Users/raed/OneDrive/Bureau/log-page/img/";
        $webDir = "/img/";

        if (!empty($_FILES['image_file']['name'])) {
            $uploadError = $_FILES['image_file']['error'];

            if ($uploadError === UPLOAD_ERR_OK) {
                $tmp_name = $_FILES['image_file']['tmp_name'];
                $originalName = basename($_FILES['image_file']['name']);
                $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
                $newName = uniqid("img_") . '.' . $extension;
                $target_file = $targetDir . $newName;
                $web_path = $webDir . $newName;

                $maxSize = 2 * 1024 * 1024; // 2MB
                if ($_FILES['image_file']['size'] > $maxSize) {
                    $message .= "<br>Erreur : taille maximale de 2MB dépassée pour l'image.";
                } else {
                    $allowedMimeTypes = ['image/jfif','image/jpeg', 'image/png', 'image/gif'];
                    $finfo = finfo_open(FILEINFO_MIME_TYPE);
                    $mimeType = finfo_file($finfo, $tmp_name);
                    finfo_close($finfo);

                    if (!in_array($mimeType, $allowedMimeTypes)) {
                        $message .= "<br>Erreur : type de fichier non autorisé ($mimeType).";
                    } else {
                        if (!is_dir($targetDir)) {
                            mkdir($targetDir, 0755, true);
                        }
                        if (move_uploaded_file($tmp_name, $target_file)) {
                            $image_path = $web_path;
                        } else {
                            $message .= "<br>Erreur lors du déplacement de l'image.";
                        }
                    }
                }
            } else {
                $message .= "<br>Erreur d'upload inattendue (code $uploadError).";
            }
        }

        // Préparer la requête de mise à jour
        if ($image_path) {
            $stmt = $conn->prepare("UPDATE product SET name = ?, price = ?, image = ?, description = ?, category = ? WHERE id = ?");
            $stmt->bind_param("sdsssi", $name, $price, $image_path, $description, $category, $product_id);
        } else {
            $stmt = $conn->prepare("UPDATE product SET name = ?, price = ?, description = ?, category = ? WHERE id = ?");
            $stmt->bind_param("sdssi", $name, $price, $description, $category, $product_id);
        }

        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();
            header("Location: products.php?message=Produit mis à jour avec succès");
            exit;
        } else {
            $message .= "<br>Erreur lors de la mise à jour : " . $stmt->error;
            $stmt->close();
        }
    } elseif (isset($_POST['delete'])) {
        // Suppression du produit
        $stmt = $conn->prepare("DELETE FROM product WHERE id = ?");
        $stmt->bind_param("i", $product_id);
        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();
            header("Location: products.php?message=Produit supprimé avec succès");
            exit;
        } else {
            $message .= "<br>Erreur lors de la suppression : " . $stmt->error;
            $stmt->close();
        }
    }
}

// Récupérer les données du produit pour affichage
$stmt = $conn->prepare("SELECT * FROM product WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 1) {
    $product = $result->fetch_assoc();
} else {
    $stmt->close();
    $conn->close();
    header("Location: products.php");
    exit;
}
$stmt->close();
$conn->close();
?>

<div class="container py-5">
    <h2>Modifier le produit</h2>

    <?php if ($message): ?>
        <div class="alert alert-info"><?= $message ?></div>
    <?php endif; ?>

    <?php if ($product): ?>
    <form method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label>Nom</label>
            <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($product['name']) ?>" required />
        </div>
        <div class="mb-3">
            <label>Prix</label>
            <input type="number" step="0.01" name="price" class="form-control" value="<?= htmlspecialchars($product['price']) ?>" required />
        </div>
        <div class="mb-3">
            <label>Image actuelle</label><br />
            <img src="<?= htmlspecialchars($product['image']) ?>" alt="" style="height:100px;" onerror="this.onerror=null;this.src='/img/placeholder.png';" />
        </div>
        <div class="mb-3">
            <label>Changer l'image</label>
            <input type="file" name="image_file" class="form-control" accept="image/*" />
        </div>
        <div class="mb-3">
            <label>Description</label>
            <textarea name="description" class="form-control"><?= htmlspecialchars($product['description']) ?></textarea>
        </div>
        <div class="mb-3">
            <label>Catégorie</label>
            <input type="text" name="category" class="form-control" value="<?= htmlspecialchars($product['category']) ?>" />
        </div>
        <button type="submit" name="update" class="btn btn-primary">Mettre à jour</button>
        <button type="submit" name="delete" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce produit ?');">Supprimer</button>
        <a href="products.php" class="btn btn-secondary">Annuler</a>
    </form>
    <?php endif; ?>
</div>

<?php include "../layout/footer.php"; ?>

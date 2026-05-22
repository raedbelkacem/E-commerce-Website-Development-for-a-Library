<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>ElectroShop</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="css/style.css" />
</head>
<body>
    <!-- header.php -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="index.php">ElectroShop</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
        aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
            <li class="nav-item"><a class="nav-link" href="index.php">Accueil</a></li>
            <li class="nav-item"><a class="nav-link" href="shop.php">Boutique</a></li>
            <li class="nav-item"><a class="nav-link" href="categories.php">Catégories</a></li>
            <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
            <?php if (isset($_SESSION['user_id'])): ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <?php if ($_SESSION['role'] === 'admin'): ?>
                            <i class="bi bi-gear-fill me-1"></i>
                        <?php else: ?>
                            <i class="bi bi-person-circle me-1"></i>
                        <?php endif; ?>
                        <?= htmlspecialchars($_SESSION['first_name'] ?? 'User') ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="dashboard.php">Dashboard</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                    </ul>
                </li>
            <?php else: ?>
                <li class="nav-item"><a class="nav-link" href="login.php">Connexion</a></li>
                <li class="nav-item"><a class="nav-link" href="register.php">S'inscrire</a></li>
            <?php endif; ?>
        </ul>
        </div>
    </div>
    </nav>
</body>


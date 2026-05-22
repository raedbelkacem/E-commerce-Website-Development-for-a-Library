<?php
// Démarrer une session si aucune session n’est encore active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vérifier si l'utilisateur est connecté
$authenticated = false;
if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
    $authenticated = true;
}
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>log_page</title>
    <link rel="icon" href="/image/image.jfif" />
    
    <!-- Intégration de Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css"
          rel="stylesheet"
          integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7"
          crossorigin="anonymous" />

    <!-- Style personnalisé -->
    <style>
      .navbar-yellow {
        background: linear-gradient(135deg,rgb(255, 255, 255), #764ba2);
        font-family: Arial, sans-serif;
      }
      .navbar-yellow .navbar-brand,
      .navbar-yellow .nav-link,
      .navbar-yellow .navbar-text {
        color: #000;
      }
      .navbar-yellow .nav-link:hover {
        color: #555;
      }

      .category-menu {
        background-color: #fff;
        padding: 10px 0;
        border-bottom: 1px solid #ddd;
      }
      .category-item {
        text-align: center;
        font-size: 14px;
        color: #333;
        cursor: pointer;
        padding: 5px 10px;
        transition: background-color 0.3s ease;
      }
      
      .category-item:hover {
        background-color: #f0f0f0;
      }

      .category-icon {
        font-size: 20px;
        display: block;
        margin-bottom: 5px;
      }

      .search-input {
        width: 300px;
      }
      @media (max-width: 768px) {
        .search-input {
          width: 100%;
          margin-top: 10px;
        }
      }
    </style>
  </head>
  <body>

    <!-- Barre de navigation principale -->
    <nav class="navbar navbar-expand-lg navbar-yellow shadow-sm">
      <div class="container">
        <!-- Logo -->
        <a class="navbar-brand d-flex align-items-center" href="/index.php">
          <img src="/image/image.jfif" width="30" height="30" class="me-2" alt="Logo" />
          TechPulse
        </a>

        <!-- Bouton responsive pour navigation mobile -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent">
          <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Contenu du menu -->
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <!-- Liens du menu -->
          <ul class="navbar-nav me-auto mb-2 mb-lg-0 align-items-center">
            <li class="nav-item"><a class="nav-link text-dark" href="/index.php">Home</a></li>
            <li class="nav-item"><a class="nav-link text-dark" href="#">Page</a></li>
            <li class="nav-item"><a class="nav-link text-dark" href="shop.php">Shop</a></li>
            <li class="nav-item"><a class="nav-link text-dark" href="contact.php">Contact</a></li>
          </ul>
           <!-- Champ de recherche -->
          <form class="d-flex me-3" role="search" method="get" action="/index.php">
            <input class="form-control me-2 search-input" type="search" name="search"
                   placeholder="Search products..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" />
            <button class="btn btn-dark" type="submit">
              <!-- Icône de recherche -->
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                   fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001l3.85 3.85
                         a1 1 0 0 0 1.415-1.414l-3.85-3.85zM6.3 11.742a5.5 5.5 0 1 1
                         0-11 5.5 5.5 0 0 1 0 11z" />
              </svg>
            </button>
          </form>

          <!-- Menu utilisateur -->
          <ul class="navbar-nav mb-2 mb-lg-0 align-items-center">
          <?php if ($authenticated): ?>
            <!-- Si admin -->
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle text-dark" data-bs-toggle="dropdown">Admin Panel</a>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="/admin/products.php">Manage Products</a></li>
                <li><a class="dropdown-item" href="/admin/orders.php">Manage Orders</a></li>
                <li><a class="dropdown-item" href="/admin/users.php">Manage Users</a></li>
                <li><hr class="dropdown-divider" /></li>
                <li><a class="dropdown-item" href="/logout.php">Logout</a></li>
              </ul>
            </li>
            <?php else: ?>
            <!-- Utilisateur normal -->
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle text-dark" data-bs-toggle="dropdown">Profile</a>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="/Profile.php">View Profile</a></li>
                <li><hr class="dropdown-divider" /></li>
                <li><a class="dropdown-item" href="/logout.php">Logout</a></li>
              </ul>
            </li>
            <?php endif; ?>
          <?php else: ?>
            <!-- Si non connecté -->
            <li class="nav-item"><a href="/register.php" class="btn btn-outline-dark me-2">Sign In</a></li>
            <li class="nav-item"><a href="/login.php" class="btn btn-dark me-2">Login</a></li>
          <?php endif; ?>

          <!-- Icône panier -->
          <li class="nav-item">
            <?php if ($authenticated): ?>
              <a href="/panier.php" class="nav-link text-dark">
                <img src="/image/panier_icon.png" alt="Panier" width="24" height="24" />
              </a>
            <?php else: ?>
              <img src="/image/panier_icon.png" alt="Panier" width="24" height="24"
                   style="opacity: 0.5; cursor: not-allowed;" title="Please log in to access your panier" />
            <?php endif; ?>
          </li>
          </ul>
          </div>
      </div>
    </nav>

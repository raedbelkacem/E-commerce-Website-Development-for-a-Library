<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>ElectroShop - Accueil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="css/style.css" />
</head>
<body>

<?php include 'header.php'; ?>

<!-- Hero Section -->
<div class="container mt-4">
  <div class="jumbotron text-center p-5 bg-light rounded">
    <h1>Bienvenue chez ElectroShop</h1>
    <p>Tout pour vos gadgets électroniques au meilleur prix !</p>
    <a href="shop.php" class="btn btn-primary btn-lg">Voir la boutique</a>
  </div>
</div>

<!-- Featured Products -->
<div class="container mt-5">
  <h2 class="mb-4">Produits phares</h2>
  <div class="row">

    <!-- Exemple produit -->
    <div class="col-md-3">
      <div class="card">
        <img src="images/smartphone.jpg" class="card-img-top" alt="Smartphone" />
        <div class="card-body">
          <h5 class="card-title">Smartphone XYZ</h5>
          <p class="card-text">Puissant, design moderne et à petit prix.</p>
          <a href="shop.php" class="btn btn-success">Acheter</a>
        </div>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card">
        <img src="images/headphones.jpg" class="card-img-top" alt="Casque audio" />
        <div class="card-body">
          <h5 class="card-title">Casque Audio ABC</h5>
          <p class="card-text">Son clair et confortable pour vos oreilles.</p>
          <a href="shop.php" class="btn btn-success">Acheter</a>
        </div>
      </div>
    </div>

    <!-- Ajoute d'autres produits ici -->

  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

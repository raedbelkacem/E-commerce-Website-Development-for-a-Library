<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Catégories - ElectroShop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>

<?php include 'header.php'; ?>

<div class="container mt-4">
  <h1>Catégories</h1>
  <div class="list-group">

    <a href="shop.php?category=smartphones" class="list-group-item list-group-item-action">Smartphones</a>
    <a href="shop.php?category=laptops" class="list-group-item list-group-item-action">Ordinateurs Portables</a>
    <a href="shop.php?category=headphones" class="list-group-item list-group-item-action">Casques Audio</a>
    <a href="shop.php?category=cameras" class="list-group-item list-group-item-action">Caméras</a>

  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

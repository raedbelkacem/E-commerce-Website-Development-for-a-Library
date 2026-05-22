<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Boutique - ElectroShop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        .hero-banner {
            background: url('images/shop-hero.jpg') no-repeat center center;
            background-size: cover;
            height: 300px;
            position: relative;
            color: white;
            margin-bottom: 30px;
        }
        .hero-content {
            position: absolute;
            top: 50%;
            left: 10%;
            transform: translateY(-50%);
            max-width: 500px;
        }
        .category-filter {
            margin-bottom: 20px;
        }
        .deal-card {
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: transform 0.3s ease;
        }
        .deal-card:hover {
            transform: translateY(-10px);
        }
        .deal-card img {
            width: 100%;
            height: 180px;
            object-fit: cover;
        }
        .deal-card-body {
            padding: 15px;
        }
        .product-card {
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        .product-card:hover {
            transform: translateY(-10px);
        }
        .product-card img {
            width: 100%;
            height: 180px;
            object-fit: cover;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
        }
        .product-card-body {
            padding: 15px;
        }
    </style>
</head>
<body>

<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

include 'header.php'; 
?>

<div class="container">

    <!-- Hero Banner -->
    <div class="hero-banner mb-4">
        <div class="hero-content">
            <h1>Latest trending Electronic items</h1>
            <p>Find more for less on featured brands.</p>
            <a href="shop.php" class="btn btn-primary btn-lg">Shop Now</a>
        </div>
    </div>

    <!-- Category Filter and Search -->
    <div class="row category-filter mb-4">
        <div class="col-md-4">
            <select class="form-select" aria-label="Category select">
                <option selected>All Categories</option>
                <option value="1">Smart watches</option>
                <option value="2">Laptops</option>
                <option value="3">GoPro cameras</option>
                <option value="4">Headphones</option>
                <option value="5">Canon cameras</option>
            </select>
        </div>
        <div class="col-md-8">
            <div class="input-group">
                <input type="text" class="form-control" placeholder="Search products..." aria-label="Search products" />
                <button class="btn btn-outline-secondary" type="button">Search</button>
            </div>
        </div>
    </div>

    <!-- Deals and Offers -->
    <h2 class="mb-3">Deals and Offers</h2>
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="deal-card">
                <img src="images/deal1.jpg" alt="Deal 1" />
                <div class="deal-card-body">
                    <h5>Smart watches</h5>
                    <p>Up to 40% off</p>
                    <p><small>Ends in: 04d 12h 30m 15s</small></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="deal-card">
                <img src="images/deal2.jpg" alt="Deal 2" />
                <div class="deal-card-body">
                    <h5>Laptops</h5>
                    <p>Up to 35% off</p>
                    <p><small>Ends in: 02d 08h 15m 40s</small></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="deal-card">
                <img src="images/deal3.jpg" alt="Deal 3" />
                <div class="deal-card-body">
                    <h5>GoPro cameras</h5>
                    <p>Up to 30% off</p>
                    <p><small>Ends in: 05d 10h 20m 30s</small></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Product Categories -->
    <h2 class="mb-3">Product Categories</h2>
    <div class="row g-4">
        <div class="col-md-3">
            <div class="product-card">
                <img src="images/smartwatch.jpg" alt="Smart watches" />
                <div class="product-card-body">
                    <h5>Smart watches</h5>
                    <p>Latest models with advanced features.</p>
                    <a href="#" class="btn btn-primary">Shop Now</a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="product-card">
                <img src="images/laptop.jpg" alt="Laptops" />
                <div class="product-card-body">
                    <h5>Laptops</h5>
                    <p>High performance and portability.</p>
                    <a href="#" class="btn btn-primary">Shop Now</a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="product-card">
                <img src="images/gopro.jpg" alt="GoPro cameras" />
                <div class="product-card-body">
                    <h5>GoPro cameras</h5>
                    <p>Capture your adventures in HD.</p>
                    <a href="#" class="btn btn-primary">Shop Now</a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="product-card">
                <img src="images/headphones.jpg" alt="Headphones" />
                <div class="product-card-body">
                    <h5>Headphones</h5>
                    <p>Clear sound and comfortable fit.</p>
                    <a href="#" class="btn btn-primary">Shop Now</a>
                </div>
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

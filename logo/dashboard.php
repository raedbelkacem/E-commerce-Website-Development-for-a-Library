<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

include 'header.php';
?>

<div class="container mt-5">
    <h1>Tableau de bord</h1>
    <p>Bienvenue, <strong><?= htmlspecialchars($_SESSION['username']) ?></strong> !</p>

    <div class="card mt-4">
        <div class="card-body">
            <h5 class="card-title">Statistiques du compte</h5>
            <ul>
                <li>Email : <?= isset($_SESSION['email']) ? htmlspecialchars($_SESSION['email']) : 'Non renseigné' ?></li>
                <li>Date d'inscription : <?= isset($_SESSION['created_at']) ? htmlspecialchars($_SESSION['created_at']) : 'Inconnue' ?></li>
                <li>Nombre de commandes : 5 <!-- Exemple, remplace par données réelles --></li>
            </ul>
        </div>
    </div>

    <a href="logout.php" class="btn btn-danger mt-4">Se déconnecter</a>
</div>

<?php include 'footer.php'; ?>

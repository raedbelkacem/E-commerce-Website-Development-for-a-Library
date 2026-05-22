<?php
include 'header.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name']);
    $email   = trim($_POST['email']);
    $message = trim($_POST['message']);

    if ($name && filter_var($email, FILTER_VALIDATE_EMAIL) && $message) {
        $to      = 'contact@electroshop.com'; // Mets ton email ici
        $subject = "Message du site de $name";
        $body    = "Nom : $name\nEmail : $email\n\nMessage :\n$message";
        $headers = "From: $email\r\nReply-To: $email";

        if (mail($to, $subject, $body, $headers)) {
            $success = "Merci pour votre message. Nous vous répondrons bientôt.";
        } else {
            $error = "Une erreur est survenue lors de l'envoi du message.";
        }
    } else {
        $error = "Veuillez remplir correctement tous les champs.";
    }
}
?>

<div class="container mt-4">
  <h1>Contactez-nous</h1>

  <?php if ($success): ?>
    <div class="alert alert-success"><?= $success ?></div>
  <?php elseif ($error): ?>
    <div class="alert alert-danger"><?= $error ?></div>
  <?php endif; ?>

  <form method="post" action="">
    <div class="mb-3">
      <label for="name" class="form-label">Nom</label>
      <input type="text" name="name" id="name" class="form-control" required value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
    </div>

    <div class="mb-3">
      <label for="email" class="form-label">Email</label>
      <input type="email" name="email" id="email" class="form-control" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
    </div>

    <div class="mb-3">
      <label for="message" class="form-label">Message</label>
      <textarea name="message" id="message" rows="5" class="form-control" required><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
    </div>

    <button type="submit" class="btn btn-primary">Envoyer</button>
  </form>
</div>

<?php include 'footer.php'; ?>

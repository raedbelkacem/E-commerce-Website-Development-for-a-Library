<?php
include "layout/header.php";
?>

<div class="container py-5">
    <h1>Contact Us</h1>
    <p>If you have any questions or need assistance, please feel free to contact us using the information below or by filling out the contact form.</p>

    <div class="row">
        <div class="col-md-6">
            <h3>Contact Information</h3>
            <ul class="list-unstyled">
                <li><strong>Address:</strong> 123 Main Street, City, Country</li>
                <li><strong>Phone:</strong> +1 234 567 890</li>
                <li><strong>Email:</strong> support@example.com</li>
            </ul>
        </div>
        <div class="col-md-6">
            <h3>Contact Form</h3>
            <form method="post" action="#">
                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input class="form-control" id="name" name="name" type="text" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input class="form-control" id="email" name="email" type="email" required>
                </div>
                <div class="mb-3">
                    <label for="message" class="form-label">Message</label>
                    <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Send</button>
            </form>
        </div>
    </div>
</div>

<?php
include "layout/footer.php";
?>

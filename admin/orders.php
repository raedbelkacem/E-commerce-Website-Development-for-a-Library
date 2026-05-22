<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: /login.php");
    exit;
}

include "../layout/header.php";

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "log_page";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle order status update or delete if requested via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_status'])) {
        $order_id = intval($_POST['order_id']);
        $new_status = $_POST['status'];
        $allowed_statuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled', 'returned', 'refunded'];
        if (in_array($new_status, $allowed_statuses)) {
            $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
            $stmt->bind_param("si", $new_status, $order_id);
            $stmt->execute();
            $stmt->close();
        }
    } elseif (isset($_POST['delete_order'])) {
        $order_id = intval($_POST['order_id']);
        $stmt = $conn->prepare("DELETE FROM orders WHERE id = ?");
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        $stmt->close();
    }
}

// Fetch orders
$result = $conn->query("SELECT * FROM orders ORDER BY created_at DESC");
$orders = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
}
$conn->close();
?>

<div class="container py-5">
    <h2>Manage Orders</h2>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Order ID</th><th>User ID</th><th>Status</th><th>Created At</th><th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $order): ?>
            <tr>
                <td><?= htmlspecialchars($order['id']) ?></td>
                <td><?= htmlspecialchars($order['user_id']) ?></td>
                <td><?= htmlspecialchars($order['status']) ?></td>
                <td><?= htmlspecialchars($order['created_at']) ?></td>
                <td>
                    <form method="POST" style="display:inline-block; margin-right: 5px;">
                        <input type="hidden" name="order_id" value="<?= htmlspecialchars($order['id']) ?>">
                        <select name="status" class="form-select form-select-sm" style="width: auto; display: inline-block;">
                            <?php
                            $statuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled', 'returned', 'refunded'];
                            foreach ($statuses as $status_option) {
                                $selected = ($order['status'] === $status_option) ? 'selected' : '';
                                echo "<option value=\"" . htmlspecialchars($status_option) . "\" $selected>" . ucfirst($status_option) . "</option>";
                            }
                            ?>
                        </select>
                        <button type="submit" name="update_status" class="btn btn-primary btn-sm">Update</button>
                    </form>
                    <form method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure you want to delete this order?');">
                        <input type="hidden" name="order_id" value="<?= htmlspecialchars($order['id']) ?>">
                        <button type="submit" name="delete_order" class="btn btn-danger btn-sm">Delete</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include "../layout/footer.php"; ?>

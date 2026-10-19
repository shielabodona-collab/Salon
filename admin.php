
<?php
require 'db.php';
requireAdmin();

$pageTitle = 'Admin Dashboard';
$error = '';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'save_service' || $action === 'save_product') {
        $id = filter_var($_POST['id'] ?? '0', FILTER_VALIDATE_INT);
        $id = $id ?: 0;
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $price = filter_var($_POST['price'] ?? '', FILTER_VALIDATE_FLOAT);
        $table = $action === 'save_service' ? 'services' : 'products';

        if (!preg_match('/^[A-Za-z0-9 &(),.\'-]{2,120}$/', $name)) {
            $error = 'Enter a valid item name.';
        } elseif ($price === false || $price < 0 || $price > 1000000) {
            $error = 'Enter a valid non-negative price.';
        } elseif (strlen($description) > 2000) {
            $error = 'Description is too long.';
        } else {
            if ($table === 'services') {
                $duration = filter_var($_POST['duration'] ?? '', FILTER_VALIDATE_INT);
                if ($duration === false || $duration < 1 || $duration > 600) {
                    $error = 'Duration must be between 1 and 600 minutes.';
                } else {
                    if ($id) {
                        $stmt = mysqli_prepare($conn,
                            "UPDATE services SET name=?, description=?, price=?, duration=? WHERE id=?");
                        mysqli_stmt_bind_param($stmt, "ssdii",
                            $name, $description, $price, $duration, $id);
                    } else {
                        $stmt = mysqli_prepare($conn,
                            "INSERT INTO services (name, description, price, duration) VALUES (?,?,?,?)");
                        mysqli_stmt_bind_param($stmt, "ssdi",
                            $name, $description, $price, $duration);
                    }
                }
            } else {
                $stock = filter_var($_POST['stock'] ?? '', FILTER_VALIDATE_INT);
                if ($stock === false || $stock < 0 || $stock > 1000000) {
                    $error = 'Stock must be a non-negative whole number.';
                } else {
                    if ($id) {
                        $stmt = mysqli_prepare($conn,
                            "UPDATE products SET name=?, description=?, price=?, stock=? WHERE id=?");
                        mysqli_stmt_bind_param($stmt, "ssdii",
                            $name, $description, $price, $stock, $id);
                    } else {
                        $stmt = mysqli_prepare($conn,
                            "INSERT INTO products (name, description, price, stock) VALUES (?,?,?,?)");
                        mysqli_stmt_bind_param($stmt, "ssdi",
                            $name, $description, $price, $stock);
                    }
                }
            }

            if (!$error && isset($stmt) && mysqli_stmt_execute($stmt)) {
                $message = 'Changes saved successfully.';
            } elseif (!$error) {
                $error = 'Unable to save changes. Check whether this item is referenced by a booking.';
            }
        }
    } elseif ($action === 'delete_service' || $action === 'delete_product') {
        $id = filter_var($_POST['id'] ?? '', FILTER_VALIDATE_INT);
        $table = $action === 'delete_service' ? 'services' : 'products';

        if (!$id || $id < 1) {
            $error = 'Invalid record.';
        } else {
            $stmt = mysqli_prepare($conn, "DELETE FROM $table WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "i", $id);
            if (mysqli_stmt_execute($stmt)) {
                $message = mysqli_stmt_affected_rows($stmt)
                    ? 'Record deleted.' : 'Record not found.';
            } else {
                $error = 'Cannot delete this record. A booking may reference this service.';
            }
        }
    } elseif ($action === 'booking_status') {
        $id = filter_var($_POST['id'] ?? '', FILTER_VALIDATE_INT);
        $status = $_POST['status'] ?? '';
        $allowed = ['Pending','Confirmed','Completed','Cancelled'];

        if (!$id || !in_array($status, $allowed, true)) {
            $error = 'Invalid booking status.';
        } else {
            $stmt = mysqli_prepare($conn,
                "UPDATE bookings SET status=? WHERE id=?");
            mysqli_stmt_bind_param($stmt, "si", $status, $id);
            if (mysqli_stmt_execute($stmt)) {
                $message = 'Booking status updated.';
            } else {
                $error = 'Unable to update booking.';
            }
        }
    }
}

$editService = null;
$editProduct = null;
$editType = $_GET['edit_type'] ?? '';
$editId = filter_var($_GET['edit'] ?? '', FILTER_VALIDATE_INT);

if ($editId && $editId > 0 && in_array($editType, ['service','product'], true)) {
    $table = $editType === 'service' ? 'services' : 'products';
    $stmt = mysqli_prepare($conn, "SELECT * FROM $table WHERE id=?");
    mysqli_stmt_bind_param($stmt, "i", $editId);
    mysqli_stmt_execute($stmt);
    $record = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    if ($editType === 'service') $editService = $record;
    else $editProduct = $record;
}

$services = mysqli_query($conn, "SELECT * FROM services ORDER BY id DESC");
$products = mysqli_query($conn, "SELECT * FROM products ORDER BY id DESC");
$bookings = mysqli_query($conn,
    "SELECT b.*, u.name AS customer_name, u.email,
            s.name AS service_name
     FROM bookings b
     JOIN users u ON b.user_id = u.id
     JOIN services s ON b.service_id = s.id
     ORDER BY b.booking_date DESC, b.booking_time DESC");
$customers = mysqli_query($conn,
    "SELECT id, name, email, phone, created_at
     FROM users WHERE role='user' ORDER BY id DESC");
$serviceCount = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM services"));
$productCount = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM products"));
$bookingCount = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM bookings"));

require 'header.php';
?>
<section class="section">
    <div class="section-heading">
        <h2>Admin Dashboard ♡</h2>
        <p>Manage your salon, services, products, and appointments.</p>
    </div>

    <?php if ($error): ?><div class="error"><?= e($error) ?></div><?php endif; ?>
    <?php if ($message): ?><div class="success"><?= e($message) ?></div><?php endif; ?>

    <div class="stat-grid">
        <div class="card">
            <div class="muted">Services</div>
            <div class="stat-number"><?= $serviceCount ?></div>
        </div>
        <div class="card">
            <div class="muted">Products</div>
            <div class="stat-number"><?= $productCount ?></div>
        </div>
        <div class="card">
            <div class="muted">Bookings</div>
            <div class="stat-number"><?= $bookingCount ?></div>
        </div>
    </div>
</section>

<section class="section">
    <div class="section-heading"><h2>Manage Services</h2></div>
    <div class="form-wrap">
        <form class="form-card" method="POST">
            <h2><?= $editService ? 'Edit Service' : 'Add a Service' ?></h2>
            <input type="hidden" name="action" value="save_service">
            <input type="hidden" name="id" value="<?= (int)($editService['id'] ?? 0) ?>">
            <div class="field">
                <label>Service Name</label>
                <input name="name" required maxlength="120"
                       value="<?= e($editService['name'] ?? '') ?>">
            </div>
            <div class="field">
                <label>Description</label>
                <textarea name="description" maxlength="2000"><?= e($editService['description'] ?? '') ?></textarea>
            </div>
            <div class="field">
                <label>Price (₱)</label>
                <input type="number" name="price" min="0" max="1000000"
                       step="0.01" required value="<?= e($editService['price'] ?? '') ?>">
            </div>
            <div class="field">
                <label>Duration (minutes)</label>
                <input type="number" name="duration" min="1" max="600"
                       required value="<?= e($editService['duration'] ?? '30') ?>">
            </div>
            <button class="btn">Save Service</button>
            <?php if ($editService): ?><p><a href="admin.php">Cancel editing</a></p><?php endif; ?>
        </form>
    </div>

    <div class="table-wrap">
        <table>
            <thead><tr><th>Name</th><th>Price</th><th>Duration</th><th>Actions</th></tr></thead>
            <tbody>
            <?php while ($s = mysqli_fetch_assoc($services)): ?>
                <tr>
                    <td><?= e($s['name']) ?></td>
                    <td>₱<?= number_format($s['price'], 2) ?></td>
                    <td><?= (int)$s['duration'] ?> min</td>
                    <td>
                        <a class="btn btn-small" href="admin.php?edit_type=service&edit=<?= (int)$s['id'] ?>">Edit</a>
                        <form class="inline-form" method="POST"
                              onsubmit="return confirm('Delete this service?')">
                            <input type="hidden" name="action" value="delete_service">
                            <input type="hidden" name="id" value="<?= (int)$s['id'] ?>">
                            <button class="btn btn-danger btn-small">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</section>

<section class="section">
    <div class="section-heading"><h2>Manage Products</h2></div>
    <div class="form-wrap">
        <form class="form-card" method="POST">
            <h2><?= $editProduct ? 'Edit Product' : 'Add a Product' ?></h2>
            <input type="hidden" name="action" value="save_product">
            <input type="hidden" name="id" value="<?= (int)($editProduct['id'] ?? 0) ?>">
            <div class="field">
                <label>Product Name</label>
                <input name="name" required maxlength="120"
                       value="<?= e($editProduct['name'] ?? '') ?>">
            </div>
            <div class="field">
                <label>Description</label>
                <textarea name="description" maxlength="2000"><?= e($editProduct['description'] ?? '') ?></textarea>
            </div>
            <div class="field">
                <label>Price (₱)</label>
                <input type="number" name="price" min="0" max="1000000"
                       step="0.01" required value="<?= e($editProduct['price'] ?? '') ?>">
            </div>
            <div class="field">
                <label>Stock Quantity</label>
                <input type="number" name="stock" min="0" max="1000000"
                       required value="<?= e($editProduct['stock'] ?? '0') ?>">
            </div>
            <button class="btn">Save Product</button>
            <?php if ($editProduct): ?><p><a href="admin.php">Cancel editing</a></p><?php endif; ?>
        </form>
    </div>

    <div class="table-wrap">
        <table>
            <thead><tr><th>Product</th><th>Price</th><th>Stock</th><th>Actions</th></tr></thead>
            <tbody>
            <?php while ($p = mysqli_fetch_assoc($products)): ?>
                <tr>
                    <td><?= e($p['name']) ?></td>
                    <td>₱<?= number_format($p['price'], 2) ?></td>
                    <td><?= (int)$p['stock'] ?></td>
                    <td>
                        <a class="btn btn-small" href="admin.php?edit_type=product&edit=<?= (int)$p['id'] ?>">Edit</a>
                        <form class="inline-form" method="POST"
                              onsubmit="return confirm('Delete this product?')">
                            <input type="hidden" name="action" value="delete_product">
                            <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                            <button class="btn btn-danger btn-small">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</section>

<section class="section">
    <div class="section-heading"><h2>Manage Appointments</h2></div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr><th>Customer</th><th>Service</th><th>Date / Time</th><th>Status</th><th>Update</th></tr>
            </thead>
            <tbody>
            <?php if (mysqli_num_rows($bookings) === 0): ?>
                <tr><td colspan="5">No bookings yet.</td></tr>
            <?php endif; ?>
            <?php while ($b = mysqli_fetch_assoc($bookings)): ?>
                <tr>
                    <td><?= e($b['customer_name']) ?><br><small><?= e($b['email']) ?></small></td>
                    <td><?= e($b['service_name']) ?></td>
                    <td><?= e($b['booking_date']) ?><br><?= e(substr($b['booking_time'],0,5)) ?></td>
                    <td><span class="pill"><?= e($b['status']) ?></span></td>
                    <td>
                        <form method="POST" class="inline-form">
                            <input type="hidden" name="action" value="booking_status">
                            <input type="hidden" name="id" value="<?= (int)$b['id'] ?>">
                            <select name="status" aria-label="Booking status">
                                <?php foreach (['Pending','Confirmed','Completed','Cancelled'] as $status): ?>
                                    <option value="<?= e($status) ?>"
                                        <?= $b['status'] === $status ? 'selected' : '' ?>>
                                        <?= e($status) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <button class="btn btn-small">Save</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</section>

<section class="section">
    <div class="section-heading"><h2>Registered Customers</h2></div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>Joined</th></tr></thead>
            <tbody>
            <?php while ($c = mysqli_fetch_assoc($customers)): ?>
                <tr>
                    <td><?= e($c['name']) ?></td>
                    <td><?= e($c['email']) ?></td>
                    <td><?= e($c['phone']) ?></td>
                    <td><?= e($c['created_at']) ?></td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</section>

<?php require 'footer.php'; ?>


<?php
require 'db.php';
requireLogin();
$pageTitle = 'My Bookings';
$userId = (int)$_SESSION['user_id'];
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bookingId = filter_var($_POST['booking_id'] ?? '', FILTER_VALIDATE_INT);

    if ($bookingId) {
        $stmt = mysqli_prepare($conn,
            "UPDATE bookings SET status = 'Cancelled'
             WHERE id = ? AND user_id = ?
             AND status IN ('Pending','Confirmed')");
        mysqli_stmt_bind_param($stmt, "ii", $bookingId, $userId);
        mysqli_stmt_execute($stmt);
        $message = mysqli_stmt_affected_rows($stmt) > 0
            ? 'Your booking has been cancelled.'
            : 'That booking cannot be cancelled.';
    }
}

$stmt = mysqli_prepare($conn,
    "SELECT b.*, s.name AS service_name, s.price
     FROM bookings b JOIN services s ON b.service_id = s.id
     WHERE b.user_id = ? ORDER BY b.booking_date DESC, b.booking_time DESC");
mysqli_stmt_bind_param($stmt, "i", $userId);
mysqli_stmt_execute($stmt);
$bookings = mysqli_stmt_get_result($stmt);

require 'header.php';
?>
<section class="section">
    <div class="section-heading">
        <h2>My Beauty Appointments ♡</h2>
        <p>Welcome, <?= e($_SESSION['name']) ?>!</p>
    </div>

    <?php if (isset($_GET['booked'])): ?>
        <div class="success">Your booking request was saved successfully.</div>
    <?php endif; ?>
    <?php if ($message): ?>
        <div class="notice"><?= e($message) ?></div>
    <?php endif; ?>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Service</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php if (mysqli_num_rows($bookings) === 0): ?>
                <tr><td colspan="6">No appointments yet. Book your first visit!</td></tr>
            <?php endif; ?>
            <?php while ($b = mysqli_fetch_assoc($bookings)): ?>
                <tr>
                    <td><?= e($b['service_name']) ?></td>
                    <td><?= e($b['booking_date']) ?></td>
                    <td><?= e(substr($b['booking_time'], 0, 5)) ?></td>
                    <td>₱<?= number_format($b['price'], 2) ?></td>
                    <td><span class="pill"><?= e($b['status']) ?></span></td>
                    <td>
                    <?php if (in_array($b['status'], ['Pending','Confirmed'], true)): ?>
                        <form method="POST" class="inline-form"
                              onsubmit="return confirm('Cancel this booking?')">
                            <input type="hidden" name="booking_id"
                                   value="<?= (int)$b['id'] ?>">
                            <button class="btn btn-danger btn-small">Cancel</button>
                        </form>
                    <?php else: ?>
                        —
                    <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <p style="margin-top:24px"><a class="btn" href="book.php">Book Another Appointment</a></p>
</section>
<?php require 'footer.php'; ?>

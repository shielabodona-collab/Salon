
<?php
require 'db.php';
$pageTitle = 'Book an Appointment';
requireLogin();

$errors = [];
$success = '';

$services = mysqli_query($conn, "SELECT id, name, price FROM services ORDER BY name");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $serviceId = filter_var($_POST['service_id'] ?? '', FILTER_VALIDATE_INT);
    $date = $_POST['booking_date'] ?? '';
    $time = $_POST['booking_time'] ?? '';
    $notes = trim($_POST['notes'] ?? '');

    $validDate = DateTime::createFromFormat('!Y-m-d', $date);
    $validTime = DateTime::createFromFormat('!H:i', $time);

    if (!$serviceId || $serviceId < 1) {
        $errors[] = 'Please select a valid service.';
    }
    if (!$validDate || $validDate->format('Y-m-d') !== $date ||
        $date < date('Y-m-d')) {
        $errors[] = 'Please choose today or a future date.';
    }
    if (!$validTime || $validTime->format('H:i') !== $time) {
        $errors[] = 'Please select a valid time.';
    } elseif ($date === date('Y-m-d') && $time <= date('H:i')) {
        $errors[] = 'Please choose a future time.';
    }
    if (strlen($notes) > 1000) {
        $errors[] = 'Notes must be under 1000 characters.';
    }

    if (!$errors) {
        $check = mysqli_prepare($conn, "SELECT id FROM services WHERE id = ?");
        mysqli_stmt_bind_param($check, "i", $serviceId);
        mysqli_stmt_execute($check);

        if (!mysqli_fetch_assoc(mysqli_stmt_get_result($check))) {
            $errors[] = 'The selected service does not exist.';
        } else {
            $userId = (int)$_SESSION['user_id'];
            $stmt = mysqli_prepare($conn,
                "INSERT INTO bookings
                 (user_id, service_id, booking_date, booking_time, notes)
                 VALUES (?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "iisss",
                $userId, $serviceId, $date, $time, $notes);

            if (mysqli_stmt_execute($stmt)) {
                header('Location: my-bookings.php?booked=1');
                exit;
            }
            $errors[] = 'Unable to save your booking.';
        }
    }
}

require 'header.php';
?>
<section class="form-wrap">
    <form class="form-card" method="POST">
        <h1>Book Your Beauty Session ♡</h1>
        <p class="muted">Choose your treatment and preferred appointment time.</p>

        <?php foreach ($errors as $error): ?>
            <div class="error"><?= e($error) ?></div>
        <?php endforeach; ?>

        <div class="field">
            <label for="service_id">Salon Service</label>
            <select id="service_id" name="service_id" required>
                <option value="">Choose a service</option>
                <?php while ($s = mysqli_fetch_assoc($services)): ?>
                    <option value="<?= (int)$s['id'] ?>"
                        <?= (string)($_POST['service_id'] ?? '') === (string)$s['id'] ? 'selected' : '' ?>>
                        <?= e($s['name']) ?> — ₱<?= number_format($s['price'], 2) ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="field">
            <label for="booking_date">Appointment Date</label>
            <input id="booking_date" type="date" name="booking_date"
                   min="<?= date('Y-m-d') ?>" required
                   value="<?= e($_POST['booking_date'] ?? '') ?>">
        </div>
        <div class="field">
            <label for="booking_time">Appointment Time</label>
            <input id="booking_time" type="time" name="booking_time"
                   min="09:00" max="18:00" required
                   value="<?= e($_POST['booking_time'] ?? '') ?>">
            <small>Preferred hours: 9:00 AM–6:00 PM.</small>
        </div>
        <div class="field">
            <label for="notes">Notes (optional)</label>
            <textarea id="notes" name="notes" maxlength="1000"
                placeholder="Tell us about your preferred style..."><?= e($_POST['notes'] ?? '') ?></textarea>
        </div>
        <button class="btn" type="submit">Confirm Booking Request</button>
    </form>
</section>
<?php require 'footer.php'; ?>

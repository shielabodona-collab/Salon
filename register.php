
<?php
require 'db.php';

$pageTitle = 'Create Account';
$errors = [];
$name = '';
$phone = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if (!preg_match('/^[A-Za-z ]{2,100}$/', $name))
        $errors[] = 'Name must contain letters and spaces only.';
    if (!preg_match('/^[0-9]{10,15}$/', $phone))
        $errors[] = 'Phone must contain 10 to 15 digits.';
    if (!preg_match('/^[A-Za-z0-9._%+-]+@gmail\.com$/i', $email))
        $errors[] = 'Only valid @gmail.com email addresses are allowed.';
    if (!preg_match('/^(?=.*[A-Za-z])(?=.*[0-9])(?=.*[^A-Za-z0-9\s])\S{8,}$/', $password))
        $errors[] = 'Password needs 8+ characters, a letter, a number, and a special character.';
    if ($password !== $confirm)
        $errors[] = 'Passwords do not match.';

    if (!$errors) {
        $check = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ?");
        mysqli_stmt_bind_param($check, "s", $email);
        mysqli_stmt_execute($check);
        $existing = mysqli_stmt_get_result($check);

        if (mysqli_num_rows($existing) > 0) {
            $errors[] = 'This email is already registered.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = mysqli_prepare($conn,
                "INSERT INTO users (name, phone, email, password, role)
                 VALUES (?, ?, ?, ?, 'user')");
            mysqli_stmt_bind_param($stmt, "ssss", $name, $phone, $email, $hash);

            if (mysqli_stmt_execute($stmt)) {
                header('Location: login.php?registered=1');
                exit;
            }
            $errors[] = 'Registration failed. Please try again.';
        }
    }
}

require 'header.php';
?>
<section class="form-wrap">
    <form class="form-card" method="POST" data-validate>
        <h1>Create Your Account ♡</h1>
        <p class="muted">Join Pink Petals Salon for your next beauty day.</p>

        <?php foreach ($errors as $error): ?>
            <div class="error"><?= e($error) ?></div>
        <?php endforeach; ?>

        <div class="field">
            <label for="name">Full Name</label>
            <input id="name" name="name" required maxlength="100"
                   pattern="[A-Za-z ]{2,100}" value="<?= e($name) ?>">
        </div>
        <div class="field">
            <label for="phone">Phone Number</label>
            <input id="phone" name="phone" type="tel" required
                   inputmode="numeric" pattern="[0-9]{10,15}"
                   maxlength="15" value="<?= e($phone) ?>">
        </div>
        <div class="field">
            <label for="email">Gmail Address</label>
            <input id="email" name="email" type="email" required
                   pattern="[A-Za-z0-9._%+-]+@gmail\.com"
                   value="<?= e($email) ?>">
        </div>
        <div class="field">
            <label for="password">Password</label>
            <input id="password" name="password" type="password"
                   required minlength="8" autocomplete="new-password">
            <small>Use letters, numbers, and a special character.</small>
        </div>
        <div class="field">
            <label for="confirm_password">Confirm Password</label>
            <input id="confirm_password" name="confirm_password"
                   type="password" required autocomplete="new-password">
        </div>
        <button class="btn" type="submit">Create Account</button>
        <p>Already registered? <a href="login.php">Log in here</a>.</p>
    </form>
</section>
<?php require 'footer.php'; ?>

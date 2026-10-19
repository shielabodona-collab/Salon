
<?php
require 'db.php';
$pageTitle = 'Login';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!preg_match('/^[A-Za-z0-9._%+-]+@gmail\.com$/i', $email)) {
        $error = 'Please enter a valid @gmail.com email.';
    } else {
        $stmt = mysqli_prepare($conn,
            "SELECT id, name, email, password, role FROM users WHERE email = ?");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);

        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $user['role'];

            if ($user['role'] === 'admin') {
                header('Location: admin.php');
            } else {
                header('Location: index.php');
            }
            exit;
        }
        $error = 'Incorrect email or password.';
    }
}

require 'header.php';
?>
<section class="form-wrap">
    <form class="form-card" method="POST" data-validate>
        <h1>Welcome Back ♡</h1>
        <?php if (isset($_GET['registered'])): ?>
            <div class="success">Account created! You can now log in.</div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="error"><?= e($error) ?></div>
        <?php endif; ?>

        <div class="field">
            <label for="email">Gmail Address</label>
            <input id="email" type="email" name="email" required
                   pattern="[A-Za-z0-9._%+-]+@gmail\.com">
        </div>
        <div class="field">
            <label for="password">Password</label>
            <input id="password" type="password" name="password"
                   required autocomplete="current-password">
        </div>
        <button class="btn" type="submit">Log In</button>
        <p>New here? <a href="register.php">Create an account</a>.</p>
    </form>
</section>
<?php require 'footer.php'; ?>

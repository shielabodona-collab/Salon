
<?php
if (!isset($pageTitle)) {
    $pageTitle = "Pink Petals Salon";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> | Pink Petals Salon</title>
    <link rel="stylesheet" href="style.css">
    <script src="validation.js" defer></script>
</head>
<body>
<header class="site-header">
    <a class="brand" href="index.php">♡ Pink Petals</a>
    <button class="menu-toggle" id="menuToggle"
        aria-label="Toggle navigation" aria-expanded="false">☰</button>
    <nav id="mainNav" class="main-nav">
        <a href="index.php">Home</a>
        <a href="book.php">Book Now</a>
        <a href="products.php">Products</a>
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="my-bookings.php">My Bookings</a>
            <?php if ($_SESSION['role'] === 'admin'): ?>
                <a href="admin.php">Admin</a>
            <?php endif; ?>
            <a href="logout.php">Logout</a>
        <?php else: ?>
            <a href="login.php">Login</a>
            <a class="nav-cta" href="register.php">Join Us</a>
        <?php endif; ?>
    </nav>
</header>
<main>

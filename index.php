
<?php
require 'db.php';
$pageTitle = 'Home';

$services = mysqli_query($conn, "SELECT * FROM services ORDER BY id DESC");
$products = mysqli_query($conn, "SELECT * FROM products ORDER BY id DESC LIMIT 3");

require 'header.php';
?>
<section class="hero">
    <h1>Your Beauty, Your Moment, Your Pink Paradise.</h1>
    <p>
        Welcome to Pink Petals Salon, your little escape for gorgeous hair,
        lovely nails, and feel-good beauty treatments.
    </p>
    <div class="hero-actions">
        <a class="btn" href="book.php">Book an Appointment ♡</a>
        <a class="btn btn-outline" href="products.php">Explore Products</a>
    </div>
</section>

<section class="section">
    <div class="section-heading">
        <h2>Our Beauty Services</h2>
        <p>A little self-care goes a long way.</p>
    </div>
    <div class="grid">
        <?php while ($service = mysqli_fetch_assoc($services)): ?>
            <article class="card">
                <div class="card-symbol">♡</div>
                <h3><?= e($service['name']) ?></h3>
                <p><?= e($service['description']) ?></p>
                <p><?= (int)$service['duration'] ?> minutes</p>
                <div class="price">₱<?= number_format($service['price'], 2) ?></div>
                <p><a class="btn" href="book.php">Book This Service</a></p>
            </article>
        <?php endwhile; ?>
    </div>
</section>

<section class="section" style="background:#fff0f6">
    <div class="section-heading">
        <h2>Pink Petals Favorites</h2>
        <p>Beauty essentials for your self-care routine.</p>
    </div>
    <div class="grid">
        <?php if (mysqli_num_rows($products) === 0): ?>
            <p>No products available yet.</p>
        <?php endif; ?>
        <?php while ($product = mysqli_fetch_assoc($products)): ?>
            <article class="card">
                <div class="card-symbol">✿</div>
                <h3><?= e($product['name']) ?></h3>
                <p><?= e($product['description']) ?></p>
                <div class="price">₱<?= number_format($product['price'], 2) ?></div>
                <p class="muted">
                    <?= (int)$product['stock'] > 0 ? 'In stock' : 'Currently unavailable' ?>
                </p>
            </article>
        <?php endwhile; ?>
    </div>
    <p style="text-align:center;margin-top:28px">
        <a class="btn" href="products.php">View All Products</a>
    </p>
</section>
<?php require 'footer.php'; ?>

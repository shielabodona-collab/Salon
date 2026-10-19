
<?php
require 'db.php';
$pageTitle = 'Beauty Products';

$products = mysqli_query($conn,
    "SELECT * FROM products ORDER BY name ASC");

require 'header.php';
?>
<section class="hero">
    <h1>Little Luxuries, Lots of Love ♡</h1>
    <p>Explore our salon's beauty essentials and self-care favorites.</p>
</section>

<section class="section">
    <div class="grid">
        <?php if (mysqli_num_rows($products) === 0): ?>
            <p>No products are available at the moment.</p>
        <?php endif; ?>

        <?php while ($p = mysqli_fetch_assoc($products)): ?>
            <article class="card">
                <div class="card-symbol">✿</div>
                <h3><?= e($p['name']) ?></h3>
                <p><?= e($p['description']) ?></p>
                <div class="price">₱<?= number_format($p['price'], 2) ?></div>
                <p class="muted">
                    <?php if ((int)$p['stock'] > 0): ?>
                        <?= (int)$p['stock'] ?> available
                    <?php else: ?>
                        Out of stock
                    <?php endif; ?>
                </p>
            </article>
        <?php endwhile; ?>
    </div>
</section>
<?php require 'footer.php'; ?>

<?php
require_once "config/database.php";

$sql = "SELECT * FROM menu_items WHERE available = 1";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>SRI LAXMI Hotel </title>

    <link rel="stylesheet" href="css/style.css">

    <!-- Elegant Google Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>

<header class="hotel-header">

    <div class="hotel-name">
        SRI LAXMI HOTEL
    </div>

    <div class="hotel-tagline">
        Delicious Food • Happy Moments
    </div>

</header>


<!-- CART BAR -->

<div class="cart-bar">

    <div class="cart-info">
        🛒 Cart
        <span id="cart-count">0</span>
    </div>

    <div class="cart-total">
        ₹<span id="cart-total">0</span>
    </div>

    <a href="cart.php" class="view-cart-btn">
        View Cart
    </a>

</div>


<main>

    <section class="welcome-section">

        <h1>💕Welcome to  SRI LAXMI Hotel 🙃</h1>

        <p>
            Fresh, delicious and affordable food made for you.
        </p>

    </section>


    <section class="menu-section">

        <h2>Our Menu</h2>

        <div class="menu-container">

            <?php while ($row = $result->fetch_assoc()): ?>

                <div class="food-card">

                    <div class="food-image-container">

                        <img
                            src="images/<?php echo htmlspecialchars($row['image']); ?>"
                            alt="<?php echo htmlspecialchars($row['name']); ?>"
                            class="food-image"
                        >

                    </div>


                    <div class="food-details">

                        <h3>
                            <?php echo htmlspecialchars($row['name']); ?>
                        </h3>

                        <div class="price">
                            ₹<?php echo number_format($row['price'], 0); ?>
                        </div>


                        <div class="quantity-control">

                            <button
                                class="quantity-btn minus-btn"
                                data-id="<?php echo $row['id']; ?>"
                            >
                                −
                            </button>

                            <span
                                class="quantity"
                                id="quantity-<?php echo $row['id']; ?>"
                            >
                                0
                            </span>

                            <button
                                class="quantity-btn plus-btn"
                                data-id="<?php echo $row['id']; ?>"
                            >
                                +
                            </button>

                        </div>


                        <button
                            class="add-cart-btn"
                            data-id="<?php echo $row['id']; ?>"
                            data-name="<?php echo htmlspecialchars($row['name']); ?>"
                            data-price="<?php echo $row['price']; ?>"
                            data-image="<?php echo htmlspecialchars($row['image']); ?>"
                        >
                            Add to Cart
                        </button>

                    </div>

                </div>

            <?php endwhile; ?>

        </div>

    </section>

</main>


<footer>

    <p>© 2026 SRI LAXMI  Hotel</p>

</footer>


<script src="js/script.js"></script>

</body>
</html>
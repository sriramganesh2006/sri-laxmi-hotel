<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Your Cart - Hotel</title>

    <link
        rel="stylesheet"
        href="css/style.css"
    >

</head>

<body>

    <!-- =====================================
         HEADER
    ====================================== -->

    <header class="header">

        <div class="container navbar">

            <a
                href="index.php"
                class="logo"
            >
                HOTEL NAME
            </a>

            <a
                href="index.php"
                class="nav-cart"
            >
                ← Menu
            </a>

        </div>

    </header>


    <!-- =====================================
         CART SECTION
    ====================================== -->

    <main>

        <section class="cart-section">

            <div class="container">

                <div class="section-title">

                    <h2>Your Cart</h2>

                    <p>
                        Review your selected food items
                    </p>

                </div>


                <!-- EMPTY CART -->

                <div
                    id="empty-cart"
                    class="empty-cart"
                >

                    <div class="empty-cart-icon">
                        🛒
                    </div>

                    <h3>
                        Your cart is empty
                    </h3>

                    <p>
                        Add some delicious food to your cart.
                    </p>

                    <a
                        href="index.php#menu"
                        class="primary-button"
                    >
                        VIEW MENU
                    </a>

                </div>


                <!-- CART CONTENT -->

                <div
                    id="cart-content"
                    class="cart-content"
                >

                    <div
                        id="cart-items"
                        class="cart-items"
                    >
                    </div>


                    <!-- CART SUMMARY -->

                    <div class="cart-summary">

                        <div class="summary-row">

                            <span>
                                Subtotal
                            </span>

                            <strong id="cart-subtotal">
                                ₹0
                            </strong>

                        </div>


                        <div class="summary-divider">
                        </div>


                        <div class="summary-row total-row">

                            <span>
                                Total
                            </span>

                            <strong id="cart-total">
                                ₹0
                            </strong>

                        </div>


                        <a
                            href="checkout.php"
                            class="checkout-button"
                        >
                            PROCEED TO CHECKOUT
                        </a>


                        <a
                            href="index.php#menu"
                            class="continue-shopping"
                        >
                            ← Continue Shopping
                        </a>

                    </div>

                </div>

            </div>

        </section>

    </main>


    <!-- =====================================
         FOOTER
    ====================================== -->

    <footer class="footer">

        <p>
            © 2026 Hotel Name. All rights reserved.
        </p>

    </footer>


    <!-- =====================================
         JAVASCRIPT
    ====================================== -->

    <script src="js/script.js"></script>

</body>

</html>
<?php

require_once "config/bootstrap.php";


/*
|--------------------------------------------------------------------------
| Get Order ID
|--------------------------------------------------------------------------
*/

$orderId = filter_input(
    INPUT_GET,
    "order_id",
    FILTER_VALIDATE_INT
);


if (!$orderId || $orderId <= 0) {

    die("Invalid order ID.");

}


/*
|--------------------------------------------------------------------------
| Get Order Details
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare(
    "SELECT
        id,
        customer_name,
        phone,
        table_number,
        total_amount,
        status,
        payment_status,
        created_at
     FROM orders
     WHERE id = ?
     LIMIT 1"
);


$stmt->bind_param(
    "i",
    $orderId
);


$stmt->execute();

$result = $stmt->get_result();

$order = $result->fetch_assoc();

$stmt->close();


/*
|--------------------------------------------------------------------------
| Check Order
|--------------------------------------------------------------------------
*/

if (!$order) {

    die("Order not found.");

}


/*
|--------------------------------------------------------------------------
| Payment Protection
|--------------------------------------------------------------------------
|
| Customer cannot see the final order confirmation
| until the admin verifies the payment.
|
*/

if ($order["payment_status"] !== "PAID") {

    header(
        "Location: payment.php?order_id=" .
        $orderId
    );

    exit;

}


/*
|--------------------------------------------------------------------------
| Get Order Items
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare(
    "SELECT
        oi.quantity,
        oi.price,
        mi.name
     FROM order_items oi
     INNER JOIN menu_items mi
        ON oi.menu_item_id = mi.id
     WHERE oi.order_id = ?"
);


$stmt->bind_param(
    "i",
    $orderId
);


$stmt->execute();

$itemsResult = $stmt->get_result();

$orderItems = [];


while ($item = $itemsResult->fetch_assoc()) {

    $orderItems[] = $item;

}


$stmt->close();

?>


<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Order Confirmation - SRI LAXMI Hotel
    </title>

    <link
        rel="stylesheet"
        href="css/style.css"
    >

</head>


<body class="success-page">


<header class="hotel-header">

    <h1>SRI LAXMI HOTEL</h1>

    <p>
        Delicious Food • Happy Moments
    </p>

</header>


<main class="success-container">


    <div class="success-card">


        <!-- Success Icon -->

        <div class="success-icon">

            ✓

        </div>


        <h2>
            Order Placed Successfully!
        </h2>


        <p class="success-message">

            Thank you for ordering from
            SRI LAXMI Hotel.

        </p>


        <!-- Order Number -->

        <div class="order-number">

            <span>
                Order Number
            </span>

            <strong>

                #<?php
                echo (int)$order["id"];
                ?>

            </strong>

        </div>


        <!-- Customer Details -->

        <div class="success-details">


            <div>

                <strong>
                    Customer Name
                </strong>

                <p>

                    <?php
                    echo htmlspecialchars(
                        $order["customer_name"]
                    );
                    ?>

                </p>

            </div>


            <div>

                <strong>
                    Phone Number
                </strong>

                <p>

                    <?php
                    echo htmlspecialchars(
                        $order["phone"]
                    );
                    ?>

                </p>

            </div>


            <div>

                <strong>
                    Table Number
                </strong>

                <p>

                    <?php
                    echo htmlspecialchars(
                        $order["table_number"]
                    );
                    ?>

                </p>

            </div>


        </div>


        <!-- Payment Status -->

        <div class="confirmed-payment">

            ✓ Payment Confirmed

        </div>


        <!-- Ordered Items -->

        <div class="success-items">


            <h3>
                Ordered Items
            </h3>


            <?php foreach (
                $orderItems as $item
            ): ?>


                <div class="success-item">


                    <span>

                        <?php
                        echo htmlspecialchars(
                            $item["name"]
                        );
                        ?>

                        ×

                        <?php
                        echo (int)$item["quantity"];
                        ?>

                    </span>


                    <strong>

                        ₹<?php

                        echo number_format(
                            (float)$item["price"] *
                            (int)$item["quantity"],
                            0
                        );

                        ?>

                    </strong>


                </div>


            <?php endforeach; ?>


        </div>


        <!-- Total -->

        <div class="success-total">


            <span>
                Total Amount
            </span>


            <strong>

                ₹<?php

                echo number_format(
                    (float)$order["total_amount"],
                    0
                );

                ?>

            </strong>


        </div>


        <!-- Order Status -->

        <div class="success-status">


            <span>
                Order Status
            </span>


            <strong>

                <?php
                echo htmlspecialchars(
                    $order["status"]
                );
                ?>

            </strong>


        </div>


        <!-- Buttons -->

        <div class="success-actions">


            <a
                href="index.php"
                class="success-button"
            >
                ← Back to Menu
            </a>


        </div>


    </div>


</main>


</body>

</html>
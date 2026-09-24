<?php

require_once "config/bootstrap.php";


$orderId = filter_input(
    INPUT_GET,
    "order_id",
    FILTER_VALIDATE_INT
);


if (!$orderId || $orderId <= 0) {
    die("Invalid order.");
}


/*
|--------------------------------------------------------------------------
| Get Order
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


if (!$order) {
    die("Order not found.");
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

$result = $stmt->get_result();

$orderItems = [];

while ($row = $result->fetch_assoc()) {
    $orderItems[] = $row;
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

    <title>Payment - SRI LAXMI Hotel</title>

    <link
        rel="stylesheet"
        href="css/style.css"
    >

</head>


<body class="payment-page">


<header class="hotel-header">

    <h1>SRI LAXMI HOTEL</h1>

    <p>Delicious Food • Happy Moments</p>

</header>


<main class="payment-container">


    <?php if ($order["payment_status"] === "PAID"): ?>

        <div class="payment-card">

            <div class="payment-success-icon">
                ✓
            </div>

            <h2>Payment Confirmed</h2>

            <p>
                Your payment has been verified by the hotel.
            </p>

            <a
                href="order-success.php?order_id=<?php echo $orderId; ?>"
                class="payment-button"
            >
                View Order Confirmation
            </a>

        </div>


    <?php else: ?>


        <div class="payment-card">


            <h2>Complete Your Payment</h2>


            <p class="payment-subtitle">
                Scan the QR code using PhonePe and pay the
                exact order amount.
            </p>


            <div class="order-number-box">

                <strong>
                    Order #<?php echo $orderId; ?>
                </strong>

            </div>


            <div class="payment-amount">

                ₹<?php echo number_format(
                    (float)$order["total_amount"],
                    2
                ); ?>

            </div>


            <div class="payment-qr-box">

                <img
                    src="images/payment-qr.png"
                    alt="PhonePe Payment QR Code"
                    class="payment-qr"
                >

            </div>


            <p class="payment-name">
                Pay to <strong>Ganesh</strong>
            </p>


            <div class="payment-instructions">

                <h3>How to Pay</h3>

                <ol>

                    <li>
                        Open PhonePe on your phone.
                    </li>

                    <li>
                        Scan the QR code above.
                    </li>

                    <li>
                        Enter the exact amount shown above.
                    </li>

                    <li>
                        Complete the payment in PhonePe.
                    </li>

                    <li>
                        Return here and click
                        <strong>I HAVE PAID</strong>.
                    </li>

                </ol>

            </div>


            <?php if ($order["payment_status"] === "VERIFYING"): ?>

                <div class="payment-waiting">

                    <strong>
                        Payment verification pending
                    </strong>

                    <p>
                        The hotel admin will verify your
                        payment in PhonePe.
                    </p>

                    <button
                        onclick="checkPaymentStatus()"
                        class="payment-button"
                    >
                        Check Payment Status
                    </button>

                </div>


            <?php else: ?>


                <button
                    onclick="markAsPaid()"
                    class="payment-button"
                    id="paid-button"
                >
                    I HAVE PAID
                </button>


                <p
                    id="payment-message"
                    class="payment-message"
                ></p>


            <?php endif; ?>


            <a
                href="index.php"
                class="back-menu-link"
            >
                ← Back to Menu
            </a>


        </div>


    <?php endif; ?>


</main>


<script>

const orderId =
    <?php echo (int)$orderId; ?>;


function markAsPaid()
{
    const button =
        document.getElementById("paid-button");

    const message =
        document.getElementById("payment-message");


    if (!button || !message) {
        return;
    }


    button.disabled = true;

    button.textContent = "Submitting...";


    fetch("api/mark-paid.php", {

        method: "POST",

        headers: {
            "Content-Type": "application/json"
        },

        body: JSON.stringify({
            order_id: orderId
        })

    })

    .then(response => response.json())

    .then(data => {

        if (data.success) {

            message.textContent =
                "Payment submitted for verification.";

            message.className =
                "payment-message success";


            setTimeout(() => {

                location.reload();

            }, 1200);

        } else {

            message.textContent =
                data.message ||
                "Unable to submit payment.";

            message.className =
                "payment-message error";

            button.disabled = false;

            button.textContent = "I HAVE PAID";
        }

    })

    .catch(error => {

        console.error(error);

        message.textContent =
            "Something went wrong. Please try again.";

        message.className =
            "payment-message error";

        button.disabled = false;

        button.textContent = "I HAVE PAID";
    });
}


function checkPaymentStatus()
{
    fetch(
        "api/payment-status.php?order_id=" +
        encodeURIComponent(orderId)
    )

    .then(response => response.json())

    .then(data => {

        if (
            data.success &&
            data.payment_status === "PAID"
        ) {

           window.location.href =
    "payment.php?order_id=" + data.order_id;

        } else {

            alert(
                "Payment is still waiting for admin verification."
            );
        }

    })

    .catch(error => {

        console.error(error);

        alert(
            "Unable to check payment status."
        );

    });
}

</script>


</body>

</html>
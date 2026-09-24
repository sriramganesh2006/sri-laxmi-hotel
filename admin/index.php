<?php

require_once "auth.php";


/*
|--------------------------------------------------------------------------
| Dashboard Statistics
|--------------------------------------------------------------------------
*/

$totalOrders = 0;
$newOrders = 0;
$preparingOrders = 0;
$completedOrders = 0;
$totalRevenue = 0;


/*
|--------------------------------------------------------------------------
| Total Orders
|--------------------------------------------------------------------------
*/

$result = $conn->query(
    "SELECT COUNT(*) AS total
     FROM orders"
);

if ($result) {

    $row = $result->fetch_assoc();

    $totalOrders = (int)$row["total"];
}


/*
|--------------------------------------------------------------------------
| New Orders
|--------------------------------------------------------------------------
*/

$result = $conn->query(
    "SELECT COUNT(*) AS total
     FROM orders
     WHERE status = 'NEW'"
);

if ($result) {

    $row = $result->fetch_assoc();

    $newOrders = (int)$row["total"];
}


/*
|--------------------------------------------------------------------------
| Preparing Orders
|--------------------------------------------------------------------------
*/

$result = $conn->query(
    "SELECT COUNT(*) AS total
     FROM orders
     WHERE status = 'PREPARING'"
);

if ($result) {

    $row = $result->fetch_assoc();

    $preparingOrders = (int)$row["total"];
}


/*
|--------------------------------------------------------------------------
| Completed Orders
|--------------------------------------------------------------------------
*/

$result = $conn->query(
    "SELECT COUNT(*) AS total
     FROM orders
     WHERE status = 'COMPLETED'"
);

if ($result) {

    $row = $result->fetch_assoc();

    $completedOrders = (int)$row["total"];
}


/*
|--------------------------------------------------------------------------
| Revenue
|--------------------------------------------------------------------------
|
| Count only PAID orders.
|
*/

$result = $conn->query(
    "SELECT COALESCE(SUM(total_amount), 0) AS revenue
     FROM orders
     WHERE payment_status = 'PAID'"
);

if ($result) {

    $row = $result->fetch_assoc();

    $totalRevenue = (float)$row["revenue"];
}


/*
|--------------------------------------------------------------------------
| Get Orders
|--------------------------------------------------------------------------
*/

$result = $conn->query(
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
     ORDER BY id DESC"
);


$orders = [];


while ($row = $result->fetch_assoc()) {

    $orders[] = $row;
}

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
        Admin Dashboard - SRI LAXMI Hotel
    </title>

    <link
        rel="stylesheet"
        href="../css/style.css"
    >

</head>


<body class="admin-page">


<header class="admin-header">

    <div>

        <h1>SRI LAXMI HOTEL</h1>

        <p>Admin Dashboard</p>

    </div>


    <div class="admin-actions">

        <span>
            Welcome, admin
        </span>

        <a href="../index.php">
            View Menu
        </a>

        <a href="orders.php">
            Orders
        </a>

        <a href="logout.php">
            Logout
        </a>

    </div>

</header>


<main class="admin-container">


<!-- =========================================================
     DASHBOARD STATISTICS
========================================================= -->

<section class="admin-stats">


    <div class="admin-stat-card">

        <span class="stat-label">
            TOTAL ORDERS
        </span>

        <strong>
            <?php echo $totalOrders; ?>
        </strong>

    </div>


    <div class="admin-stat-card">

        <span class="stat-label">
            NEW ORDERS
        </span>

        <strong>
            <?php echo $newOrders; ?>
        </strong>

    </div>


    <div class="admin-stat-card">

        <span class="stat-label">
            PREPARING
        </span>

        <strong>
            <?php echo $preparingOrders; ?>
        </strong>

    </div>


    <div class="admin-stat-card">

        <span class="stat-label">
            COMPLETED
        </span>

        <strong>
            <?php echo $completedOrders; ?>
        </strong>

    </div>


    <div class="admin-stat-card">

        <span class="stat-label">
            PAID REVENUE
        </span>

        <strong>
            ₹<?php
            echo number_format(
                $totalRevenue,
                0
            );
            ?>
        </strong>

    </div>


</section>


<!-- =========================================================
     ORDERS
========================================================= -->

<div class="admin-orders-heading">

    <div>

        <h2>Orders</h2>

        <p>
            Manage customer orders and payments.
        </p>

    </div>


    <a
        href="orders.php"
        class="view-all-orders"
    >
        View All Orders →
    </a>

</div>


<?php if (count($orders) === 0): ?>

    <div class="no-orders">

        <h3>No Orders Yet</h3>

        <p>
            New customer orders will appear here.
        </p>

    </div>


<?php else: ?>


    <?php foreach ($orders as $order): ?>


        <div class="admin-order-card">


            <div class="admin-order-top">


                <div>

                    <h3>

                        Order #<?php
                        echo (int)$order["id"];
                        ?>

                    </h3>


                    <small>

                        <?php

                        echo htmlspecialchars(
                            $order["created_at"]
                        );

                        ?>

                    </small>

                </div>


                <div class="status-area">


                    <span class="payment-status
                        payment-<?php

                        echo strtolower(
                            $order["payment_status"]
                        );

                        ?>">

                        Payment:

                        <?php

                        echo htmlspecialchars(
                            $order["payment_status"]
                        );

                        ?>

                    </span>


                    <span class="order-status
                        status-<?php

                        echo strtolower(
                            $order["status"]
                        );

                        ?>">

                        Order:

                        <?php

                        echo htmlspecialchars(
                            $order["status"]
                        );

                        ?>

                    </span>


                </div>


            </div>


            <div class="customer-info">


                <p>

                    <strong>
                        Customer:
                    </strong>

                    <?php

                    echo htmlspecialchars(
                        $order["customer_name"]
                    );

                    ?>

                </p>


                <p>

                    <strong>
                        Phone:
                    </strong>

                    <?php

                    echo htmlspecialchars(
                        $order["phone"]
                    );

                    ?>

                </p>


                <p>

                    <strong>
                        Table:
                    </strong>

                    <?php

                    echo htmlspecialchars(
                        $order["table_number"]
                    );

                    ?>

                </p>


            </div>


            <?php

            $stmt = $conn->prepare(
                "SELECT
                    mi.name,
                    oi.quantity,
                    oi.price
                 FROM order_items oi
                 INNER JOIN menu_items mi
                    ON oi.menu_item_id = mi.id
                 WHERE oi.order_id = ?"
            );


            $stmt->bind_param(
                "i",
                $order["id"]
            );


            $stmt->execute();

            $itemsResult =
                $stmt->get_result();

            ?>


            <div class="admin-items">

                <h4>Items</h4>


                <?php while (
                    $item =
                    $itemsResult->fetch_assoc()
                ): ?>


                    <div class="admin-item-row">

                        <span>

                            <?php

                            echo htmlspecialchars(
                                $item["name"]
                            );

                            ?>

                            ×

                            <?php

                            echo (int)
                                $item["quantity"];

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


                <?php endwhile; ?>


            </div>


            <?php

            $stmt->close();

            ?>


            <div class="admin-order-bottom">


                <strong class="admin-total">

                    Total:

                    ₹<?php

                    echo number_format(
                        (float)$order["total_amount"],
                        0
                    );

                    ?>

                </strong>


                <?php if (
                    $order["payment_status"]
                    === "VERIFYING"
                ): ?>


                    <form
                        action="confirm-payment.php"
                        method="POST"
                        class="payment-confirm-form"
                    >

                        <input
                            type="hidden"
                            name="order_id"
                            value="<?php
                                echo (int)
                                    $order["id"];
                            ?>"
                        >


                        <input
                            type="hidden"
                            name="csrf_token"
                            value="<?php
                                echo htmlspecialchars(
                                    getCsrfToken()
                                );
                            ?>"
                        >


                        <button
                            type="submit"
                            class="confirm-payment-btn"
                        >
                            ✓ CONFIRM PAYMENT
                        </button>

                    </form>


                <?php endif; ?>


                <?php if (
                    $order["payment_status"]
                    === "PAID"
                ): ?>


                    <form
                        action="update-status.php"
                        method="POST"
                        class="status-form"
                    >

                        <input
                            type="hidden"
                            name="order_id"
                            value="<?php
                                echo (int)
                                    $order["id"];
                            ?>"
                        >


                        <input
                            type="hidden"
                            name="csrf_token"
                            value="<?php
                                echo htmlspecialchars(
                                    getCsrfToken()
                                );
                            ?>"
                        >


                        <select name="status">

                            <option
                                value="NEW"
                                <?php

                                if (
                                    $order["status"]
                                    === "NEW"
                                ) {
                                    echo "selected";
                                }

                                ?>
                            >
                                NEW
                            </option>


                            <option
                                value="ACCEPTED"
                                <?php

                                if (
                                    $order["status"]
                                    === "ACCEPTED"
                                ) {
                                    echo "selected";
                                }

                                ?>
                            >
                                ACCEPTED
                            </option>


                            <option
                                value="PREPARING"
                                <?php

                                if (
                                    $order["status"]
                                    === "PREPARING"
                                ) {
                                    echo "selected";
                                }

                                ?>
                            >
                                PREPARING
                            </option>


                            <option
                                value="COMPLETED"
                                <?php

                                if (
                                    $order["status"]
                                    === "COMPLETED"
                                ) {
                                    echo "selected";
                                }

                                ?>
                            >
                                COMPLETED
                            </option>

                        </select>


                        <button type="submit">
                            Update
                        </button>

                    </form>


                <?php endif; ?>


                <?php if (
                    $order["payment_status"]
                    === "PENDING"
                ): ?>

                    <span class="waiting-payment">

                        Waiting for customer payment

                    </span>

                <?php endif; ?>


            </div>


        </div>


    <?php endforeach; ?>


<?php endif; ?>


</main>


</body>

</html>
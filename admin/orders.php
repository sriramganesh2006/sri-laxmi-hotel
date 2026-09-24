<?php

require_once "auth.php";

/*
|--------------------------------------------------------------------------
| Fetch Orders
|--------------------------------------------------------------------------
*/

$orders = [];

try {

    $sql = "
        SELECT
            id,
            customer_name,
            phone,
            table_number,
            total_amount,
            status,
            payment_status,
            created_at
        FROM orders
        ORDER BY created_at DESC
    ";

    $result = $conn->query($sql);

    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }

} catch (Throwable $e) {

    $errorMessage = "Unable to load orders.";
}


/*
|--------------------------------------------------------------------------
| Fetch Items For Each Order
|--------------------------------------------------------------------------
*/

$orderItems = [];

try {

    $sql = "
        SELECT
            oi.order_id,
            oi.quantity,
            oi.price,
            mi.name
        FROM order_items oi
        INNER JOIN menu_items mi
            ON oi.menu_item_id = mi.id
        ORDER BY oi.order_id DESC
    ";

    $result = $conn->query($sql);

    while ($row = $result->fetch_assoc()) {

        $orderItems[$row["order_id"]][] = $row;
    }

} catch (Throwable $e) {

    $orderItems = [];
}

?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Admin Orders - SRI LAXMI Hotel</title>

    <link rel="stylesheet"
          href="css/admin-orders.css">

</head>

<body>

<header class="admin-header">

    <div>
        <h1>SRI LAXMI HOTEL</h1>
        <p>Admin Order Management</p>
    </div>

    <a href="index.php" class="dashboard-link">
        Dashboard
    </a>

</header>


<main class="orders-container">

    <div class="page-heading">

        <div>
            <h2>Orders</h2>

            <p>
                Manage customer orders and update their status.
            </p>
        </div>

        <button
            onclick="location.reload()"
            class="refresh-btn">
            ↻ Refresh
        </button>

    </div>


    <?php if (isset($errorMessage)): ?>

        <div class="error-box">
            <?= htmlspecialchars($errorMessage) ?>
        </div>

    <?php endif; ?>


    <?php if (count($orders) === 0): ?>

        <div class="empty-orders">

            <div class="empty-icon">📦</div>

            <h3>No Orders Yet</h3>

            <p>
                New customer orders will appear here.
            </p>

        </div>

    <?php else: ?>


        <div class="orders-list">

            <?php foreach ($orders as $order): ?>

                <?php

                $orderId = (int)$order["id"];

                $status = strtoupper(
                    $order["status"] ?? "NEW"
                );

                ?>

                <section class="order-card">


                    <!-- ORDER HEADER -->

                    <div class="order-header">

                        <div>

                            <span class="order-label">
                                ORDER
                            </span>

                            <h3>
                                #<?= $orderId ?>
                            </h3>

                        </div>


                        <span class="status status-<?= strtolower($status) ?>">

                            <?= htmlspecialchars($status) ?>

                        </span>

                    </div>


                    <!-- CUSTOMER DETAILS -->

                    <div class="customer-details">

                        <div>

                            <span class="detail-label">
                                Customer
                            </span>

                            <strong>
                                <?= htmlspecialchars(
                                    $order["customer_name"]
                                ) ?>
                            </strong>

                        </div>


                        <div>

                            <span class="detail-label">
                                Phone
                            </span>

                            <strong>
                                <?= htmlspecialchars(
                                    $order["phone"]
                                ) ?>
                            </strong>

                        </div>


                        <div>

                            <span class="detail-label">
                                Table
                            </span>

                            <strong>
                                <?= htmlspecialchars(
                                    $order["table_number"]
                                ) ?>
                            </strong>

                        </div>


                        <div>

                            <span class="detail-label">
                                Ordered At
                            </span>

                            <strong>
                                <?= htmlspecialchars(
                                    $order["created_at"]
                                ) ?>
                            </strong>

                        </div>

                    </div>


                    <!-- ORDER ITEMS -->

                    <div class="items-section">

                        <h4>Order Items</h4>

                        <?php if (
                            isset($orderItems[$orderId])
                        ): ?>

                            <?php foreach (
                                $orderItems[$orderId]
                                as $item
                            ): ?>

                                <div class="order-item">

                                    <div>

                                        <strong>
                                            <?= htmlspecialchars(
                                                $item["name"]
                                            ) ?>
                                        </strong>

                                        <span>
                                            ×
                                            <?= (int)$item["quantity"] ?>
                                        </span>

                                    </div>


                                    <strong>
                                        ₹<?= number_format(
                                            $item["price"] *
                                            $item["quantity"],
                                            0
                                        ) ?>
                                    </strong>

                                </div>

                            <?php endforeach; ?>

                        <?php endif; ?>

                    </div>


                    <!-- ORDER TOTAL -->

                    <div class="order-total">

                        <span>Total</span>

                        <strong>
                            ₹<?= number_format(
                                (float)$order["total_amount"],
                                0
                            ) ?>
                        </strong>

                    </div>


                    <!-- PAYMENT -->

                    <div class="payment-row">

                        <span>
                            Payment
                        </span>

                        <span class="payment-status">

                            <?= htmlspecialchars(
                                $order["payment_status"]
                            ) ?>

                        </span>

                    </div>


                    <!-- STATUS ACTIONS -->

                    <div class="status-actions">

                        <span>
                            Update Status
                        </span>


                        <form
                            action="update-order-status.php"
                            method="POST"
                            class="status-form">

                            <input
                                type="hidden"
                                name="order_id"
                                value="<?= $orderId ?>">


                            <select
                                name="status"
                                onchange="this.form.submit()">

                                <option
                                    value="NEW"
                                    <?= $status === "NEW"
                                        ? "selected"
                                        : "" ?>>
                                    New
                                </option>

                                <option
                                    value="ACCEPTED"
                                    <?= $status === "ACCEPTED"
                                        ? "selected"
                                        : "" ?>>
                                    Accepted
                                </option>

                                <option
                                    value="PREPARING"
                                    <?= $status === "PREPARING"
                                        ? "selected"
                                        : "" ?>>
                                    Preparing
                                </option>

                                <option
                                    value="READY"
                                    <?= $status === "READY"
                                        ? "selected"
                                        : "" ?>>
                                    Ready
                                </option>

                                <option
                                    value="COMPLETED"
                                    <?= $status === "COMPLETED"
                                        ? "selected"
                                        : "" ?>>
                                    Completed
                                </option>

                                <option
                                    value="CANCELLED"
                                    <?= $status === "CANCELLED"
                                        ? "selected"
                                        : "" ?>>
                                    Cancelled
                                </option>

                            </select>

                        </form>

                    </div>

                </section>

            <?php endforeach; ?>

        </div>

    <?php endif; ?>

</main>

</body>
</html>
<?php

require_once "../config/bootstrap.php";


/*
|--------------------------------------------------------------------------
| Only POST Requests
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    header("Location: orders.php");
    exit;
}


/*
|--------------------------------------------------------------------------
| Get Data
|--------------------------------------------------------------------------
*/

$orderId = (int)($_POST["order_id"] ?? 0);

$status = strtoupper(
    trim($_POST["status"] ?? "")
);


/*
|--------------------------------------------------------------------------
| Validate
|--------------------------------------------------------------------------
*/

$allowedStatuses = [
    "NEW",
    "ACCEPTED",
    "PREPARING",
    "READY",
    "COMPLETED",
    "CANCELLED"
];


if ($orderId <= 0) {

    header("Location: orders.php");
    exit;
}


if (!in_array($status, $allowedStatuses, true)) {

    header("Location: orders.php");
    exit;
}


/*
|--------------------------------------------------------------------------
| Update Order
|--------------------------------------------------------------------------
*/

try {

    $stmt = $conn->prepare(
        "UPDATE orders
         SET status = ?
         WHERE id = ?"
    );


    $stmt->bind_param(
        "si",
        $status,
        $orderId
    );


    $stmt->execute();

    $stmt->close();


} catch (Throwable $e) {

    error_log(
        "Order status update error: " .
        $e->getMessage()
    );
}


/*
|--------------------------------------------------------------------------
| Return
|--------------------------------------------------------------------------
*/

header("Location: orders.php");

exit;

?>
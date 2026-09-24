<?php

require_once "auth.php";


if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    header("Location: index.php");

    exit;
}


$orderId = filter_input(
    INPUT_POST,
    "order_id",
    FILTER_VALIDATE_INT
);


$newStatus = $_POST["status"] ?? "";

$csrfToken = $_POST["csrf_token"] ?? "";


$allowedStatuses = [
    "NEW",
    "ACCEPTED",
    "COMPLETED"
];


if (
    !$orderId ||
    $orderId <= 0 ||
    !in_array($newStatus, $allowedStatuses, true) ||
    !verifyCsrfToken($csrfToken)
) {

    die("Invalid request.");
}


/*
|--------------------------------------------------------------------------
| Only PAID orders can be changed to ACCEPTED/COMPLETED
|--------------------------------------------------------------------------
*/

if (
    $newStatus === "ACCEPTED" ||
    $newStatus === "COMPLETED"
) {

    $stmt = $conn->prepare(
        "UPDATE orders
         SET status = ?
         WHERE id = ?
         AND payment_status = 'PAID'"
    );


    $stmt->bind_param(
        "si",
        $newStatus,
        $orderId
    );

} else {

    $stmt = $conn->prepare(
        "UPDATE orders
         SET status = ?
         WHERE id = ?"
    );


    $stmt->bind_param(
        "si",
        $newStatus,
        $orderId
    );
}


$stmt->execute();

$stmt->close();


header(
    "Location: index.php"
);

exit;

?>
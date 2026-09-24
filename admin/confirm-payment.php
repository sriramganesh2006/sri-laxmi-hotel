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


$csrfToken = $_POST["csrf_token"] ?? "";


if (
    !$orderId ||
    $orderId <= 0 ||
    !verifyCsrfToken($csrfToken)
) {

    die("Invalid request.");
}


/*
|--------------------------------------------------------------------------
| Confirm Payment
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare(
    "UPDATE orders
     SET
        payment_status = 'PAID',
        status = 'NEW'
     WHERE id = ?
     AND payment_status = 'VERIFYING'"
);


$stmt->bind_param(
    "i",
    $orderId
);


$stmt->execute();

$stmt->close();


header(
    "Location: index.php"
);

exit;

?>
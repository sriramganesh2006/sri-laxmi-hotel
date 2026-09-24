<?php

require_once "../config/bootstrap.php";


if (!isRequestMethod("GET")) {

    jsonResponse(
        [
            "success" => false,
            "message" => "Only GET requests are allowed."
        ],
        405
    );
}


$orderId = filter_input(
    INPUT_GET,
    "order_id",
    FILTER_VALIDATE_INT
);


if (!$orderId || $orderId <= 0) {

    jsonResponse(
        [
            "success" => false,
            "message" => "Invalid order ID."
        ],
        400
    );
}


$stmt = $conn->prepare(
    "SELECT
        id,
        payment_status,
        status
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

    jsonResponse(
        [
            "success" => false,
            "message" => "Order not found."
        ],
        404
    );
}


jsonResponse(
    [
        "success" => true,
        "order_id" => (int)$order["id"],
        "payment_status" => $order["payment_status"],
        "order_status" => $order["status"]
    ]
);

?>
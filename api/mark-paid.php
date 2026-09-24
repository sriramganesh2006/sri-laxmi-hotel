<?php

require_once "../config/bootstrap.php";


if (!isRequestMethod("POST")) {

    jsonResponse(
        [
            "success" => false,
            "message" => "Only POST requests are allowed."
        ],
        405
    );
}


$data = getJsonInput();


$orderId = (int)($data["order_id"] ?? 0);


if ($orderId <= 0) {

    jsonResponse(
        [
            "success" => false,
            "message" => "Invalid order ID."
        ],
        400
    );
}


$stmt = $conn->prepare(
    "SELECT payment_status
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


if ($order["payment_status"] === "PAID") {

    jsonResponse(
        [
            "success" => true,
            "message" => "Payment is already confirmed.",
            "payment_status" => "PAID"
        ]
    );
}


$stmt = $conn->prepare(
    "UPDATE orders
     SET payment_status = 'VERIFYING'
     WHERE id = ?
     AND payment_status = 'PENDING'"
);


$stmt->bind_param(
    "i",
    $orderId
);


$stmt->execute();

$stmt->close();


jsonResponse(
    [
        "success" => true,
        "message" =>
            "Payment submitted for verification.",
        "payment_status" => "VERIFYING"
    ]
);

?>
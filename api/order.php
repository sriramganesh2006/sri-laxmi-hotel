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


$customerName = trim($data["customer_name"] ?? "");
$phone = trim($data["phone"] ?? "");
$tableNumber = trim($data["table_number"] ?? "");
$cart = $data["items"] ?? [];


/*
|--------------------------------------------------------------------------
| Validate Customer Details
|--------------------------------------------------------------------------
*/

if ($customerName === "") {

    jsonResponse(
        [
            "success" => false,
            "message" => "Customer name is required."
        ],
        400
    );
}


if (!preg_match("/^[0-9]{10}$/", $phone)) {

    jsonResponse(
        [
            "success" => false,
            "message" => "Enter a valid 10-digit phone number."
        ],
        400
    );
}


if ($tableNumber === "") {

    jsonResponse(
        [
            "success" => false,
            "message" => "Table number is required."
        ],
        400
    );
}


if (!is_array($cart) || count($cart) === 0) {

    jsonResponse(
        [
            "success" => false,
            "message" => "Cart is empty."
        ],
        400
    );
}


/*
|--------------------------------------------------------------------------
| Validate Cart From Database
|--------------------------------------------------------------------------
*/

$validatedItems = [];
$totalAmount = 0;
$totalItems = 0;


try {

    foreach ($cart as $cartItem) {

        $menuItemId = (int)($cartItem["id"] ?? 0);
        $quantity = (int)($cartItem["quantity"] ?? 0);


        if ($menuItemId <= 0 || $quantity <= 0) {

            jsonResponse(
                [
                    "success" => false,
                    "message" => "Invalid cart item."
                ],
                400
            );
        }


        $stmt = $conn->prepare(
            "SELECT id, name, price, image, available
             FROM menu_items
             WHERE id = ?
             LIMIT 1"
        );

        $stmt->bind_param(
            "i",
            $menuItemId
        );

        $stmt->execute();

        $result = $stmt->get_result();

        $menuItem = $result->fetch_assoc();

        $stmt->close();


        if (!$menuItem) {

            jsonResponse(
                [
                    "success" => false,
                    "message" => "Menu item not found."
                ],
                400
            );
        }


        if ((int)$menuItem["available"] !== 1) {

            jsonResponse(
                [
                    "success" => false,
                    "message" =>
                        $menuItem["name"] .
                        " is currently unavailable."
                ],
                400
            );
        }


        $price = (float)$menuItem["price"];

        $itemTotal = $price * $quantity;


        $validatedItems[] = [
            "id" => (int)$menuItem["id"],
            "name" => $menuItem["name"],
            "price" => $price,
            "quantity" => $quantity,
            "image" => $menuItem["image"],
            "item_total" => $itemTotal
        ];


        $totalItems += $quantity;

        $totalAmount += $itemTotal;
    }


    /*
    |--------------------------------------------------------------------------
    | Create Order
    |--------------------------------------------------------------------------
    */

    $conn->begin_transaction();


    $status = "NEW";
    $paymentStatus = "PENDING";


    $stmt = $conn->prepare(
        "INSERT INTO orders
        (
            customer_name,
            phone,
            table_number,
            total_amount,
            status,
            payment_status
        )
        VALUES (?, ?, ?, ?, ?, ?)"
    );


    $stmt->bind_param(
        "sssdss",
        $customerName,
        $phone,
        $tableNumber,
        $totalAmount,
        $status,
        $paymentStatus
    );


    $stmt->execute();

    $orderId = $stmt->insert_id;

    $stmt->close();


    /*
    |--------------------------------------------------------------------------
    | Insert Order Items
    |--------------------------------------------------------------------------
    */

    foreach ($validatedItems as $item) {

        $stmt = $conn->prepare(
            "INSERT INTO order_items
            (
                order_id,
                menu_item_id,
                quantity,
                price
            )
            VALUES (?, ?, ?, ?)"
        );


        $stmt->bind_param(
            "iiid",
            $orderId,
            $item["id"],
            $item["quantity"],
            $item["price"]
        );


        $stmt->execute();

        $stmt->close();
    }


    $conn->commit();


    /*
    |--------------------------------------------------------------------------
    | Response
    |--------------------------------------------------------------------------
    */

    jsonResponse(
        [
            "success" => true,
            "message" => "Order created successfully.",
            "order_id" => $orderId,
            "total_items" => $totalItems,
            "total_amount" => $totalAmount,
            "payment_status" => "PENDING"
        ]
    );


} catch (Throwable $e) {

    if ($conn->in_transaction) {
        $conn->rollback();
    }


    error_log(
        "Order creation error: " .
        $e->getMessage()
    );


    jsonResponse(
        [
            "success" => false,
            "message" =>
                "Unable to place order. Please try again."
        ],
        500
    );
}

?>
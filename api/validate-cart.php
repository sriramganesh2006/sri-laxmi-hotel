<?php

require_once "../config/bootstrap.php";


/*
|--------------------------------------------------------------------------
| Only POST requests allowed
|--------------------------------------------------------------------------
*/

if (!isRequestMethod("POST")) {

    jsonResponse(
        [
            "success" => false,
            "message" => "Only POST requests are allowed."
        ],
        405
    );
}


/*
|--------------------------------------------------------------------------
| Read Request
|--------------------------------------------------------------------------
*/

$data = getJsonInput();


if (!isset($data["items"]) || !is_array($data["items"])) {

    jsonResponse(
        [
            "success" => false,
            "message" => "Cart items are required."
        ],
        400
    );
}


$cartItems = $data["items"];


if (count($cartItems) === 0) {

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
| Prepare Database Query
|--------------------------------------------------------------------------
*/

$sql = "
    SELECT
        id,
        name,
        price,
        image,
        available
    FROM menu_items
    WHERE id = ?
    LIMIT 1
";

$stmt = $conn->prepare($sql);


$validatedItems = [];

$totalAmount = 0;
$totalItems = 0;


/*
|--------------------------------------------------------------------------
| Validate Each Cart Item
|--------------------------------------------------------------------------
*/

foreach ($cartItems as $item) {

    if (!isset($item["id"]) || !isset($item["quantity"])) {

        $stmt->close();

        jsonResponse(
            [
                "success" => false,
                "message" => "Invalid cart item."
            ],
            400
        );
    }


    $itemId = $item["id"];
    $quantity = $item["quantity"];


    /*
    |--------------------------------------------------------------------------
    | Validate ID
    |--------------------------------------------------------------------------
    */

    if (!isPositiveInteger($itemId)) {

        $stmt->close();

        jsonResponse(
            [
                "success" => false,
                "message" => "Invalid menu item ID."
            ],
            400
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Validate Quantity
    |--------------------------------------------------------------------------
    */

    if (!isPositiveInteger($quantity) || (int)$quantity > 99) {

        $stmt->close();

        jsonResponse(
            [
                "success" => false,
                "message" => "Invalid quantity."
            ],
            400
        );
    }


    $itemId = (int)$itemId;
    $quantity = (int)$quantity;


    /*
    |--------------------------------------------------------------------------
    | Get Real Item From Database
    |--------------------------------------------------------------------------
    */

    $stmt->bind_param("i", $itemId);

    $stmt->execute();

    $result = $stmt->get_result();

    $menuItem = $result->fetch_assoc();


    /*
    |--------------------------------------------------------------------------
    | Item Does Not Exist
    |--------------------------------------------------------------------------
    */

    if (!$menuItem) {

        $stmt->close();

        jsonResponse(
            [
                "success" => false,
                "message" => "Menu item not found."
            ],
            400
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Item Not Available
    |--------------------------------------------------------------------------
    */

    if ((int)$menuItem["available"] !== 1) {

        $stmt->close();

        jsonResponse(
            [
                "success" => false,
                "message" => $menuItem["name"] . " is currently unavailable."
            ],
            400
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Calculate Price From Database
    |--------------------------------------------------------------------------
    */

    $price = (float)$menuItem["price"];

    $itemTotal = $price * $quantity;


    /*
    |--------------------------------------------------------------------------
    | Add To Validated Cart
    |--------------------------------------------------------------------------
    */

    $validatedItems[] = [

        "id" => (int)$menuItem["id"],

        "name" => $menuItem["name"],

        "price" => $price,

        "image" => $menuItem["image"],

        "quantity" => $quantity,

        "item_total" => $itemTotal
    ];


    $totalItems += $quantity;

    $totalAmount += $itemTotal;
}


$stmt->close();


/*
|--------------------------------------------------------------------------
| Final Response
|--------------------------------------------------------------------------
*/

jsonResponse(
    [
        "success" => true,

        "items" => $validatedItems,

        "total_items" => $totalItems,

        "total_amount" => $totalAmount
    ]
);

?>
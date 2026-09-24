<?php

require_once "../config/bootstrap.php";


/*
|--------------------------------------------------------------------------
| Only GET requests allowed
|--------------------------------------------------------------------------
*/

if (!isRequestMethod("GET")) {

    jsonResponse(
        [
            "success" => false,
            "message" => "Only GET requests are allowed."
        ],
        405
    );
}


/*
|--------------------------------------------------------------------------
| Get Available Menu Items
|--------------------------------------------------------------------------
*/

try {

    $sql = "
        SELECT
            id,
            name,
            price,
            image,
            available
        FROM menu_items
        WHERE available = 1
        ORDER BY id ASC
    ";

    $stmt = $conn->prepare($sql);

    $stmt->execute();

    $result = $stmt->get_result();

    $items = [];

    while ($row = $result->fetch_assoc()) {

        $items[] = [
            "id" => (int)$row["id"],
            "name" => $row["name"],
            "price" => (float)$row["price"],
            "image" => $row["image"],
            "available" => (bool)$row["available"]
        ];
    }

    $stmt->close();


    /*
    |--------------------------------------------------------------------------
    | Send Response
    |--------------------------------------------------------------------------
    */

    jsonResponse(
        [
            "success" => true,
            "items" => $items
        ]
    );

} catch (Exception $e) {

    error_log("Menu API error: " . $e->getMessage());

    jsonResponse(
        [
            "success" => false,
            "message" => "Unable to load menu."
        ],
        500
    );
}

?>
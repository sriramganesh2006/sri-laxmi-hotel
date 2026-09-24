<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/database.php";


function jsonResponse($data, $statusCode = 200)
{
    http_response_code($statusCode);

    header("Content-Type: application/json; charset=UTF-8");

    echo json_encode(
        $data,
        JSON_UNESCAPED_UNICODE
    );

    exit;
}


function isRequestMethod($method)
{
    return strtoupper($_SERVER["REQUEST_METHOD"] ?? "") ===
           strtoupper($method);
}


function getJsonInput()
{
    $rawData = file_get_contents("php://input");

    if (!$rawData) {

        jsonResponse(
            [
                "success" => false,
                "message" => "Request body is empty."
            ],
            400
        );
    }

    $data = json_decode($rawData, true);

    if (!is_array($data)) {

        jsonResponse(
            [
                "success" => false,
                "message" => "Invalid JSON request."
            ],
            400
        );
    }

    return $data;
}


function isPositiveInteger($value)
{
    return filter_var(
        $value,
        FILTER_VALIDATE_INT
    ) !== false && (int)$value > 0;
}


/*
|--------------------------------------------------------------------------
| CSRF Protection
|--------------------------------------------------------------------------
*/

function getCsrfToken()
{
    if (empty($_SESSION["csrf_token"])) {

        $_SESSION["csrf_token"] =
            bin2hex(random_bytes(32));
    }

    return $_SESSION["csrf_token"];
}


function verifyCsrfToken($token)
{
    return isset($_SESSION["csrf_token"]) &&
           is_string($token) &&
           hash_equals(
               $_SESSION["csrf_token"],
               $token
           );
}

?>
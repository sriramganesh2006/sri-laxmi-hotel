<?php

require_once "../config/bootstrap.php";


/*
|--------------------------------------------------------------------------
| If already logged in, go to dashboard
|--------------------------------------------------------------------------
*/

if (
    isset($_SESSION["admin_logged_in"]) &&
    $_SESSION["admin_logged_in"] === true
) {

    header("Location: index.php");

    exit;
}


/*
|--------------------------------------------------------------------------
| Admin credentials
|--------------------------------------------------------------------------
|
| Username:
| admin
|
| Password:
| sai@123
|
| The password is stored as a hash.
|--------------------------------------------------------------------------
*/

$adminUsername = "admin";

$adminPasswordHash = password_hash(
    "sai@123",
    PASSWORD_DEFAULT
);


$error = "";


/*
|--------------------------------------------------------------------------
| Login Form
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim(
        $_POST["username"] ?? ""
    );

    $password = $_POST["password"] ?? "";


    if ($username === "" || $password === "") {

        $error = "Please enter username and password.";

    } elseif (
        $username === $adminUsername &&
        password_verify(
            $password,
            $adminPasswordHash
        )
    ) {

        /*
        |--------------------------------------------------------------------------
        | Login successful
        |--------------------------------------------------------------------------
        */

        session_regenerate_id(true);

        $_SESSION["admin_logged_in"] = true;

        $_SESSION["admin_username"] = $adminUsername;


        header("Location: index.php");

        exit;

    } else {

        $error = "Invalid username or password.";

    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Admin Login - SRI LAXMI Hotel</title>

    <link
        rel="stylesheet"
        href="../css/style.css"
    >

</head>

<body class="admin-login-page">


<div class="admin-login-container">


    <div class="admin-login-card">


        <!-- HOTEL NAME -->

        <div class="admin-login-logo">

            <div class="admin-login-icon">
                🔐
            </div>

            <h1>
                SRI LAXMI HOTEL
            </h1>

            <p>
                Admin Panel
            </p>

        </div>


        <!-- ERROR MESSAGE -->

        <?php if ($error !== ""): ?>

            <div class="admin-login-error">

                <?php
                echo htmlspecialchars($error);
                ?>

            </div>

        <?php endif; ?>


        <!-- LOGIN FORM -->

        <form
            method="POST"
            action=""
            class="admin-login-form"
        >


            <label for="username">
                Username
            </label>

            <input
                type="text"
                id="username"
                name="username"
                placeholder="Enter username"
                autocomplete="username"
                required
            >


            <label for="password">
                Password
            </label>

            <input
                type="password"
                id="password"
                name="password"
                placeholder="Enter password"
                autocomplete="current-password"
                required
            >


            <button
                type="submit"
                class="admin-login-button"
            >
                Login
            </button>

        </form>


        <a
            href="../index.php"
            class="back-to-menu"
        >
            ← Back to Menu
        </a>


    </div>


</div>


</body>

</html>
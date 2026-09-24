<?php

require_once "config/bootstrap.php";

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Checkout - SRI LAXMI Hotel</title>

    <link rel="stylesheet" href="css/style.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link rel="preconnect"
          href="https://fonts.gstatic.com"
          crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;600;700&family=Poppins:wght@400;500;600;700&display=swap"
          rel="stylesheet">

</head>


<body>


<header class="hotel-header">

    <div class="hotel-name">
        SAI LAXMI HOTEL
    </div>

    <div class="hotel-tagline">
        Delicious Food • Happy Moments
    </div>

</header>


<main class="checkout-page">

    <div class="checkout-container">


        <h1>Checkout</h1>

        <p class="checkout-subtitle">
            Enter your details to place your order.
        </p>


        <!-- Customer Details -->

        <form id="checkout-form">


            <div class="form-group">

                <label for="customer_name">
                    Name
                </label>

                <input
                    type="text"
                    id="customer_name"
                    name="customer_name"
                    placeholder="Enter your name"
                    maxlength="100"
                    required
                >

            </div>


            <div class="form-group">

                <label for="phone">
                    Phone Number
                </label>

                <input
                    type="tel"
                    id="phone"
                    name="phone"
                    placeholder="Enter phone number"
                    maxlength="15"
                    required
                >

            </div>


            <div class="form-group">

                <label for="table_number">
                    Table Number
                </label>

                <input
                    type="text"
                    id="table_number"
                    name="table_number"
                    placeholder="Example: T12"
                    maxlength="20"
                    required
                >

            </div>


            <!-- Order Summary -->

            <div class="checkout-summary">

                <h2>Order Summary</h2>

                <div id="checkout-items"></div>

                <div class="checkout-total">

                    <span>Total</span>

                    <strong>
                        ₹<span id="checkout-total">0</span>
                    </strong>

                </div>

            </div>


            <div
                id="checkout-message"
                class="checkout-message">
            </div>


            <button
                type="submit"
                class="place-order-btn"
                id="place-order-btn">

                Place Order

            </button>


        </form>


        <a href="cart.php" class="back-cart">
            ← Back to Cart
        </a>


    </div>

</main>


<script>

const checkoutForm = document.getElementById("checkout-form");

const checkoutItems =
    document.getElementById("checkout-items");

const checkoutTotal =
    document.getElementById("checkout-total");

const checkoutMessage =
    document.getElementById("checkout-message");

const placeOrderBtn =
    document.getElementById("place-order-btn");


/*
|--------------------------------------------------------------------------
| Get Cart
|--------------------------------------------------------------------------
*/

function getCart() {

    try {

        return JSON.parse(
            localStorage.getItem("hotelCart")
        ) || [];

    } catch (error) {

        return [];

    }

}


/*
|--------------------------------------------------------------------------
| Escape HTML
|--------------------------------------------------------------------------
*/

function escapeHtml(value) {

    return String(value)
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}


/*
|--------------------------------------------------------------------------
| Load Checkout
|--------------------------------------------------------------------------
*/

async function loadCheckout() {

    const cart = getCart();


    if (cart.length === 0) {

        checkoutItems.innerHTML =
            "<p>Your cart is empty.</p>";

        placeOrderBtn.disabled = true;

        return;

    }


    const itemsForValidation = cart.map(item => ({

        id: item.id,

        quantity: item.quantity

    }));


    try {

        const response = await fetch(
            "api/validate-cart.php",
            {

                method: "POST",

                headers: {
                    "Content-Type": "application/json"
                },

                body: JSON.stringify({
                    items: itemsForValidation
                })

            }
        );


        const data = await response.json();


        if (!data.success) {

            checkoutMessage.textContent =
                data.message;

            placeOrderBtn.disabled = true;

            return;

        }


        /*
        |--------------------------------------------------------------------------
        | Display Server-Validated Cart
        |--------------------------------------------------------------------------
        */

        checkoutItems.innerHTML = "";


        data.items.forEach(item => {

            const row =
                document.createElement("div");

            row.className = "checkout-item";


            row.innerHTML = `

                <div>

                    <strong>
                        ${escapeHtml(item.name)}
                    </strong>

                    <span>
                        × ${item.quantity}
                    </span>

                </div>

                <strong>
                    ₹${item.item_total.toFixed(0)}
                </strong>

            `;


            checkoutItems.appendChild(row);

        });


        checkoutTotal.textContent =
            data.total_amount.toFixed(0);


    } catch (error) {

        checkoutMessage.textContent =
            "Unable to connect to the server.";

        placeOrderBtn.disabled = true;

    }

}


/*
|--------------------------------------------------------------------------
| Submit Checkout
|--------------------------------------------------------------------------
*/

checkoutForm.addEventListener(
    "submit",
    async function(event) {

        event.preventDefault();


        const cart = getCart();


        if (cart.length === 0) {

            checkoutMessage.textContent =
                "Your cart is empty.";

            return;

        }


        const customerName =
            document.getElementById(
                "customer_name"
            ).value.trim();


        const phone =
            document.getElementById(
                "phone"
            ).value.trim();


        const tableNumber =
            document.getElementById(
                "table_number"
            ).value.trim();


        if (!customerName ||
            !phone ||
            !tableNumber) {

            checkoutMessage.textContent =
                "Please fill all details.";

            return;

        }


        placeOrderBtn.disabled = true;

        placeOrderBtn.textContent =
            "Placing Order...";


        checkoutMessage.textContent = "";


        const items =
            cart.map(item => ({

                id: item.id,

                quantity: item.quantity

            }));


        try {

            const response = await fetch(
                "api/order.php",
                {

                    method: "POST",

                    headers: {

                        "Content-Type":
                            "application/json"

                    },

                    body: JSON.stringify({

                        customer_name:
                            customerName,

                        phone:
                            phone,

                        table_number:
                            tableNumber,

                        items:
                            items

                    })

                }
            );


            const data =
                await response.json();


            if (!data.success) {

                checkoutMessage.textContent =
                    data.message;

                placeOrderBtn.disabled = false;

                placeOrderBtn.textContent =
                    "Place Order";

                return;

            }


            /*
            |--------------------------------------------------------------------------
            | Order Successfully Created
            |--------------------------------------------------------------------------
            */

            localStorage.removeItem(
                "hotelCart"
            );


            window.location.href =
                "order-success.php?order_id="
                + encodeURIComponent(data.order_id);


        } catch (error) {

            checkoutMessage.textContent =
                "Something went wrong. Please try again.";

            placeOrderBtn.disabled = false;

            placeOrderBtn.textContent =
                "Place Order";

        }

    }
);


/*
|--------------------------------------------------------------------------
| Start
|--------------------------------------------------------------------------
*/

loadCheckout();

</script>


</body>

</html>
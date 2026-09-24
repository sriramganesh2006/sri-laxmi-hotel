// ================================
// CART SYSTEM
// ================================

const CART_KEY = "hotelCart";


// Get cart from LocalStorage
function getCart() {

    try {

        return JSON.parse(
            localStorage.getItem(CART_KEY)
        ) || [];

    } catch (error) {

        return [];

    }
}


// Save cart
function saveCart(cart) {

    localStorage.setItem(
        CART_KEY,
        JSON.stringify(cart)
    );

}


// Get total quantity
function getTotalItems(cart) {

    return cart.reduce(
        (total, item) => total + Number(item.quantity),
        0
    );

}


// Get total price
function getTotalPrice(cart) {

    return cart.reduce(
        (total, item) =>
            total + (Number(item.price) * Number(item.quantity)),
        0
    );

}


// Update cart bar
function updateCartCount() {

    const cart = getCart();

    const countElement =
        document.getElementById("cart-count");

    const totalElement =
        document.getElementById("cart-total");


    if (countElement) {

        countElement.textContent =
            getTotalItems(cart);

    }


    if (totalElement) {

        totalElement.textContent =
            getTotalPrice(cart).toFixed(0);

    }

}


// ================================
// ADD TO CART
// ================================

function addToCart(item) {

    const cart = getCart();


    const existingItem =
        cart.find(
            cartItem =>
                Number(cartItem.id) === Number(item.id)
        );


    if (existingItem) {

        existingItem.quantity += 1;

    } else {

        cart.push({

            id: Number(item.id),

            name: item.name,

            price: Number(item.price),

            image: item.image,

            quantity: 1

        });

    }


    saveCart(cart);

    updateCartCount();

    updateMenuQuantities();

}


// ================================
// REMOVE ONE ITEM
// ================================

function removeFromCart(itemId) {

    let cart = getCart();


    const existingItem =
        cart.find(
            item =>
                Number(item.id) === Number(itemId)
        );


    if (!existingItem) {
        return;
    }


    existingItem.quantity -= 1;


    if (existingItem.quantity <= 0) {

        cart =
            cart.filter(
                item =>
                    Number(item.id) !== Number(itemId)
            );

    }


    saveCart(cart);

    updateCartCount();

    updateMenuQuantities();

}


// ================================
// UPDATE MENU QUANTITIES
// ================================

function updateMenuQuantities() {

    const cart = getCart();


    document
        .querySelectorAll(".quantity")
        .forEach(quantityElement => {

            const id =
                quantityElement.id.replace(
                    "quantity-",
                    ""
                );


            const item =
                cart.find(
                    cartItem =>
                        Number(cartItem.id) === Number(id)
                );


            quantityElement.textContent =
                item ? item.quantity : 0;

        });

}


// ================================
// SETUP MENU BUTTONS
// ================================

function setupMenuButtons() {

    const plusButtons =
        document.querySelectorAll(".plus-btn");


    const minusButtons =
        document.querySelectorAll(".minus-btn");


    const addButtons =
        document.querySelectorAll(".add-cart-btn");


    console.log(
        "Add to Cart buttons found:",
        addButtons.length
    );


    // PLUS BUTTON
    plusButtons.forEach(button => {

        button.addEventListener(
            "click",
            function () {

                const id =
                    this.dataset.id;


                const card =
                    this.closest(".food-card");


                if (!card) return;


                const addButton =
                    card.querySelector(".add-cart-btn");


                if (!addButton) return;


                addToCart({

                    id: addButton.dataset.id,

                    name: addButton.dataset.name,

                    price: addButton.dataset.price,

                    image: addButton.dataset.image

                });

            }
        );

    });


    // MINUS BUTTON
    minusButtons.forEach(button => {

        button.addEventListener(
            "click",
            function () {

                removeFromCart(
                    this.dataset.id
                );

            }
        );

    });


    // ADD TO CART BUTTON
    addButtons.forEach(button => {

        button.addEventListener(
            "click",
            function () {

                console.log(
                    "Add to Cart clicked:",
                    this.dataset.name
                );


                addToCart({

                    id: this.dataset.id,

                    name: this.dataset.name,

                    price: this.dataset.price,

                    image: this.dataset.image

                });


                // Small visual feedback

                const originalText =
                    this.textContent;


                this.textContent =
                    "✓ Added to Cart";


                setTimeout(() => {

                    this.textContent =
                        originalText;

                }, 700);

            }
        );

    });


    updateMenuQuantities();

}


// ================================
// CART PAGE
// ================================

function renderCartPage() {

    const cartContainer =
        document.getElementById("cart-items");


    if (!cartContainer) {
        return;
    }


    const cart = getCart();


    const totalElement =
        document.getElementById("cart-total");


    const emptyMessage =
        document.getElementById("empty-cart");


    if (cart.length === 0) {

        cartContainer.innerHTML = "";

        if (emptyMessage) {
            emptyMessage.style.display = "block";
        }

        if (totalElement) {
            totalElement.textContent = "0";
        }

        return;

    }


    if (emptyMessage) {
        emptyMessage.style.display = "none";
    }


    cartContainer.innerHTML = "";


    let total = 0;


    cart.forEach(item => {

        const itemTotal =
            Number(item.price) *
            Number(item.quantity);


        total += itemTotal;


        const cartItem =
            document.createElement("div");


        cartItem.className =
            "cart-item";


        cartItem.innerHTML = `

            <img
                src="images/${escapeHtml(item.image)}"
                alt="${escapeHtml(item.name)}"
            >

            <div class="cart-item-details">

                <h3>
                    ${escapeHtml(item.name)}
                </h3>

                <p>
                    ₹${Number(item.price).toFixed(0)}
                </p>

                <div class="quantity-control">

                    <button
                        class="quantity-btn cart-minus"
                        data-id="${item.id}">
                        −
                    </button>

                    <span class="quantity">
                        ${item.quantity}
                    </span>

                    <button
                        class="quantity-btn cart-plus"
                        data-id="${item.id}">
                        +
                    </button>

                </div>

            </div>

            <strong>
                ₹${itemTotal.toFixed(0)}
            </strong>

        `;


        cartContainer.appendChild(cartItem);

    });


    if (totalElement) {

        totalElement.textContent =
            total.toFixed(0);

    }


    setupCartButtons();

}


// ================================
// CART PAGE BUTTONS
// ================================

function setupCartButtons() {

    document
        .querySelectorAll(".cart-plus")
        .forEach(button => {

            button.addEventListener(
                "click",
                function () {

                    const cart =
                        getCart();


                    const item =
                        cart.find(
                            item =>
                                Number(item.id) ===
                                Number(this.dataset.id)
                        );


                    if (item) {

                        item.quantity += 1;

                        saveCart(cart);

                        renderCartPage();

                        updateCartCount();

                    }

                }
            );

        });


    document
        .querySelectorAll(".cart-minus")
        .forEach(button => {

            button.addEventListener(
                "click",
                function () {

                    removeFromCart(
                        this.dataset.id
                    );

                    renderCartPage();

                }
            );

        });

}


// ================================
// ESCAPE HTML
// ================================

function escapeHtml(value) {

    return String(value)

        .replace(/&/g, "&amp;")

        .replace(/</g, "&lt;")

        .replace(/>/g, "&gt;")

        .replace(/"/g, "&quot;")

        .replace(/'/g, "&#039;");

}


// ================================
// PAGE LOAD
// ================================

document.addEventListener(
    "DOMContentLoaded",
    function () {

        updateCartCount();

        setupMenuButtons();

        renderCartPage();

    }
);
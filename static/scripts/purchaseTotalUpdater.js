// Copyright (c) 2022 JoeBlakeB, all rights reserved.

// Update the total price of the purchase when the quantity is changed

var quantityInput = document.getElementById("quantity");
var subtotalDisplay = document.getElementById("subtotalDisplay");
var totalDisplay = document.getElementById("totalDisplay");
quantityInput.addEventListener("keyup", function() {
    let quantity = quantityInput.value;
    let total = subtotalDisplay.innerHTML.slice(1) * quantity;
    totalDisplay.innerHTML = "£" + total.toFixed(2);
});
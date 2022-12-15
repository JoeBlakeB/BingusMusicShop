// Copyright (c) 2022 JoeBlakeB, all rights reserved.

let form = document.getElementsByTagName("form")[0];

// Do not allow the form to be submitted if the data is invalid
form.addEventListener("submit", function (event) {
    if (verifyName() &&
        verifyPrice() && 
        verifyStock() && 
        verifyDescription()) {
        return form.submit();
    }
    event.preventDefault();
});

/**
 * Check the product name is valid, colour it red if it is not
 * 
 * @returns {boolean} if the name is valid
 */
function verifyName() {
    let value = nameContainer.children[1].value.trim();
    let valid = value.length > 0 && value.length <= 128;
    nameContainer.classList.toggle("inputError", !valid);
    return valid;
}
var nameContainer = document.getElementById("nameContainer");
nameContainer.children[1].addEventListener("input", verifyName);

/**
 * Check the product price is valid, colour it red if it is not
 * 
 * @returns {boolean} if the price is valid
 */
function verifyPrice() {
    let value = priceInput.valueAsNumber;
    let valid = value < 10**7 && value > 0;
    priceContainer.classList.toggle("inputError", !valid);
    return valid;
}
var priceContainer = document.getElementById("priceContainer");
var priceInput = priceContainer.children[2].children[0];
priceInput.addEventListener("input", verifyPrice);

// Only allow numbers to be entered
priceInput.addEventListener("keypress", function (event) {
    if ((isNaN(event.key) && event.key !== ".") || 
        (event.key === "." && priceInput.value.includes("."))) {
        return event.preventDefault();
    }
});

/**
 * Check the stock is valid, colour it red if it is not
 * 
 * @returns {boolean} if the stock is valid
 */
function verifyStock() {
    let value = stockContainer.children[1].valueAsNumber;
    let valid = value < 10**7 && value >= 0;
    stockContainer.classList.toggle("inputError", !valid);
    return valid;
}
var stockContainer = document.getElementById("stockContainer");
stockContainer.children[1].addEventListener("input", verifyStock);

// Only allow numbers to be entered
stockContainer.children[1].addEventListener("keypress", function (event) {
    if (isNaN(event.key)) {
        return event.preventDefault();
    }
});

/**
 * Check the description is valid, colour it red if it is not
 * 
 * @returns {boolean} if the description is valid
 */
function verifyDescription() {
    let value = descriptionContainer.children[1].value.trim();
    let valid = value.length <= 2 ** 16 - 1;
    descriptionContainer.classList.toggle("inputError", !valid);
    return valid;
}
var descriptionContainer = document.getElementById("descriptionContainer");
descriptionContainer.children[1].addEventListener("input", verifyDescription);
// Copyright (c) 2022 JoeBlakeB, all rights reserved.

var form = document.getElementsByTagName("form")[0];
var postcodeContainer = document.getElementById("postcodeContainer");
var countryContainer = document.getElementById("countryContainer");

/**
 * Validate a post code if it is in the UK, otherwise validate the length
 * only ran after clicking save incase they change the country.
 */
form.addEventListener("submit", function (event) {
    if (countryContainer.children[1].value == "GB") {
        let value = postcodeContainer.children[1].value.trim();
        let valid = value.match(/^[A-Za-z]{1,2}[0-9]([0-9A-Za-z]|)(| )[0-9][A-Za-z]{2}$/);
        if (valid) {
            return form.submit();
        }
        postcodeContainer.classList.toggle("inputError", !valid);
        event.preventDefault();
    }
});
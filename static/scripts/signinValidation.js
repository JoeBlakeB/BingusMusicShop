// Copyright (c) 2022 JoeBlakeB, all rights reserved.

let form = document.getElementsByTagName("form")[0];
let formType = form.submitButton.getAttribute("formtype");

// Add event listeners to check if the data is valid
// and to warn the user if it is not.

form.addEventListener("submit", function (event) {
    if (formType === "register") {
        // Check each on a different line to force all to run
        let verified = verifyEmail();
        verified = verifyPassword() && verified;
        verified = verifyName() && verified;
        if (verified) {
            return form.submit();
        }
    }
    else if (formType === "auth") {
        if (verifyAuth()) {
            return form.submit();
        }
    }
    else if (formType === "authWithEmail") {
        let verified = verifyEmail();
        verified = verifyAuth() && verified;
        if (verified) {
            return form.submit();
        }
    }
    else if (formType === "changePassword") {
        if (verifyPassword()) {
            return form.submit();
        }
    }
    else {
        if (verifyEmail()) {
            return form.submit();
        }
    }
    event.preventDefault();
});

/**
 * Update the DOM to show if the input is valid or not.
 * 
 * @param {element} element the input container
 * @param {bool} valid if the input is valid
 * @param {string} message what to tell the user 
 */
function verify(element, valid=true, message="") {
    element.classList.toggle("error", !valid);
    element.lastElementChild.innerHTML = message;
}

/**
 * Only checks if the email is valid.
 * Already in use checks are done server side.
 * 
 * @returns {boolean} if the email is valid
 */
function verifyEmail() {
    let email = emailContainer.children[1].value;
    let error = false;
    if (!email.match(/^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/)) {
        error = "You email is not valid, it must be in the following format:<br>name@example.com";
    }
    else if (email.length > 256) {
        error = "Your email must not be longer than 256 characters.";
    }
    verify(emailContainer, !error, error);
    return !error;
}

/**
 * Checks if password is 8-256 characters and has a-z A-Z and 0-9
 * 
 * @returns {boolean} if the password is valid
 */
function verifyPassword() {
    let password = passwordContainer.children[1].value;
    let error = false;
    // Check password
    if (password.length < 8) {
        error = "Passwords must be at least 8 characters.";
    }
    else if (password.length > 256) {
        error = "Passwords must not be longer than 256 characters.";
    }
    else if (!password.match(/[a-z]/)
        || !password.match(/[A-Z]/)
        || !password.match(/[0-9]/)) {
        error = "Passwords must contain at least:<br>a lowercase letter, an uppercase letter, and a number.";
    }

    // Update the DOM
    if (error) {
        verify(passwordContainer, false, error);
        return false
    }
    else {
        verify(passwordContainer);
        // if passwordConf is not default, check it as well
        if (passwordConfContainer.children[1].value !== "" ||
            passwordConfContainer.classList.contains("edited")) {
            return verifyPasswordConf();
        }
        return false;
    }
}

/**
 * Check that the password confirmation matches the password.
 * 
 * @returns {boolean} if the password confirmation is valid
 */
function verifyPasswordConf() {
    passwordConfContainer.classList.add("edited");
    let equals = (passwordContainer.children[1].value
        === passwordConfContainer.children[1].value);
    verify(passwordConfContainer, equals, "Passwords do not match.");
    return equals;
}

/**
 * Check the name is not empty.
 * This is because a name can be literally anything
 * and I dont want to accidentally block a real name.
 * 
 * @returns {boolean} if the name is valid
 */
function verifyName() {
    let name = nameContainer.children[1].value;
    let error = false;
    if (name.length === 0) {
        error = "You must enter your name.";
    }
    else if (name.length > 256) {
        error = "Your name must not be longer than 256 characters.";
    }
    verify(nameContainer, !error, error);
    return !error;
}

/**
 * Check a verification code is a 6 digit number
 * 
 * @returns {boolean} if the code is valid
 */
function verifyAuth() {
    let auth = authContainer.children[1].value;
    let error = false;
    if (!auth.match(/^[0-9]{6}$/)) {
        error = "Your verification code must be 6 digits.";
    }
    verify(authContainer, !error, error);
    return !error;
}

// Add event listeners,
// different pages have different inputs so not all are needed.

if (formType != "auth" && formType != "changePassword") {
    var emailContainer = document.getElementById("emailContainer");
    emailContainer.children[1].addEventListener("focusout", verifyEmail);
}

if (formType == "register") {
    var passwordContainer = document.getElementById("passwordContainer");
    var passwordConfContainer = document.getElementById("passwordConfContainer");
    var nameContainer = document.getElementById("nameContainer");

    passwordContainer.children[1].addEventListener("focusout", verifyPassword);
    passwordConfContainer.children[1].addEventListener("focusout", verifyPasswordConf);
    nameContainer.children[1].addEventListener("focusout", verifyName);
}
else if (formType == "auth" || formType == "authWithEmail") {
    var authContainer = document.getElementById("authContainer");
    authContainer.children[1].addEventListener("focusout", verifyAuth);
}
else if (formType == "changePassword") {
    var passwordContainer = document.getElementById("passwordContainer");
    var passwordConfContainer = document.getElementById("passwordConfContainer");
    passwordContainer.children[1].addEventListener("focusout", verifyPassword);
    passwordConfContainer.children[1].addEventListener("focusout", verifyPasswordConf);
}
// Copyright (c) 2022 JoeBlakeB, all rights reserved.

var currentImages = document.getElementById("currentImages");

/**
 * Tell the user a status message about the image management
 * 
 * @param {string} message The message to tell the user
 * @param {string} status The class to add to the message, success or error
 */
function tellUser(message, status = null) {
    let statusMessage = document.getElementById("statusMessage");
    statusMessage.innerText = message;
    statusMessage.classList.remove("success", "error");
    statusMessage.classList.add(status);
}

/**
 * Show or hide the Current Images header on if its empty
 */
function showHideCurrentImages() {
    document.getElementById("currentImagesHeader").style.display = (
        currentImages.childElementCount == 0 ? "none" : "block");
}

/**
 * Add an image to the page
 * 
 * @param {int} imageID The ID of the image
 * @param {string} fileName The URL of the image
 */
function addImage(imageID, fileName) {
    let div = document.createElement("div");
    div.id = "image-" + imageID;
    div.classList.add("imageContainer");
    div.innerHTML = `
        <img src="/images/${fileName}" alt="Image #${imageID}">
        <div class="imageContainerButtons">
            <button class="button" onclick="setPrimaryImage(${imageID})">Set as First</button>
            <button class="button" onclick="deleteImage(${imageID});">Delete</button>
        </div>
    `;
    currentImages.insertBefore(div, currentImages.firstChild);
    showHideCurrentImages();
}

/**
 * Upload the image after checking that it is valid
 * 
 * @param {object} image The image to upload
 */
function uploadImage(image) {
    // Check image is less than 2MB & is a valid image type, png jpeg, gif, webp
    if (image.size > 2*1024*1024) {
        return tellUser("The image is too large. Please upload an image less than 2MB.", "error");
    }
    if (!image.type.match(/image\/(png|jpeg|gif|webp)/)) {
        return tellUser("The file is not a valid image type, it must be one of the following: png, jpeg, gif, or webp.", "error");
    }
    // Upload the image
    tellUser("Uploading image...");
    let formData = new FormData();
    formData.append("image", image);
    formData.append("productID", imageInput.getAttribute("productID"));
    fetch("../../upload", {
        method: "POST",
        body: formData
    }).then(response => response.json()).then(data => {
        tellUser(data.message, data.success ? "success" : "error");
        addImage(data.imageID, data.fileName);
    }).catch(error => {
        tellUser("There was an error uploading the image, please try again.", "error");
    });
}

var imageInput = document.getElementById("imageInput");
imageInput.addEventListener("change", function () {
    let image = imageInput.files[0];
    console.log(typeof image);
    uploadImage();
});
var imageManagement = document.getElementById("imageManagement");
imageManagement.addEventListener("dragover", (event) => {
    event.preventDefault();
});
imageManagement.addEventListener("drop", (event) => {
    event.preventDefault();
    let image = event.dataTransfer.files[0];
    uploadImage(image);
});

/**
 * Delete image from server and page
 * 
 * @param {int} imageID The ID of the image to delete
 */
function deleteImage(imageID) {
    tellUser("Deleting image...");
    fetch("image?imageID=" + imageID + "&action=delete", {
        method: "GET",
    }).then(response => response.json()).then(data => {
        if (data.success) {
            tellUser("Image deleted successfully.", "success");
            let div = document.getElementById("image-" + imageID);
            div.remove();
            showHideCurrentImages();
        }
        else {
            tellUser("could remove image.", "error");
        }
    }).catch(error => {
        tellUser("could remove image.", "error");
    });
}

/**
 * Delete and re add the image to the page and the database so that it is the first image
 * 
 * @param {int} imageID The ID of the image to be made the first image
 */
function setPrimaryImage(imageID) {
    tellUser("Setting image as first...");
    fetch("image?imageID=" + imageID + "&action=setPrimary", {
        method: "GET",
    }).then(response => response.json()).then(data => {
        if (data.success) {
            tellUser("Image moved successfully.", "success");
            let div = document.getElementById("image-" + imageID);
            div.remove();
            currentImages.insertBefore(div, currentImages.firstChild);
        }
        else {
            tellUser("could not move image.", "error");
        }
    }).catch(error => {
        tellUser("could remove image.", "error");
    });
}
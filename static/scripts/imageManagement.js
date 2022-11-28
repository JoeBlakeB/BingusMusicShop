// Copyright (c) 2022 JoeBlakeB, all rights reserved.

/**
 * Upload the image in the input field automatically when clicked
 */
function uploadImage() {
    let image = imageInput.files[0];
    let formData = new FormData();
    formData.append("image", image);
    formData.append("productID", imageInput.getAttribute("productID"));
    fetch("../../upload", {
        method: "POST",
        body: formData
    }).then(response => {
        // reset image input
        imageInput.value = "";
    }).catch(error => {
        console.error(error); // TODO
    });
}
var imageInput = document.getElementById("imageInput");
imageInput.addEventListener("change", uploadImage);

/**
 * Delete image from server and page
 * 
 * @param {int} imageID The ID of the image to delete
 */
function deleteImage(imageID) {
    let div = document.getElementById("image-" + imageID);
    div.remove();

    // TODO, tell server
}

/**
 * Delete and re add the image to the page and the database so that it is the first image
 * 
 * @param {int} imageID The ID of the image to be made the first image
 */
function setPrimaryImage(imageID) {
    let div = document.getElementById("image-" + imageID);
    div.remove();
    let imageList = document.getElementById("imageList");
    imageList.insertBefore(div, imageList.firstChild);

    // TODO, tell server
}
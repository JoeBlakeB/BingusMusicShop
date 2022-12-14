// Copyright (c) 2022 JoeBlakeB, all rights reserved.

/**
 * Change the url for the main image to show,
 * and update the image selector to show which one is selected.
 * 
 * @param {event} event - The event that triggered this function.
 */
function setSelectedImage(event) {
    if (event.target.src == undefined) {
        target = event.target.children[0];
    }
    else {
        target = event.target;
    }
    document.getElementsByClassName("selected")[0].classList.remove("selected");
    target.classList.add("selected");
    selectedImage.src = target.src;
}

// Add event listeners to the image list
var imageList = document.getElementsByClassName("imageList")[0].children;
for (var i = 0; i < imageList.length; i++) {
    imageList[i].addEventListener("click", setSelectedImage);
};

var selectedImage = document.getElementById("selectedImage");
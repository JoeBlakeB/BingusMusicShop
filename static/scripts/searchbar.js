// Copyright (c) 2022 JoeBlakeB, all rights reserved.

var searchInput = document.getElementById("searchBar");
var searchButton = document.getElementById("searchButton");

/**
 * Sets the value of a search parameter in the URL.
 * 
 * @param {string} param The name of the search parameter.
 * @param {string} value The value of the search parameter.
 */
function setSearchParam(param, value) {
    let url = new URL(window.location.href);
    url.searchParams.delete("page");
    url.searchParams.set(param, value);
    window.location.href = url.toString();
}

searchInput.addEventListener("keyup", function(event) {
    if (event.key === "Enter") {
        event.preventDefault();
        searchButton.click();
    }
});

searchButton.addEventListener("click", function() {
    if (window.location.href.includes("/search")) {
        setSearchParam("q", searchInput.value);
    }
    else {
        window.location.href = basePath + "/search?q=" + 
            searchInput.value.replace(/ /g, "+");
    }
});
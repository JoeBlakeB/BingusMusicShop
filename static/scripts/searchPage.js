// Copyright (c) 2022 JoeBlakeB, all rights reserved.

// Sort by and items per page drop downs

var sortSelect = document.getElementById("sortSelect");
var itemsPerPageSelect = document.getElementById("itemsPerPageSelect");

sortSelect.addEventListener("change", function() {
    setSearchParam("sort", sortSelect.value);
});

itemsPerPageSelect.addEventListener("change", function() {
    setSearchParam("itemsPerPage", itemsPerPageSelect.value);
});

// Page buttons

var pageButtons = document.getElementsByClassName("pageButton");

for (var i = 0; i < pageButtons.length; i++) {
    pageButtons[i].addEventListener("click", function(event) {
        setSearchParam("page", event.target.getAttribute("value"));
    });
}
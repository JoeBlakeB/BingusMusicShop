<!DOCTYPE html>
<html lang="en">

<head>
    <?php
    $title = empty($searchTerm) ?
        "All Products" :
        "Search for \"" . $searchTerm . "\"";
    require "include/head.php";
    require "view/include/productList.php";
    ?>
</head>

<body>
    <?php require "include/header.php"; ?>
    <div class="content">
        <?php if (empty($results) && !$totalPages) { ?>
            <p>
                There are no results for <i><?= $searchTerm ?></i>.<br>
                Please try again with a different search term or
                <a href="<?= $this->basePath ?>">return to the homepage.</a>
            </p>
        <?php } else if (empty($results)) {
            $searchPath = $this->basePath . "/search?q=$searchTerm&itemsPerPage=$count&sort=$sort";
            ?> <p>
                There are not enough results for <?= $page ?> pages. <br>
                Go to the <a href="<?= $searchPath ?>">first page</a><?=
                $totalPages == 1 ? "." : " or the <a href=\"$searchPath&page= $totalPages\">last page</a>." ?>
            </p>
        <?php } else { ?>
            <div class="searchOptions">
                <div>
                    <label for="sort">Sort by:</label>
                    <select name="sort" id="sortSelect"> <?php
                        $sortOptions = [
                            "relevance" => "Relevance",
                            "newest" => "Newest",
                            "oldest" => "Oldest",
                            "price_asc" => "Price: Low to High",
                            "price_desc" => "Price: High to Low",
                            "name_asc" => "Name: A to Z",
                            "name_desc" => "Name: Z to A"
                        ];

                        echo "<option value=\"$sort\" selected>" . $sortOptions[$sort] . "</option>";
                        unset($sortOptions[$sort]);
                        foreach ($sortOptions as $value => $text) {
                            echo "<option value=\"$value\">$text</option>";
                        }
                    ?> </select>
                </div>
                <div>
                    <label for="itemsPerPage">Items per page:</label>
                    <select name="itemsPerPage" id="itemsPerPageSelect"> <?php
                        $itemsPerPageOptions = [12, 24, 48, 96];
                        echo "<option value=\"$count\" selected>$count</option>";
                        unset($itemsPerPageOptions[$count/12 - 1]);
                        foreach ($itemsPerPageOptions as $value) {
                            echo "<option value=\"$value\">$value</option>";
                        }
                    ?> </select>
                </div>
            </div>

            <div class="productList">
                <?php productList($results, $this->basePath); ?>
            </div>

            <?php if ($totalPages > 1) { ?>
            <div id="pageSelector">
                <span class="pageButton" value='<?= $page - 1; ?>'>
                    <i class="fa-solid fa-angle-left"></i> <span>Previous</span>
                </span>
                <?php
                $pagesBefore = $page - 1;
                $pagesAfter = $totalPages - $page;
                $maxPagesEachSide = 3;

                if ($pagesBefore > $maxPagesEachSide) {
                    $pagesBefore = $maxPagesEachSide;
                    echo "<span class='pageButton' value='1'>1</span>";
                    if ($page - $pagesBefore > 2) {
                        echo "<span class='spacer'>...</span>";
                    }
                }

                for ($i = $page - $pagesBefore; $i < $page; $i++) {
                    echo "<span class='pageButton" .
                        ($i == $page - $pagesBefore ? " hideable" : "") .
                        "' value='$i'>$i</span>";
                }

                echo "<span class=\"currentPage\">$page</span>";

                if ($pagesAfter > $maxPagesEachSide) {
                    $pagesAfter = $maxPagesEachSide;
                    $pagesAfterMaxReached = true;
                }

                for ($i = $page + 1; $i <= $page + $pagesAfter; $i++) {
                    echo "<span class='pageButton" . 
                        ($i == $page + $pagesAfter ? " hideable" : "") .
                        "' value='$i'>$i</span>";
                }

                if (isset($pagesAfterMaxReached)) {
                    if ($page + $pagesAfter < $totalPages - 1) {
                        echo "<span class='spacer'>...</span>";
                    }
                    echo "<span class='pageButton' value='$totalPages'>$totalPages</span>";
                }
                ?>
                <span value="<?= $page + 1; ?>" class="pageButton">
                    <span>Next</span> <i class="fa-solid fa-angle-right"></i>
                </span>
            </div>
            <?php } ?>
        <?php } ?>
    </div>
    <script src="/static/scripts/searchPage.js"></script>
    <?php require "include/footer.php"; ?>
</body>

</html>
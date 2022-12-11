<?php

/**
 * Search for a product.
 * 
 * @author Joe Baker
 * @copyright Copyright (c) 2022 JoeBlakeB, all rights reserved.
 */

require "model/productModel.php";

class SearchController extends AbstractController {
    /**
     * Search for products.
     */
    public function invoke() {
        $this->maxPathLength(1, 1);
        $searchTerm = isset($_GET["q"]) ? trim($_GET["q"]) : "";
        $count = isset($_GET["itemsPerPage"]) && is_numeric($_GET["itemsPerPage"]) ? $_GET["itemsPerPage"] : 10;
        $offset = isset($_GET["page"]) && is_numeric($_GET["page"]) ? $_GET["page"] * $count : 0;
        $sort = isset($_GET["sort"]) ? $_GET["sort"] : "relevance";

        try {
            $productModel = new ProductModel();
            $results = $productModel->search($sort, $count, $offset, $searchTerm);
            if (!empty($results)) {
                foreach ($results as &$product) {
                    var_dump($product);
                    echo "<hr>";
                }
            }
        }
        catch (PDOException $e) {
            $this->showError(500, "Internal Server Error", "An error occurred while trying to search for products.");
        }
    }
}

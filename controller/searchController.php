<?php

/**
 * Search for a product.
 * 
 * @author Joe Baker
 * @copyright Copyright (c) 2022 JoeBlakeB, all rights reserved.
 */

require_once("model/productModel.php");

class SearchController extends AbstractController {
    /**
     * Search for products.
     */
    public function invoke() {
        $this->maxPathLength(1, 1);
        $searchTerm = isset($_GET["q"]) ? trim($_GET["q"]) : "";
        $count = isset($_GET["itemsPerPage"]) && is_numeric($_GET["itemsPerPage"]) ? $_GET["itemsPerPage"] : 12;
        $page = isset($_GET["page"]) && 
                is_numeric($_GET["page"]) && 
                (int)$_GET["page"] > 0 ? $_GET["page"] : 1;
        $sort = isset($_GET["sort"]) ? $_GET["sort"] : "relevance";

        try {
            $productModel = new ProductModel();
            $results = $productModel->search($sort, $count, $page, $searchTerm);
            $totalPages = $results[1];
            $results = $results[0];
        }
        catch (PDOException $e) {
            $this->showError(500, "Internal Server Error", "An error occurred while trying to search for products.");
        }

        require_once("view/search.php");
    }
}

<?php

/**
 * Show a specific product.
 * 
 * @author Joe Baker
 * @copyright Copyright (c) 2022 JoeBlakeB, all rights reserved.
 */

require_once("model/productModel.php");

class ProductController extends AbstractController {
    /**
     * Show a specific product.
     */
    public function invoke() {
        $this->maxPathLength(2, 2);
        $productID = $this->uri[1];
        if (!is_numeric($productID)) {
            $this->pageNotFound();
        }
        try {
            $productModel = new ProductModel();
            $product = $productModel->getProductByID($productID);
            if ($product) {
                exit(require_once("product.php"));
            } else {
                $this->showError(404, "Product Not Found", "The product you requested could not be found.");
            }
        }
        catch (PDOException $e) {
            $this->showError(500, "Internal Server Error", "An error occurred while trying to get the product.");
        }
    }
}


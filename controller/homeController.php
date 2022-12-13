<?php

/**
 * Show the home page.
 * 
 * @author Joe Baker
 * @copyright Copyright (c) 2022 JoeBlakeB, all rights reserved.
 */

require_once("model/productModel.php");

class HomeController extends AbstractController {
    public function invoke() {
        if (!empty($this->uri)) {
            $this->pageNotFound();
        }

        try {
            $productModel = new ProductModel();
            $productsFeatured = $productModel->search("random")[0];
            $productsNew = $productModel->search("newest")[0];
        }
        catch (PDOException $e) {
            $message = "Please use the search bar above to find the things you're looking for.";
        }
        
        require_once("view/home.php");
    }
}

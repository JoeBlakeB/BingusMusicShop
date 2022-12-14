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
                if (isset($_GET["purchase"])) {
                    if (!isset($_SESSION["account"])) {
                        header("Location: $this->basePath/account/signin");
                    }
                    if (!$product->getStock()) {
                        $this->showError(400, "Product Out of Stock", "The product you just tried to purchase is currently out of stock.", $productID, "Back to Product");
                    }
                    exit($this->purchase($product));
                }
                require_once("product.php");
            } else {
                $this->showError(404, "Product Not Found", "The product you requested could not be found.");
            }
        }
        catch (PDOException $e) {
            $this->showError(500, "Internal Server Error", "An error occurred while trying to get the product.");
        }
    }

    /**
     * Purchase a product individually.
     * 
     * @param Product $product The product to purchase.
     */
    private function purchase($product) {
        if (!isset($_SESSION["account"])) {
            return header("Location: $this->basePath/account/signin");
        }

        require_once("model/accountModel.php");
        require_once("model/orderModel.php");
        
        try {
            $accountModel = new AccountModel();
            $account = $accountModel->getAccountByID($_SESSION["account"]["id"]);
            $cards = $account->getCards();
            $addresses = $account->getAddresses();
        }
        catch (PDOException $e) {
            $this->showError(500, "Internal Server Error", "An error occurred while trying to get your account information, please try agian later.");
        }

        for ($i = count($cards) - 1; $i >= 0; $i--) {
            if (!$cards[$i]->isExpired()) {
                $cards["card-".$cards[$i]->getID()] = $cards[$i];
            }
            unset($cards[$i]);
        }
        for ($i = count($addresses) - 1; $i >= 0; $i--) {
            $addresses["address-".$addresses[$i]->getID()] = $addresses[$i];
            unset($addresses[$i]);
        }

        if (empty($cards)) {
            $this->showError(400, "No Payment Methods", "You need to add a valid payment method to your account before you can purchase this product.", $this->basePath . "/account/payments", "Add a Payment Method");
        }

        if (empty($addresses)) {
            $this->showError(400, "No Addresses", "You need to add an address to your account before you can purchase this product.", $this->basePath . "/account/addresses", "Add an Address");
        }
        
        if (!empty($_POST)) {
            $quantity = $_POST["quantity"];
            $addressID = $_POST["address"];
            $cardID = $_POST["card"];
            $valid = [
                "quantity" => $quantity > 0 && $quantity <= $product->getStock(),
                "address" => isset($addresses[$addressID]),
                "card" => isset($cards[$cardID])
            ];
            if ($valid["quantity"] && $valid["address"] && $valid["card"]) {
                try {
                    $orderModel = new OrderModel();
                    $orderID = $orderModel->purchaseOneProduct($account, $product, $quantity, $addresses[$addressID], $cards[$cardID]);
                    if (!$orderID) {
                        return $this->showError(500, "Stock Too Low", "The stock has been updated since you last checked, please try again and order a lower quantity.");
                    }
                    return header("Location: $this->basePath/account/orders?success&orderID=$orderID");
                }
                catch (PDOException $e) {
                    return $this->showError(500, "Internal Server Error", "An error occurred while trying to purchase the product, please try again later.");
                }
            }
        }

        require_once("purchase.php");
    }
}


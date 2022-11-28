<?php

/**
 * Show the pages for the site control.
 * 
 * @author Joe Baker
 * @copyright Copyright (c) 2022 JoeBlakeB, all rights reserved.
 */

class AdminController extends AbstractController {
    /**
     * Decide which admin page to show.
     * If the user is not admin then show an error.
     * 
     * @param array $uri The path of the page.
     */
    public function invoke() {
        if (!isset($_SESSION["account"])) {
            return header("Location: $this->basePath/account/signin");
        }
        if (!$_SESSION["account"]["isAdmin"]) {
            return $this->showError(403, "Forbidden", "You do not have permission to access this page.");
        }

        if (!isset($this->uri[1])) {
            return $this->overview();
        }
        $this->runPageMethod($this->uri[1]);
    }

    /**
     * Show the admin details page.
     */
    public function overview() {
        require "admin/overview.php";
    }

    /**
     * Show the user management page.
     * - Verify, make admin, and delete users
     * - View specific user details
     * - View a list of all users
     */
    public function usersPage() {
        $this->maxPathLength(2, $this->uri);
        require "model/accountModel.php";
        $accountModel = new AccountModel();
        if (isset($_GET["accountID"])) {
            $account = $accountModel->getAccountByID($_GET["accountID"]);
            if ($account == null) {
                http_response_code(404);
            }
        }

        try {
            if (isset($_POST["action"]) && isset($_GET["accountID"])) {
                switch ($_POST["action"]) {
                    case "Verify User":
                        $actionMessage = "Verified user #" . $account->getID();
                        $account->verify();
                        break;
                    case "Make Admin":
                        $actionMessage = "Made user #" . $account->getID() . " an admin.";
                        $account->setAdmin(true);
                        break;
                    case "Remove Admin":
                        $actionMessage = "User #" . $account->getID() . " is no longer an admin.";
                        $account->setAdmin(false);
                        break;
                    case "Delete User":
                        $actionMessage = "Deleted user #" . $account->getID();
                        $account->delete();
                        break;
                    default:
                        $actionMessage =  "Invalid action.";
                        http_response_code(400);
                        break;
                }
            }
        }
        catch (Exception $e) {
            $actionMessage = "An error occured while performing that action.";
            http_response_code(500);
        }

        $allAccounts = $accountModel->getAllAccounts();
        require "admin/users.php";
    }

    /**
     * Select which products management page to show
     * all, new, and edit.
     */
    public function productsPage() {
        $this->maxPathLength(3, $this->uri);
        if (!isset($this->uri[2])) {
            return $this->productsAll();
        }
        $this->runPageMethod($this->uri[2], "PageProducts");
    }

    /**
     * Show the page to view all products.
     */
    public function productsAll() {
        require "model/productModel.php";
        $productModel = new ProductModel();
        $products = $productModel->getAllProducts();
        require "admin/products/all.php";
    }

    /**
     * Show the page to add a new product.
     */
    public function newPageProducts() {
        if (!empty($_POST)) {
            $valid = $this->validateProduct($_POST);
            if ($valid[0]) {
                require "model/productModel.php";
                $productModel = new ProductModel();
                $product = $productModel->createProduct($_POST["name"], $_POST["description"], $_POST["price"], $_POST["stock"]);
                if ($product == null) {
                    $error = "An error occured while creating the product.";
                    http_response_code(500);
                } else {
                    return header("Location: $this->basePath/admin/products/edit?new=true&id=" . $product->getID());
                }
            }
            else {
                $error = "Invalid product details. <noscript>Enable JavaScript to see errors.</noscript>";
                http_response_code(400);
            }
        }
        $edit = false;
        require "admin/products/edit.php";
    }

    /**
     * Show the page to edit a product.
     */
    public function editPageProducts() {
        require "model/productModel.php";
        $productModel = new ProductModel();
        $product = $productModel->getProductByID($_GET["id"]);
        if ($product == null) {
            $this->pageNotFound();
        }
        if (!empty($_POST)) {
            $valid = $this->validateProduct($_POST);
            if ($valid[0]) {
                $error = $product->update($_POST);
                if ($error === null) {
                    $success = "Product updated successfully.";
                }
            }
            else {
                $error = "Invalid product details. <noscript>Enable JavaScript to see errors.</noscript>";
                http_response_code(400);
            }
        }
        $edit = true;
        require "admin/products/edit.php";
    }

    /**
     * Validate the product data.
     * 
     * @param array $data The data to validate.
     * @return array of bools for each field.
     */
    private function validateProduct($data) {
        $valid = [];
        $valid["name"] = (
            strlen($data["name"]) > 0 &&
            strlen($data["name"]) <= 128);
        $valid["description"] = (
            strlen($data["description"]) < 2 ** 16 - 1);
        $valid["price"] = (
            is_numeric($data["price"]) &&
            $data["price"] > 0 &&
            $data["price"] < 10 ** 7);
        $valid["stock"] = (
            is_numeric($data["stock"]) &&
            $data["stock"] >= 0 &&
            $data["stock"] < 10 ** 7);
        $valid[0] = (
            $valid["name"] &&
            $valid["description"] &&
            $valid["price"] &&
            $valid["stock"]);
        return $valid;
    }

    /**
     * Delete a product.
     */
    public function deletePageProducts() {
        require "model/productModel.php";
        $productModel = new ProductModel();
        $product = $productModel->getProductByID($_GET["id"]);
        if ($product == null) {
            $this->pageNotFound();
        }
        if (isset($_GET["confirm"])) {
            $product->delete();
            try {
                return header("Location: $this->basePath/admin/products");
            }
            catch (PDOException $e) {
                $error = "An error occured while deleting the product.";
                http_response_code(500);
            }
        }
        else if (isset($_GET["hide"])) {
            $product->setStock(0);
            return header("Location: $this->basePath/admin/products");
        }
        require "admin/products/delete.php";
    }
}


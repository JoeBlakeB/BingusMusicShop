<?php

/**
 * The class for managing products.
 * 
 * @author Joe Baker
 * @copyright Copyright (c) 2022 JoeBlakeB, all rights reserved.
 */

include "model/abstractModel.php";

class ProductModel extends AbstractModel {
    /**
     * Get all products from the products table
     * and the number of images for each product in the images table.
     * 
     * @return array The products as objects.
     */
    public function getAllProducts() {
        $stmt = $this->dbh->prepare(
            "SELECT products.*, COUNT(imageID) as imageCount
            FROM products LEFT JOIN images 
            ON products.productID = images.productID
            GROUP BY products.productID;");
        $stmt->execute();
        return $this->createObjectArray($stmt->fetchAll(), Product::class);
    }

    /**
     * Get a product by its id.
     * 
     * @param int $productID The id of the product.
     * @return Product The product as an object.
     */
    public function getProductByID($productID) {
        $stmt = $this->dbh->prepare(
            "SELECT products.*, COUNT(imageID) as imageCount
            FROM products LEFT JOIN images 
            ON products.productID = images.productID
            WHERE products.productID = :productID
            GROUP BY products.productID;");
        $stmt->execute(["productID" => $productID]);
        $product = $stmt->fetch();
        if ($product) {
            return new Product($this->dbh, $product);
        }
        return null;
    }
}


// TODO, changed to private so add methods and then use them in admin edit+all
class Product implements ModelObjectInterface {
    private $dbh;
    private $id;
    private $name;
    private $description;
    private $price;
    private $stock;
    private $imageCount;

    public function __construct(&$dbh, $data) {
        $this->dbh = $dbh;
        $this->id = $data["productID"];
        $this->name = $data["name"];
        $this->description = $data["description"];
        $this->price = $data["price"];
        $this->stock = $data["stock"];
        $this->imageCount = $data["imageCount"];
    }

    /**
     * @return int The product's id.
     */
    public function getID() {
        return $this->id;
    }

    /**
     * @return string The product's name escaped for html.
     */
    public function getName() {
        return htmlspecialchars($this->name);
    }

    /**
     * @return string The product's description escaped for html.
     */
    public function getDescription() {
        return htmlspecialchars($this->description);
    }

    /**
     * @return string The price formatted as a currency.
     */
    public function getPriceStr() {
        return "£" . number_format($this->price, 2);
    }

    /**
     * @return int The price as an integer.
     */
    public function getPriceInt() {
        return $this->price;
    }

    /**
     * @return int The product's stock.
     */
    public function getStock() {
        return $this->stock;
    }

    /**
     * @return int The number of images for the product.
     */
    public function getImageCount() {
        return $this->imageCount;
    }

    /**
     * Update the product's details.
     * 
     * @param array $data The data to update.
     * @return string An error message if the update failed.
     *                empty string for nothing changed,
     *                null for success.
     */
    public function update($data) {
        try {
            $stmt = $this->dbh->prepare(
                "UPDATE products
                SET name = :name,
                    description = :description,
                    price = :price,
                    stock = :stock
                WHERE productID = :productID;");
            $stmt->execute([
                "name" => $data["name"],
                "description" => $data["description"],
                "price" => $data["price"],
                "stock" => $data["stock"],
                "productID" => $this->id
            ]);
            if ($stmt->rowCount() == 0) {
                return "";
            }
        }
        catch (PDOException $e) {
            return "Failed to update product: " . $e->getMessage();
        }
        $this->name = $data["name"];
        $this->description = $data["description"];
        $this->price = $data["price"];
        $this->stock = $data["stock"];
        return null;
    }
}
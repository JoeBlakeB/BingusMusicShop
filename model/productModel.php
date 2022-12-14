<?php

/**
 * The class for managing products.
 * 
 * @author Joe Baker
 * @copyright Copyright (c) 2022 JoeBlakeB, all rights reserved.
 */

require_once("model/abstractModel.php");

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
     * Search for products.
     * Supported sort methodds: 
     * - relevance (default)
     *    -- Sort by relevance to the search term.
     *    -- LIKE complete search term in name ranks highest
     *    -- LIKE single words in description ranks lowest
     * - name + _asc and _desc
     * - price + _asc and _desc
     * - newest and oldest
     * 
     * @param string $sort The sort order.
     * @param int $count The number of products to return.
     * @param int $page The page of the products to return.
     * @param string $searchTerm The search term.
     * @return array $results and $totalPages
     * - The products as objects.
     * - The total number of pages.
     */
    public function search($sort, $count=4, $page=1, $searchTerm="") {
        $imagesTableSql = "(SELECT productID, fileHash, fileType
                FROM images
                WHERE imageID in (
                    SELECT MAX(imageID)
                    FROM images
                    GROUP BY productID
            )) AS images";

        $sortMethods = [
            "name_asc" => "name ASC, price DESC",
            "name_desc" => "name DESC, price DESC",
            "price_asc" => "price ASC, name ASC",
            "price_desc" => "price DESC, name ASC",
            "newest" => "productID DESC",
            "oldest" => "productID ASC",
            "random" => "RAND()"
        ];
        $sort = in_array($sort, array_keys($sortMethods)) ? $sortMethods[$sort] : (
            strpos($searchTerm, " ") && substr_count($searchTerm, " ") < 8 ? "relevance" : "productID DESC");

        if ($sort == "relevance") {
            $this->searchTableIndex = 0;
            $this->searchTableSql = "";
            $this->searchTableParams = [];
            $this->genRelevanceSelect($searchTerm, 1000, 200);
            $this->searchTableSql .= " UNION ";
            $this->genRelevanceSelect(str_replace(" ", "%", $searchTerm), 250, 80);
            foreach (explode(" ", $searchTerm) as $searchSegment) {
                $len = strlen($searchSegment);
                $len *= (int)($len > 6 ? 1.5 : 1);
                $this->searchTableSql .= " UNION ";
                $this->genRelevanceSelect($searchSegment, $len * 8, $len);
            }

            $stmt = $this->dbh->prepare("SELECT search.productID, search.name, 
                    search.price, search.stock, images.fileHash, 
                    images.fileType, SUM(search.relevance) AS relevance
                FROM ( $this->searchTableSql ) AS search LEFT JOIN $imagesTableSql
                ON search.productID = images.productID
                GROUP BY search.productID, search.name, 
                    search.price, search.stock, images.fileHash, images.fileType
                HAVING relevance > " . (30 + (2 * strlen($searchTerm))) . "
                AND search.stock > 0
                ORDER BY relevance DESC, productID DESC");
            $stmt->execute($this->searchTableParams);
        }
        else {
            $stmt = $this->dbh->prepare(
                "SELECT products.productID, products.name, products.price, 
                    images.fileHash, images.fileType
                FROM products LEFT JOIN $imagesTableSql
                ON products.productID = images.productID
                WHERE name LIKE :searchTerm
                AND products.stock > 0
                ORDER BY $sort");
            $stmt->execute(["searchTerm" => "%$searchTerm%"]);
        }

        $data = $stmt->fetchAll();
        $totalPages = ceil(count($data) / $count);
        $data = array_slice($data, ($page-1)*$count, $count);

        return [$this->createObjectArray($data, Product::class), $totalPages];
    }

    /**
     * Generate a SELECT for the relevance search.
     * Sets the $searchTableSql and $searchTableParams properties.
     * 
     * @param string $searchSegment The search term or segment.
     * @param int $relevanceName The relevance if in the name.
     * @param int $relevanceDesc The relevance if in the description.
     */
    private function genRelevanceSelect($searchSegment, $relevanceName, $relevanceDesc) {
        $this->searchTableSql .= 
            "SELECT productID, name, price, stock, $relevanceName AS relevance FROM products
            WHERE name LIKE :param$this->searchTableIndex
            UNION
            SELECT productID, name, price, stock, $relevanceDesc AS relevance FROM products
            WHERE description LIKE :param$this->searchTableIndex";
        $this->searchTableParams["param" . ($this->searchTableIndex++)] = "%$searchSegment%";
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

    /**
     * Create a new product
     * 
     * @param string $name The name of the product.
     * @param string $description The description of the product.
     * @param float $price The price of the product.
     * @param int $stock The stock of the product.
     * @return Product The product as an object.
     */
    public function createProduct($name, $description, $price, $stock) {
        $stmt = $this->dbh->prepare(
            "INSERT INTO products (name, description, price, stock)
            VALUES (:name, :description, :price, :stock);");
        $stmt->execute([
            "name" => $name,
            "description" => $description,
            "price" => $price,
            "stock" => $stock
        ]);
        return $this->getProductByID($this->dbh->lastInsertId());
    }

    /**
     * Store an images metadata in the database.
     * 
     * @param int $productID The id of the product.
     * @param string $fileHash The images file hash.
     * @param string $fileType The images file type.
     * @return int The id of the image.
     */
    public function addImage($productID, $image, $type) {
        $stmt = $this->dbh->prepare(
            "INSERT INTO images (productID, fileHash, fileType)
            VALUES (:productID, :fileHash, :fileType);");
        $stmt->execute([
            "productID" => $productID,
            "fileHash" => $image,
            "fileType" => $type
        ]);
        return $this->dbh->lastInsertId();
    }

    /**
     * Delete an images from the database, and delete the file
     * if it is not used by another product.
     * 
     * @param int $imageID The id of the image.
     */
    public function deleteImage($imageID) {
        $stmt = $this->dbh->prepare(
            "SELECT fileHash, fileType FROM images
            WHERE imageID = :imageID;
            DELETE FROM images WHERE imageID = :imageID;");
        $stmt->execute(["imageID" => $imageID]);
        $image = $stmt->fetch();
        
        $stmt = $this->dbh->prepare(
            "SELECT COUNT(imageID) as imageCount FROM images
            WHERE fileHash = :fileHash;");
        $stmt->execute(["fileHash" => $image["fileHash"]]);
        $imageCount = $stmt->fetch()["imageCount"];
        if ($imageCount == 0) {
            $fileDir = dirname(dirname(__FILE__)) . "/images/";
            unlink($fileDir . $image["fileHash"] . "." . $image["fileType"]);
        }
    }

    /**
     * Make an image the main image for a product,
     * Deletes and re adds the metadata to the database.
     * 
     * @param int $imageID The new id of the image.
     */
    public function setMainImage($imageID) {
        $stmt = $this->dbh->prepare(
            "INSERT INTO images (productID, fileHash, fileType)
            SELECT productID, fileHash, fileType FROM images
            WHERE imageID = :imageID;
            DELETE FROM images WHERE imageID = :imageID;");
        $stmt->execute(["imageID" => $imageID]);
        return $this->dbh->lastInsertId();
    }
}


class Product extends ProductModel implements ModelObjectInterface {
    private $id;
    private $name;
    private $description;
    private $price;
    private $stock;
    private $imageCount;
    private $images;

    public function __construct(&$dbh, $data) {
        $this->dbh = $dbh;
        $this->id = $data["productID"];
        $this->name = $data["name"];
        $this->description = isset($data["description"]) ? $data["description"] : "";
        $this->price = $data["price"];
        $this->stock = isset($data["stock"]) ? $data["stock"] : 0;
        $this->quantity = isset($data["quantity"]) ? $data["quantity"] : 0;
        if (isset($data["fileHash"])) {
            $this->images = [[
                "fileHash" => $data["fileHash"],
                "fileType" => $data["fileType"],
                "url" => "/images/" . $data["fileHash"]
                              . "." . $data["fileType"]
            ]];
            $this->imageCount = 1;
        }
        else if (isset($data["imageCount"])) {
            $this->imageCount = $data["imageCount"];
        }
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
     * @return double The price as a double.
     */
    public function getPriceDouble() {
        return $this->price;
    }

    /**
     * @return int The product's stock, or 0 if not set.
     */
    public function getStock() {
        return $this->stock;
    }

    /**
     * Check the stock hasnt been changed since the product was loaded,
     * then decrease the stock if it is possible.
     * 
     * @param int $quantity The quantity to decrease the stock by.
     * @return bool If the stock was decreased
     */
    public function decreaseStock($quantity) {
        $stmt = $this->dbh->prepare(
            "SELECT stock FROM products
            WHERE productID = :productID
            FOR UPDATE;");
        $stmt->execute(["productID" => $this->id]);
        $stock = $stmt->fetch()["stock"];
        if ($stock < $quantity) {
            return false;
        }
        $stmt = $this->dbh->prepare(
            "UPDATE products
            SET stock = stock - :quantity
            WHERE productID = :productID;");
        $stmt->execute([
            "productID" => $this->id,
            "quantity" => $quantity
        ]);
        return true;
    }

    /**
     * @return int The quantity of the product if bought, 0 if not.
     */
    public function getQuantity() {
        return $this->quantity;
    }

    /**
     * @return int The number of images for the product.
     */
    public function getImageCount() {
        return $this->imageCount;
    }

    /**
     * Get the images for the product.
     * Will get only the first image if the product was got from a search.
     * 
     * @return array An array of arrays containing the images metadata.
     */
    public function getImages() {
        if (!isset($this->images)) {
            $stmt = $this->dbh->prepare(
                "SELECT * FROM images
            WHERE productID = :productID
            ORDER BY imageID DESC;"
            );
            $stmt->execute(["productID" => $this->id]);
            $images = $stmt->fetchAll();
            foreach ($images as &$image) {
                $image["url"] = "/images/" . $image["fileHash"] . "." . $image["fileType"];
            }
            $this->images = $images;
        }
        return $this->images;
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

    /** 
     * Set the stock of a product, used when
     * an order is placed and when the product is hidden.
     */
    public function setStock($stock) {
        $stmt = $this->dbh->prepare(
            "UPDATE products
            SET stock = :stock
            WHERE productID = :productID;");
        $stmt->execute([
            "stock" => $stock,
            "productID" => $this->id
        ]);
        $this->stock = $stock;
    }

    /**
     * Delete the products images, then the actual product.
     */
    public function delete() {
        $stmt = $this->dbh->prepare(
            "SELECT imageID FROM images
            WHERE productID = :productID;");
        $stmt->execute(["productID" => $this->id]);
        $images = $stmt->fetchAll();
        foreach ($images as $image) {
            $this->deleteImage($image["imageID"]);
        }
        $stmt = $this->dbh->prepare(
            "DELETE FROM products
            WHERE productID = :productID;");
        $stmt->execute(["productID" => $this->id]);
    }
}

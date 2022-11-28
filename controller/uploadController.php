<?php

/**
 * Upload images for products.
 *
 * 
 * @author Joe Baker
 * @copyright Copyright (c) 2022 JoeBlakeB, all rights reserved.
 */

class UploadController extends AbstractController {
    /**
     * Upload an image and save it,
     * then return a JSON response.
     */
    public function invoke() {
        if (!isset($_SESSION["account"]) ||
                !$_SESSION["account"]["isAdmin"]) {
            return $this->showError(403, "Forbidden", "You do not have permission to access this page.");
        }

        if (isset($this->uri[1])) {
            $this->pageNotFound();
        }

        if ($_SERVER["REQUEST_METHOD"] != "POST") {
            $this->showError(405, "Method Not Allowed", "You can only upload images with POST.");
        }

        if (empty($_FILES["image"])) {
            $this->showError(400, "Bad Request", "No file was uploaded.");
        }

        if (empty($_POST["productID"])) {
            $this->showError(400, "Bad Request", "No product ID was provided.");
        }

        // Get image details
        $productID = $_POST["productID"];
        $image = $_FILES["image"];
        $fileHash = md5_file($image["tmp_name"]);
        $fileType = explode("/", $image["type"])[1];
        $fileName = $fileHash . "." . $fileType;
        $fileDir = dirname(dirname(__FILE__)) . "/images/";

        if (!file_exists($fileDir)) {
            mkdir($fileDir, 0777, true);
        }
        
        // Check image is valid
        if (!preg_match("/^(png|jpg|jpeg|gif)$/", $fileType)) {
            $this->respondWithJson([
                "success" => false,
                "message" => "Invalid file type."
            ], 400);
        }

        if ($image["size"] > 2 * 1024 * 1024) {
            $this->respondWithJson([
                "success" => false,
                "message" => "File is too large."
            ], 400);
        }
        
        // Move image to images folder
        if (move_uploaded_file($image["tmp_name"], $fileDir . $fileName)) {
            try {
                require "model/productModel.php";
                $productModel = new ProductModel();
                $productModel->addImage($productID, $fileHash, $fileType);
                $this->respondWithJson([
                    "success" => true,
                    "message" => "File uploaded successfully.",
                    "file" => $fileName
                ]);
            }
            catch (PDOException $e) {
                // If database fails, delete the image.
                unlink($fileDir . $fileName);
                $this->respondWithJson([
                    "success" => false,
                    "message" => "Failed to add image to database."
                ], 500);
            }
        }
        else {
            $this->respondWithJson([
                "success" => false,
                "message" => "An error occurred while uploading the file."
            ], 500);
        }
    }
}
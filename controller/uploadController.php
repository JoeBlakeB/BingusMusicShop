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

        $this->maxPathLength(1, 1);

        if ($_SERVER["REQUEST_METHOD"] != "POST") {
            $this->showError(405, "Method Not Allowed", "You can only upload images with POST.");
        }

        if (empty($_POST["productID"])) {
            $this->respondWithJson([
                "success" => false,
                "message" => "No ID was provided."
            ], 400);
        }

        if (empty($_FILES["image"]) || $_FILES["image"]["tmp_name"] == "") {
            $this->respondWithJson([
                "success" => false,
                "message" => "No image was uploaded."
            ], 400);
        }

        $image = $_FILES["image"];

        if ($image["size"] > 2 * 1024 * 1024) {
            $this->respondWithJson([
                "success" => false,
                "message" => "File is too large."
            ], 400);
        }

        $fileType = explode("/", $image["type"])[1];

        if (!preg_match("/^(png|jpg|jpeg|gif|webp)$/", $fileType)) {
            $this->respondWithJson([
                "success" => false,
                "message" => "Invalid file type."
            ], 400);
        }

        $productID = $_POST["productID"];
        $fileHash = md5_file($image["tmp_name"]);
        $fileName = $fileHash . "." . $fileType;
        $fileDir = dirname(dirname(__FILE__)) . "/images/";
        
        // Save metadata to database, then move the file.
        // If the file cannot be moved, then delete the metadata.
        try {
            require_once("model/productModel.php");
            $productModel = new ProductModel();
            $imageID = $productModel->addImage($productID, $fileHash, $fileType);

            if (!file_exists($fileDir)) {
                mkdir($fileDir, 0777, true);
            }

            if (move_uploaded_file($image["tmp_name"], $fileDir . $fileName)) {
                $this->respondWithJson([
                    "success" => true,
                    "message" => "Image uploaded successfully.",
                    "fileName" => $fileName,
                    "imageID" => $imageID
                ]);
            }
            else {
                $productModel->deleteImage($imageID);
                $this->respondWithJson([
                    "success" => false,
                    "message" => "An error occurred while uploading the file."
                ], 500);
            }
        }
        catch (PDOException $e) {
            $message = "Failed to add image to database.";
            $status = 500;

            if (strpos($e->getMessage(), "SQLSTATE[23000]") !== false) {
                $message += " The product may have been deleted.";
                $status = 404;
            }
            
            $this->respondWithJson([
                "success" => false,
                "message" => $message
            ], $status);
        }
    }
}
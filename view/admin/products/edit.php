<!DOCTYPE html>
<html lang="en">

<head>
    <?php
    if (!isset($valid)) {
        $valid = [
            "name" => true,
            "description" => true,
            "price" => true,
            "stock" => true
        ];
    }
    if ($edit) {
        $productName = $product->getName();
        $productPrice = $product->getPriceDouble();
        $productStock = $product->getStock();
        $productDescription = $product->getDescription();
    } else {
        $productName = isset($_POST["name"]) ? $_POST["name"] : "";
        $productPrice = isset($_POST["price"]) ? $_POST["price"] : "";
        $productStock = isset($_POST["stock"]) ? $_POST["stock"] : "";
        $productDescription = isset($_POST["description"]) ? $_POST["description"] : "";
    }
    if (empty($success) && isset($_GET["new"])) {
        $success = "Product created successfully.";
    }
    $title = ($edit ? $product->getName() : "New Product") . " - Admin";
    require_once("include/head.php");
    ?>
</head>

<body>
    <?php require_once("include/header.php"); ?>

    <div class="content multipleContents">
        <h1>Admin - <?= $edit ?
            "Edit a Product" :
            "Add a new Product"; ?>
        </h1>

        <p><a href=".." class="button">
            Back to Admin Overview
        </a></p>
        <p><a href="." class="button">
            Back to All Products
        </a></p>

        <?php
        if ($edit) {
        ?>
            <p><a href="<?= $this->basePath ?>/product/<?= $product->getId() ?>" class="button">
                View Product
            </a></p>
            <p><a href="delete?id=<?= $product->getID(); ?>" class="button">
                Delete Product
            </a></p>
        <?php
        }

        if (!empty($error)) {
            echo "<p class='error'>$error</p>";
        }

        if (!empty($success)) {
            echo "<p class='success'>$success</p>";
        }
        ?>

        <h2><?= $edit ? $product->getName() : ""; ?></h2>
    </div>

    <form action="<?= $edit ? "edit?id=" . $product->getID() : "new"; ?>" method="post" class="editForm multipleContents">
        <div class="grid threeColumns uneven">
            <div class="inputContainer <?= $valid["name"] ? "" : "inputError"; ?>" id="nameContainer">
                <label for="name">Name</label>
                <input type="text" name="name" id="name" value="<?= $productName; ?>" placeholder="Product Name" required>
            </div>

            <div class="inputContainer <?= $valid["price"] ? "" : "inputError"; ?>" id="priceContainer">
                <label for="price">Price</label><br>
                <span class="priceInput">£
                    <input type="number" name="price" id="price" value="<?= $productPrice; ?>" step="0.01" min="0.01" max="9999999" placeholder="0.00" required>
                </span>
            </div>

            <div class="inputContainer <?= $valid["stock"] ? "" : "inputError"; ?>" id="stockContainer">
                <label for="stock">Stock</label>
                <input type="number" name="stock" id="stock" value="<?= $productStock; ?>" min="0" max="9999999" placeholder="0" required>
            </div>
        </div>

        <div class="inputContainer <?= $valid["description"] ? "" : "inputError"; ?>" id="descriptionContainer">
            <label for="description">Description</label>
            <textarea name="description" id="description" placeholder="Product Description"><?= $productDescription; ?></textarea>
        </div>

        <input type="submit" value="<?= $edit ? "Save Changes" : "Add Product"; ?>">
    </form>
    <script src="/static/scripts/productEditValidation.js"></script>

    <?php if ($edit) { ?>
        <div class="content" id="imageManagement">
            <h2>Images</h2>
            <h3>Add a New Image</h3>
            <input type="file" accept="image/png,image/jpeg,image/gif,image/webp" name="imageInput" id="imageInput" productID="<?= $product->getID(); ?>">
            <label for="imageInput" class="button">Select an Image</label>
            <p>Or drag and drop an image here...</p>
            <p id="statusMessage"></p>
            <h3 id="currentImagesHeader" <?php
            $images = $product->getImages();
            if (count($images) == 0) {
                echo "style='display: none;'";
            }
            ?>>Current Images</h3>
            <div id="currentImages">
                <?php
                $primary = true;
                foreach ($images as $image) { ?>
                    <div class="imageContainer" id="image-<?= $image["imageID"]; ?>">
                        <img src="<?= $image["url"]; ?>" alt="Image #<?= $image["imageID"]; ?>">
                        <div class="imageContainerButtons">
                            <button class="button <?= $primary ? "primaryImage" : ""; ?>" 
                                onclick="setPrimaryImage(<?= $image["imageID"]; ?>)">
                                <?= $primary ? "Primary Image" : "Set as Primary"; ?></button>
                            <button class="button" onclick="deleteImage(<?= $image["imageID"]; ?>);">Delete</button>
                        </div>
                    </div>
                    <?php 
                    $primary = false; 
                } ?>
            </div>
        </div>
        <script src="/static/scripts/imageManagement.js"></script>
    <?php } ?>
    <?php require_once("include/footer.php"); ?>
</body>

</html>

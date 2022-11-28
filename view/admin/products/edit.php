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
        $productPrice = $product->getPriceInt();
        $productStock = $product->getStock();
        $productDescription = $product->getDescription();
    }
    else {
        $productName = isset($_POST["name"]) ? $_POST["name"] : "";
        $productPrice = isset($_POST["price"]) ? $_POST["price"] : "";
        $productStock = isset($_POST["stock"]) ? $_POST["stock"] : "";
        $productDescription = isset($_POST["description"]) ? $_POST["description"] : "";
    }
    if (empty($success) && isset($_GET["new"])) {
        $success = "Product created successfully.";
    }
    $title = ($edit ? $product->getName() : "New Product") . " - Admin";
    require "head.php";
    ?>
</head>

<body>
    <?php require "header.php"; ?>

    <div class="basicContent">
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

    <form action="<?= $edit ? "edit?id=" . $product->getID() : "new"; ?>" method="post" class="productEditForm">
        <div class="grid threeColumns">
            <div class="inputContainer <?= $valid["name"] ? "" : "inputError" ?>" id="nameContainer">
                <label for="name">Name</label>
                <input type="text" name="name" id="name" value="<?= $productName; ?>" placeholder="Product Name" required>
            </div>

            <div class="inputContainer <?= $valid["price"] ? "" : "inputError" ?>" id="priceContainer">
                <label for="price">Price</label><br>
                <span class="priceInput">£
                    <input type="number" name="price" id="price" value="<?= $productPrice; ?>" step="0.01" min="0.01" max="9999999" placeholder="0.00" required>
                </span>
            </div>

            <div class="inputContainer <?= $valid["stock"] ? "" : "inputError" ?>" id="stockContainer">
                <label for="stock">Stock</label>
                <input type="number" name="stock" id="stock" value="<?= $productStock; ?>" min="0" max="9999999" placeholder="0" required>
            </div>
        </div>

        <div class="inputContainer <?= $valid["description"] ? "" : "inputError" ?>" id="descriptionContainer">
            <label for="description">Description</label>
            <textarea name="description" id="description" placeholder="Product Description"><?= $productDescription; ?></textarea>
        </div>

        <input type="submit" value="<?= $edit ? "Save Changes" : "Add Product"?>">
    </form>
    <script src="/static/scripts/productEditValidation.js"></script>

    <?php if ($edit) { ?>
    <div class="basicContent">
        <h2>Images</h2>
        <p>TODO</p>
    </div>
    <?php } ?>
</body>

</html>
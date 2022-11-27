<!DOCTYPE html>
<html lang="en">

<head>
    <?php
    $title = $product->getName() . " - Admin";
    require "head.php";
    ?>
</head>

<body>
    <?php require "header.php"; ?>

    <div class="basicContent">
        <h1>Admin - Edit a Product</h1>

        <p><a href=".." class="button">
                Back to Admin Overview
            </a></p>
        <p><a href="." class="button">
                Back to All Products
            </a></p>

        <?php
        if (!empty($error)) {
            echo "<p class='error'>$error</p>";
        }

        if (!empty($success)) {
            echo "<p class='success'>$success</p>";
        }
        ?>

        <h2><?= $product->getName(); ?></h2>
    </div>

    <form action="edit?id=<?= $product->getID(); ?>" method="post" class="productEditForm">
        <div class="grid threeColumns">
            <div class="inputContainer <?= $valid["name"] ? "" : "inputError" ?>" id="nameContainer">
                <label for="name">Name</label>
                <input type="text" name="name" id="name" value="<?= $product->getName(); ?>" required>
            </div>

            <div class="inputContainer <?= $valid["price"] ? "" : "inputError" ?>" id="priceContainer">
                <label for="price">Price</label><br>
                <span class="priceInput">£
                    <input type="number" name="price" id="price" value="<?= $product->getPriceInt(); ?>" step="0.01" required>
                </span>
            </div>

            <div class="inputContainer <?= $valid["stock"] ? "" : "inputError" ?>" id="stockContainer">
                <label for="stock">Stock</label>
                <input type="number" name="stock" id="stock" value="<?= $product->getStock(); ?>" required>
            </div>
        </div>

        <div class="inputContainer <?= $valid["description"] ? "" : "inputError" ?>" id="descriptionContainer">
            <label for="description">Description</label>
            <textarea name="description" id="description"><?= $product->getDescription(); ?></textarea>
        </div>

        <input type="submit" value="Save Changes">
    </form>
    <script src="/static/scripts/productEditValidation.js"></script>

    <div class="basicContent">
        <h2>Images</h2>
        <p>TODO</p>
    </div>

</body>

</html>
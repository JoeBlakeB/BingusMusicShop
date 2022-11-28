<!DOCTYPE html>
<html lang="en">

<head>
    <?php
    $title = "Delete " . $product->getName() . " - Admin";
    require "head.php";
    ?>
</head>

<body>
    <?php require "header.php"; ?>

    <div class="basicContent">
        <h1>Admin - Delete Product</h1>

        <p><a href=".." class="button">
                Back to Admin Overview
            </a></p>
        <p><a href="." class="button">
                Back to All Products
            </a></p>
        <p><a href="edit?<?= $product->getID(); ?>" class="button">
                Back to Product Edit
            </a></p>

        <?php
        if (!empty($error)) {
            echo "<p class='error'>$error</p>";
        }
        ?>

        <h2>Delete <?= $product->getName(); ?></h2>

        <p>
            Deleting a product is permanent and cannot be undone.<br>
            Anyone who ordered the product will no longer be able to see it.<br>
            You can hide the product from the store by setting the stock to 0.
        </p>

        <p><a href="delete?confirm=true&id=<?= $product->getID(); ?>" class="button">
            Confirm Delete
        </a></p>
        <p><a href="delete?hide=true&id=<?= $product->getID(); ?>" class="button">
            Hide Product
        </a></p>
        <p><a href="edit?id=<?= $product->getID(); ?>" class="button">
            Cancel
        </a></p>
    </div>
</body>

</html>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php
    $title = "All Products - Admin";
    require_once("include/head.php");
    ?>
</head>

<body>
    <?php require_once("include/header.php"); ?>

    <div class="content">
        <h1>Admin - All Products</h1>

        <p><a href="<?= $this->basePath ?>/admin/" class="button">
            Back to Admin Overview
        </a></p>

        <p><a href="<?= $this->basePath ?>/admin/products/new" class="button">
            Add a New Product
        </a></p>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Product Name</th>
                    <th>Images</th>
                    <th>Price</th>
                    <th>Stock</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product) { ?>
                    <tr onclick="window.location.href='<?= $this->basePath ?>/admin/products/edit?id=<?= $product->getID(); ?>';">
                        <td><?= $product->getID() ?></td>
                        <td class="big"><?= $product->getName(); ?></td>
                        <td><?= $product->getImageCount(); ?></td>
                        <td><?= $product->getPriceStr(); ?></td>
                        <td><?= $product->getStock(); ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <?php require_once("include/footer.php"); ?>
</body>

</html>
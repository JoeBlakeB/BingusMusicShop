<!DOCTYPE html>
<html lang="en">

<head>
    <?php
    $title = "All Products - Admin";
    require "head.php";
    ?>
</head>

<body>
    <?php require "header.php"; ?>

    <div class="basicContent">
        <h1>Admin - All Products</h1>

        <p><a href=".." class="button">
            Back to Admin Overview
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
</body>

</html>
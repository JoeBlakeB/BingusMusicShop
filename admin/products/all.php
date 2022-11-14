<?php

/**
 * List all products
 * 
 * @author Joe Baker
 * @copyright Copyright (c) 2022 JoeBlakeB, all rights reserved.
 */

$rootPath = "../../";
set_include_path("{$rootPath}include");
session_start();

// Check if the user is an admin.
if (!isset($_SESSION["account"]) || !$_SESSION["account"]["isAdmin"]) {
    header("Location: {$rootPath}");
    die();
}

include "utils.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>All Products</title>
    <meta name="description" content="Bingus Music Shop Products Admin">
    <meta name="keywords" content="Bingus Music Shop, Products, Admin">
    <?php require "head.php"; ?>
</head>

<body>
    <?php require "header.php"; ?>

    <div class="basicContent">
        <h1>View All Products</h1>

        <a href="new.php" class="button"><p>New Product</p></a>

        <?php
        try {
            $dbh = sqlConnect();
            $stmt = $dbh->prepare("SELECT products.*, COUNT(imageID) as imageCount
                FROM products LEFT JOIN images ON products.productID = images.productID
                GROUP BY products.productID;");
            $stmt->execute();
            $products = $stmt->fetchAll();

            if ($products) {
            ?>
                <table>
                    <tr class="headerRow">
                        <th>ID</th>
                        <th>Product Name</th>
                        <th>Images</th>
                        <th>Price</th>
                        <th>Stock</th>
                    </tr>
                    <?php
                    foreach ($products as $product) {
                    ?>
                        <tr onclick="window.location.href = 'edit.php?id=<?=$product[0];?>';">
                            <td><?=$product[0] ?></td>
                            <td class="columnBig"><?=$product["name"];?></td>
                            <td><?=$product["imageCount"];?></td>
                            <td>£<?=$product["price"];?></td>
                            <td><?=$product["stock"];?></td>
                        </tr>
                    <?php
                    }
                    ?>
                </table>
                <?php
                $dbh = null;
            }
        } catch (PDOException $e) {
            echo "Error getting list of products: " . $e->getMessage();
        }

        ?>
    </div>
</body>

</html>
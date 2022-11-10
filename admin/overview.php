<?php
/**
 * Links to all admin pages: products, users, and orders.
 * 
 * @author Joe Baker
 * @copyright Copyright (c) 2022 JoeBlakeB, all rights reserved.
 */

$rootPath = "../";
set_include_path("{$rootPath}include");
session_start();

// Check if the user is an admin.
if (!isset($_SESSION["account"]) || !$_SESSION["account"]["isAdmin"]) {
    header("Location: {$rootPath}products/orders.php");
    die();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Admin</title>
    <meta name="description" content="Bingus Music Shop Admin">
    <meta name="keywords" content="Bingus Music Shop, Admin">
    <?php require "head.php"; ?>
</head>

<body>
    <?php require "header.php"; ?>

    <h1>Bingus Music Shop Admin</h1>

    // TODO, make this look good

    <h2>Products</h2>
    <!-- <a href="products.php">Products</a>
    <a href="products/add.php">Add Product</a> -->
    NOT IMPLEMENTED

    <h2>Users</h2>
    <a href="users.php">Users</a>

    <h2>Orders</h2>
    NOT IMPLEMENTED
</body>

</html>
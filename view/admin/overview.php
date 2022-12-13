<!DOCTYPE html>
<html lang="en">

<head>
    <?php
    $title = "Admin";
    require_once("include/head.php");
    ?>
</head>

<body>
    <?php require_once("include/header.php"); ?>

    <div class="content">
        <h1>Admin - Overview</h1>

        <h2>Products</h2>
        <p><a class="button" href="<?= $this->basePath ?>/admin/products">View all Products</a></p>
        <p><a class="button" href="<?= $this->basePath ?>/admin/products/new">Add a New Product</a></p>

        <h2>Users</h2>
        <p><a class="button" href="<?= $this->basePath ?>/admin/users">Users</a></p>

        <h2>Orders</h2>
        NOT IMPLEMENTED
    </div>
    <?php require_once("include/footer.php"); ?>
</body>

</html>
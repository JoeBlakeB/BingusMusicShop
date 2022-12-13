<!DOCTYPE html>
<html lang="en">

<head>
    <?php
    require_once("include/head.php");
    ?>
</head>

<body>
    <?php require_once("include/header.php"); ?>
    <div class="content orderView">
        <h1>Admin - Order #<?= $order["orderID"]; ?></h1>

        <p><a href="<?= $this->basePath ?>/admin/" class="button">
            Back to Admin Overview
        </a></p>
        <p><a href="<?= $this->basePath ?>/admin/orders" class="button">
            Back to All Orders
        </a></p>

        <?php require_once("include/orderView.php"); ?>
    </div>
    <?php require_once("include/footer.php"); ?>
</body>

</html>
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
        <h1>Order #<?= $order["orderID"]; ?></h1>
        <?php if (isset($_GET["success"])) { ?>
            <h3 class="success">You order has been placed successfully.</h3>
        <?php } ?>
        <p><a class="button" href=".">Back to Account</a></p>
        <p><a class="button" href="orders">Back to All Orders</a></p>
        
        <?php require_once("include/orderView.php"); ?>
    </div>
    <?php require_once("include/footer.php"); ?>
</body>

</html>
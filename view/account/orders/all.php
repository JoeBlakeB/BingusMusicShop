<!DOCTYPE html>
<html lang="en">

<head>
    <?php
    require_once("include/head.php");
    ?>
</head>

<body>
    <?php require_once("include/header.php"); ?>
    <div class="content">
        <h1>Your Orders</h1>
        <p><a class="button" href=".">Back to Account</a></p>
        
        <?php
        if (empty($orders)) {
            echo "<p>You curently do not have any orders.</p>";
        }
        else {
            require_once("include/ordersList.php"); 
        } ?>
    </div>
    <?php require_once("include/footer.php"); ?>
</body>

</html>
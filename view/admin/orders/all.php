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
        <h1>Admin - All Orders</h1>

        <p><a href="<?= $this->basePath ?>/admin/" class="button">
            Back to Admin Overview
        </a></p>

        <?php require_once("include/ordersList.php"); ?>
    </div>
    <?php require_once("include/footer.php"); ?>
</body>

</html>
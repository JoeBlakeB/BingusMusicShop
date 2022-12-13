<!DOCTYPE html>
<html lang="en">

<head>
    <?php
    require_once("include/head.php");
    require_once("view/include/productList.php");
    ?>
</head>

<body>
    <?php require_once("include/header.php"); ?>
    <div class="content homepage">
        <?php if (isset($message)) { ?>
            <p><?php echo $message; ?></p>
        <?php } else { ?>
            <h2>Featured Products</h2>
            <div class="productList">
                <?php productList($productsFeatured, $this->basePath); ?>
            </div>
            <h2>Newest Products</h2>
            <div class="productList">
                <?php productList($productsNew, $this->basePath); ?>
            </div>
        <?php } ?>
    </div>
    <?php require_once("include/footer.php"); ?>
</body>

</html>
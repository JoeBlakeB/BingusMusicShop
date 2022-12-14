<!DOCTYPE html>
<html lang="en">

<head>
    <?php
    require_once("include/head.php");
    require_once("include/productList.php");
    ?>
</head>

<body>
    <?php require_once("include/header.php"); ?>
    <div class="content homepage">
        <?php if (isset($message)) { ?>
            <p><?php echo $message; ?></p>
        <?php } else { ?>
            <?php if (!isset($_SESSION["account"])) { ?>
                <p>
                    Welcome to Bingus Music Shop! We sell a wide range of musical instruments, accessories, and music sheets. Type what you're looking for into the search bar above and press enter to find what you're looking for and click sign in to create an account to be able to purchase products.
                </p>
            <?php } ?>
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
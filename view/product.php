<!DOCTYPE html>
<html lang="en">

<head>
    <?php
    $title = $product->getName();
    $images = $product->getImages();
    require "head.php";
    ?>
</head>

<body>
    <?php require "header.php"; ?>
    <div class="content viewProduct <?= empty($images) ? "noImages" : "" ?>">
        <h1><?= $product->getName() ?></h1>

        <?php
        if (count($images) > 0) { ?>
        <div class="imageViewContainer">
            <div id="imageView">
                <img id="selectedImage" src="<?= $images[0]["url"]; ?>" alt="<?= $product->getName(); ?>">
            </div>
            <?php if (count($images) > 1) { ?>
            <div class="imageList">
                <?php for ($i = 0; $i < count($images); $i++) { ?>
                    <div class="imageListItem">
                        <img class="<?= $i == 0 ? "selected" : "" ?>" src="<?= $images[$i]["url"]; ?>" alt="<?= $product->getName(); ?>">
                    </div>
                <?php } ?>
            </div>
            <script src="/static/scripts/imageViewer.js"></script>
            <?php } ?>
        </div>
        <?php } ?>
        
        <h3>Price: <?= $product->getPriceStr() ?></h3>
        <p><?= $product->getDescription() ?></p>
    </div>
</body>

</html>
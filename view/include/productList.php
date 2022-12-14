<?php

/**
 * Show a list of products
 * 
 * @param array $products The products to show
 * @param string $basePath The base path of the site
 */
function productList($products, $basePath) {
    foreach ($products as $product) { ?>
        <div class="product">
            <a href="<?= $basePath . "/product/" . $product->getId(); ?>">
                <?php if (count($product->getImages()) > 0) { ?>
                    <img src="<?= $product->getImages()[0]["url"]; ?>" alt="<?php echo $product->getName(); ?>">
                <?php } else { ?>
                    <img src="/static/images/ProductPlaceholder.png" alt="<?php echo $product->getName(); ?>">
                <?php } ?>
                <h3><?= $product->getName(); ?></h3>
                <p><?= $product->getPriceStr(); ?></p>
            </a>
        </div>
    <?php }
}
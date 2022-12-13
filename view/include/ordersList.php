<div class="ordersList">
    <?php foreach ($orders as &$order) { ?> <div>
        <p class="orderListItemHeader">
            <a href="orders?orderID=<?= $order["orderID"] ?>" class="button">View Order</a>
        </p>
        <h2 class="orderListItemHeader">Order #<?= $order["orderID"] ?></h2>
        
        <p>
            Order Date: 
            <?= date_format(date_create($order["orderDate"]), "d/m/Y H:i:s"); ?>
        </p>
        <p>
            Total Price: £<?= number_format($order["totalPrice"], 2); ?>
        </p>

        <?php if ($viewPath == "admin") { ?>
            <p>
                Account: 
                <?php if (empty($order["account"]["accountID"])) { ?>
                    [DELETED USER]
                <?php } else { ?>
                <a href="<?= $this->basePath ?>/admin/users?accountID=<?= $order["account"]["accountID"] ?>">
                    <?= $order["account"]["fullName"] ?> - <?= $order["account"]["email"] ?>
                </a>
                <?php } ?>
            </p>
        <?php } ?>

        <p>
            Address: 
            <?php if (empty($order["address"]->getID())) { ?>
                [DELETED ADDRESS]
            <?php } else { ?>
                <?= $order["address"]->getFullName(); ?>,
                <?= $order["address"]->getAddress1(); ?>,
                <?= $order["address"]->getPostcode(); ?>
            <?php } ?>
        </p>
        
        <h3>Items</h3>
        <ul>
            <?php foreach ($order["products"] as &$product) { ?>
                <li>
                    <a href="<?= $this->basePath ?>/product/<?= $product->getID() ?>">
                        <?= $product->getName() ?>
                    </a>
                </li>
            <?php } ?>
        </ul>
    </div> <?php } ?>
</div>
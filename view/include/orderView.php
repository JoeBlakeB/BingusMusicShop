<?php if (empty($order["account"]["accountID"])) { ?>
    <h2 class="warning">
        The users account has been deleted;
        the account, address, and payment information is missing.
    </h2>
<?php } else if (empty($order["address"]->getID())
        || empty($order["card"]->getID())) { ?>
    <h2 class="warning">
        The user has deleted some of their information; the 
        <?=
            (empty($order["address"]->getID()) ? "address" : "") .
            ((empty($order["address"]->getID()) && empty($order["card"]->getID()) ? " and " : "")) .
            (empty($order["card"]->getID()) ? "payment" : "");
        ?> information is missing.
    </h2>
<?php } ?>

<h3>Total Price:</h3>
<p>
    £<?= number_format($order["totalPrice"], 2); ?>
</p>

<?php if ($viewPath == "admin" && 
        !empty($order["account"]["accountID"])) { ?>
    <h3>Account:</h3>
    <p>
        <a href="<?= $this->basePath ?>/admin/users?accountID=<?= $order["account"]["accountID"] ?>">
            <?= $order["account"]["fullName"] ?> - <?= $order["account"]["email"] ?>
        </a>
    </p>
<?php } ?>

<?php if (!empty($order["address"]->getID())) { ?>
    <h3>Address:</h3>
    <p> <?= $order["address"]; ?> </p>
<?php } ?>

<?php if (!empty($order["card"]->getID())) { ?>
    <h3>Card:</h3>
    <p> <?= $order["card"]->getFullName() ?> - <?= $order["card"]->getCardNumberHidden(); ?> </p>
<?php } ?>

<h3>Order Date:</h3>
<p>
    <?= date_format(date_create($order["orderDate"]), "d/m/Y H:i:s"); ?>
</p>

<h3>Items:</h3>
<table>
    <thead>
        <tr>
            <th>Product</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>SubTotal</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($order["products"] as &$product) { ?>
            <tr>
                <td class="big">
                    <a href="<?= $this->basePath ?>/product/<?= $product->getID() ?>">
                        <?= $product->getName() ?>
                    </a>
                </td>
                <td> <?= $product->getPriceStr(); ?> </td>
                <td> <?= $product->getQuantity(); ?> </td>
                <td> £<?= number_format($product->getPriceInt() * $product->getQuantity(), 2); ?> </td>
            </tr>
        <?php } ?>
    </tbody>
</table>
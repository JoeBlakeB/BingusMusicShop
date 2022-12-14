<!DOCTYPE html>
<html lang="en">

<head>
    <?php
    $title = "Purchase " . $product->getName();
    require_once("include/head.php");

    if (!isset($valid)) {
        $valid = [
            "quantity" => true,
            "address" => true,
            "card" => true
        ];
    }
    ?>
</head>

<body>
    <?php require_once("include/header.php"); ?>
    <form method="post" class="editForm basicResponsiveForm purchaseForm">
        <h1>Purchase <?= $product->getName(); ?></h1>
        <h3>Price per item: <span id="subtotalDisplay"><?= $product->getPriceStr(); ?></span></h3>

        <div class="inputContainer <?= $valid["quantity"] ? "" : "inputError"; ?>" id="quantityContainer">
            <label for="quantity">Quantity:</label>
            <input type="number" name="quantity" id="quantity" value="1" min="1" max="<?= $product->getStock(); ?>" required>
        </div>

        <h3>Total: <span id="totalDisplay"><?= $product->getPriceStr(); ?></span></h3>
        
        <div class="inputContainer <?= $valid["address"] ? "" : "inputError"; ?>" id="addressContainer">
            <label for="address">Address:</label>
            <select name="address" id="address" required>
                <option disabled selected value>Select an Address</option>
                <?php foreach ($addresses as $address) { ?>
                    <option value="address-<?= $address->getId(); ?>" 
                        <?= isset($_POST["address"]) && 
                            "address-".$address->getID() == $_POST["address"] ?
                            "selected" : ""; ?>>
                        <?= $address->toString(", "); ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="inputContainer <?= $valid["card"] ? "" : "inputError"; ?>" id="cardContainer">
            <label for="card">Payment Method:</label>
            <select name="card" id="card" required>
                <option disabled selected value>Select a Payment Method</option>
                <?php foreach ($cards as $card) { ?>
                    <option value="card-<?= $card->getId(); ?>"
                        <?= isset($_POST["card"]) && 
                            "card-".$card->getID() == $_POST["card"] ?
                            "selected" : ""; ?>>
                        <?= $card->getCardNumberHidden(); ?> - <?= $card->getExpiry(); ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <input type="submit" value="Confirm Purchase">
    </form>
    <script src="/static/scripts/purchaseTotalUpdater.js"></script>
    <?php require_once("include/footer.php"); ?>
</body>

</html>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php
    $title = "Payment Card Management";
    require "include/head.php";


    $fullName = isset($_POST["name"]) ? $_POST["name"] : (isset($card) ? $card->getFullName() : $_SESSION["account"]["fullName"]);
    $cardNumber = isset($_POST["cardNumber"]) ? $_POST["cardNumber"] : (isset($card) ? $card->getCardNumber() : "");
    $expiryMonth = isset($_POST["expiryMonth"]) ? $_POST["expiryMonth"] : (isset($card) ? $card->getExpiryMonth() : "");
    $expiryYear = isset($_POST["expiryYear"]) ? $_POST["expiryYear"] : (isset($card) ? $card->getExpiryYear() : "");
    $securityCode = isset($_POST["securityCode"]) ? $_POST["securityCode"] : (isset($card) ? $card->getSecurityCode() : "");
    ?>
</head>

<body>
    <?php require "include/header.php"; ?>
    <div class="content multipleContents">
        <h1><?= isset($card) ? "Edit Credit or Debit Card" : "New Credit or Debit Card"; ?></h1>

        <p><a class="button" href="payments">Back to All Payment Methods</a></p>

        <p class="warning">Please note, this is not a real shop, please NO NOT enter real card details.</p>
    </div>

    <form action="payments?<?= isset($card) ? "edit=" . $card->getID() : "new"; ?>" method="post" class="editForm basicResponsiveForm">
        <div class="inputContainer <?= $valid["name"] ? "" : "inputError"; ?>" id="nameContainer">
            <label for="name">Full Name:</label>
            <input type="text" name="name" id="name" value="<?= $fullName ?>" maxlength="256" required>
        </div>
        <div class="inputContainer <?= $valid["cardNumber"] ? "" : "inputError"; ?>" id="cardNumberContainer">
            <label for="cardNumber">Card Number:</label>
            <input type="text" name="cardNumber" id="cardNumber" value="<?= $cardNumber; ?>" maxlength="16" required>
        </div>
        <div class="grid threeColumns">
            <div class="inputContainer <?= $valid["expiryMonth"] ? "" : "inputError"; ?>" id="expiryMonthContainer">
                <label for="expiryMonth">Expiry Month:</label>
                <select name="expiryMonth" id="expiryMonth" required>
                    <option value="" disabled selected>Select a month</option>
                    <?php
                    $months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
                    for ($i = 0; $i < 12; $i++) { ?>
                        <option value="<?= $i + 1; ?>" <?= $expiryMonth == $i + 1 ? "selected" : ""; ?>><?= $months[$i]; ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="inputContainer <?= $valid["expiryYear"] ? "" : "inputError"; ?>" id="expiryYearContainer">
                <label for="expiryYear">Expiry Year:</label>
                <input type="number" name="expiryYear" id="expiryYear" value="<?= $expiryYear; ?>" min="<?= date("Y"); ?>" max="<?= date("Y") + 15; ?>" required>
            </div>
            <div class="inputContainer <?= $valid["securityCode"] ? "" : "inputError"; ?>" id="securityCodeContainer">
                <label for="securityCode">Security Code:</label>
                <input type="number" name="securityCode" id="securityCode" value="<?= $securityCode; ?>" min="0" max="999" required>
            </div>
        </div>
        <input type="submit" value="Save">
    </form>
    <?php require "include/footer.php"; ?>
</body>

</html>
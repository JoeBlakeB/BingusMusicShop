<!DOCTYPE html>
<html lang="en">

<head>
    <?php
    $title = "Address Management";
    require "include/head.php";

    $fullName = isset($_POST["name"]) ? $_POST["name"] : (
        isset($address) ? $address->getFullName() : $_SESSION["account"]["fullName"]);
    $address1 = isset($_POST["address1"]) ? $_POST["address1"] : (
        isset($address) ? $address->getAddress1() : "");
    $address2 = isset($_POST["address2"]) ? $_POST["address2"] : (
        isset($address) ? $address->getAddress2() : "");
    $city = isset($_POST["city"]) ? $_POST["city"] : (
        isset($address) ? $address->getCity() : "");
    $county = isset($_POST["county"]) ? $_POST["county"] : (
        isset($address) ? $address->getCounty() : "");
    $postcode = isset($_POST["postcode"]) ? $_POST["postcode"] : (
        isset($address) ? $address->getPostcode() : "");
    $country = isset($_POST["country"]) ? $_POST["country"] : (
        isset($address) ? $address->getCountryCode() : "GB");
    ?>
</head>

<body>
    <?php require "include/header.php"; ?>
    <div class="content">
        <h1><?= isset($address) ? "Edit Address" : "New Address"; ?></h1>

        <p><a class="button" href="addresses">Back to All Addresses</a></p>
    </div>

    <form action="addresses?<?= isset($address) ? "edit=" . $address->getID() : "new"; ?>" method="post" class="editForm basicResponsiveForm">
        <div class="inputContainer <?= $valid["name"] ? "" : "inputError"; ?>" id="nameContainer">
            <label for="name">Full Name:</label>
            <input type="text" name="name" id="name" value="<?= $fullName ?>" maxlength="256" required>
        </div>
        <div class="inputContainer <?= $valid["address1"] ? "" : "inputError"; ?>" id="address1Container">
            <label for="address1">Address Line 1:</label>
            <input type="text" name="address1" id="address1" value="<?= $address1; ?>" maxlength="128" required>
        </div>
        <div class="inputContainer <?= $valid["address2"] ? "" : "inputError"; ?>" id="address2Container">
            <label for="address2">Address Line 2:</label>
            <input type="text" name="address2" id="address2" value="<?= $address2; ?>" maxlength="128">
        </div>
        <div class="inputContainer <?= $valid["city"] ? "" : "inputError"; ?>" id="cityContainer">
            <label for="city">City:</label>
            <input type="text" name="city" id="city" value="<?= $city; ?>" maxlength="64" required>
        </div>
        <div class="inputContainer <?= $valid["county"] ? "" : "inputError"; ?>" id="countyContainer">
            <label for="county">County:</label>
            <input type="text" name="county" id="county" value="<?= $county; ?>" maxlength="64">
        </div>
        <div class="inputContainer <?= $valid["postcode"] ? "" : "inputError"; ?>" id="postcodeContainer">
            <label for="postcode">Postcode:</label>
            <input type="text" name="postcode" id="postcode" value="<?= $postcode; ?>" maxlength="8">
        </div>
        <div class="inputContainer <?= $valid["country"] ? "" : "inputError"; ?>" id="countryContainer">
            <label for="country">Country:</label>
            <select name="country" id="country" required>
                <?php foreach ($accountModel->countriesList as $countryCode
                    => $countryName) { ?>
                    <option value="<?= $countryCode; ?>" <?= $country == $countryCode ? "selected" : ""; ?>><?= $countryName; ?></option>
                <?php } ?>
            </select>
        </div>
        <input type="submit" value="Save">
    </form>
    <script src="/static/scripts/postcodeValidation.js"></script>
</body>

</html>
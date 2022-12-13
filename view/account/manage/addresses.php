<!DOCTYPE html>
<html lang="en">

<head>
    <?php
    $title = "Address Management";
    require_once("include/head.php");
    ?>
</head>

<body>
    <?php require_once("include/header.php"); ?>
    <div class="content">
        <h1>Your Addresses</h1>

        <p><a class="button" href=".">Back to Account</a></p>
        <p><a class="button" href="addresses?new">Add a New Address</a></p>

        <?php if (count($addresses) == 0) { ?>
            <p>You have no addresses saved.</p>
        <?php } else { ?>
            <h2>Addresses</h2>
            <?php foreach ($addresses as &$address) { ?>
                <div class="addressesListItem">
                    <?= $address ?><br>
                    <p><a class="button" href="addresses?edit=<?= $address->getID(); ?>">Edit</a></p>
                    <p><a class="button" href="addresses?delete=<?= $address->getID(); ?>">Delete</a></p>
                </div>
        <?php }
        } ?>
    </div>
    <?php require_once("include/footer.php"); ?>
</body>

</html>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php
    $title = "Payment Method Management";
    require "include/head.php";
    ?>
</head>

<body>
    <?php require "include/header.php"; ?>
    <div class="content">
        <h1>Your Payment Methods</h1>

        <p><a class="button" href=".">Back to Account</a></p>
        <p><a class="button" href="payments?new">Add a New Credit or Debit Card</a></p>

        <?php if (count($cards) == 0) { ?>
            <p>You have no payment methods saved.</p>
        <?php } else { ?>
            <h2>Payment Methods</h2>
            <?php foreach ($cards as &$card) { ?>
                <div class="paymentMethodsListItem <?= $card->isExpired() ? "expired" : "" ?>">
                    <?= $card ?><br>
                    <p><a class="button" href="payments?edit=<?= $card->getID(); ?>">Edit</a></p>
                    <p><a class="button" href="payments?delete=<?= $card->getID(); ?>">Delete</a></p>
                </div>
        <?php }
        } ?>
    </div>
</body>

</html>
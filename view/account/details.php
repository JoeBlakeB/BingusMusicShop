<!DOCTYPE html>
<html lang="en">

<head>
    <?php
    $title = "Account Details";
    require "include/head.php";
    ?>
</head>

<body>
    <?php require "include/header.php"; ?>
    <div class="content">
        <h1>Your Account</h1>
        <p>Hello <?php echo $_SESSION["account"]["fullName"]; ?>, you are currently signed in.</p>
        <p><a class="button" href="<?= $this->basePath ?>/account/addresses">Manage Addresses</a></p>
        <p><a class="button" href="<?= $this->basePath ?>/account/payments">Manage Payment Methods</a></p>
        <p><a class="button" href="<?= $this->basePath ?>/account/security">Manage Account Security</a></p>
        <p><a class="button disabled" href="#">View Orders</a></p>
        <p><a class="button" href="<?= $this->basePath ?>/account/signout">Sign out</a></p>
    </div>
    <?php require "include/footer.php"; ?>
</body>

</html>
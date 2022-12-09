<!DOCTYPE html>
<html lang="en">

<head>
    <?php
    $title = "Account Details";
    require "head.php";
    ?>
</head>

<body>
    <?php require "header.php"; ?>
    <div class="content">
        <h1>Your Account</h1>
        <p>Hello <?php echo $_SESSION["account"]["fullName"]; ?>, you are currently signed in.</p>
        <p><a class="button" href="<?= $this->basePath ?>/account/addresses">Manage Addresses</a></p>
        <p><a class="button" href="<?= $this->basePath ?>/account/payments">Manage Payment Methods</a></p>
        <p><a class="button disabled" href="#">View Orders</a></p>
        <p><a class="button disabled" href="#">Change Password</a></p>
        <p><a class="button disabled" href="#">Enable 2fa</a></p>
        <p><a class="button" href="<?= $this->basePath ?>/account/signout">Sign out</a></p>
    </div>
</body>

</html>
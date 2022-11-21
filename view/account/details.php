<!DOCTYPE html>
<html lang="en">

<head>
    <?php require "head.php"; ?>
</head>

<body>
    <?php require "header.php"; ?>
    <h1>Account Details</h1>
    <p>Currently just a debug page</p>
    <?php
    var_dump($_SESSION);
    ?>
    <br>
    You are signed in as <?php echo $_SESSION["account"]["fullName"]; ?>.
    <br>
    <a href="/BingusMusicShop.php/account/signout">Sign out</a>
</body>

</html>
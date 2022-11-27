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
    <div class="basicContent">
        <h1>Account Details</h1>
        You are signed in as <?php echo $_SESSION["account"]["fullName"]; ?>.
        <br>
        <a href="<?= $this->basePath ?>/account/signout">Sign out</a>
    </div>
</body>

</html>
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
        <h1>Account Details</h1>
        You are signed in as <?php echo $_SESSION["account"]["fullName"]; ?>.
        <p><a class="button" href="<?= $this->basePath ?>/account/signout">Sign out</a></p>
    </div>
</body>

</html>
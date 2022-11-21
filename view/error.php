<!DOCTYPE html>
<html lang="en">

<head>
    <?php
    $pageTitle = $errorTitle;
    require "head.php";
    ?>
</head>

<body>
    <?php require "header.php"; ?>
    <h1>Error <?=$errorCode?>: <?=$errorTitle?></h1>
    <p><?=$errorMessage?></p>
</body>

</html>
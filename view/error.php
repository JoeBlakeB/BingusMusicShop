<!DOCTYPE html>
<html lang="en">

<head>
    <?php
    $title = $errorTitle;
    require "head.php";
    ?>
</head>

<body>
    <?php require "header.php"; ?>
    <div class="centeredContent">
        <h1>Error <?=$errorCode?>: <?=$errorTitle?></h1>
        <p><?=$errorMessage?></p>
    </div>
</body>

</html>
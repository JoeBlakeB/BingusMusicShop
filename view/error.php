<!DOCTYPE html>
<html lang="en">

<head>
    <?php
    $title = $errorTitle;
    require_once("include/head.php");
    ?>
</head>

<body>
    <?php require_once("include/header.php"); ?>
    <div class="centeredContent">
        <h1>Error <?=$errorCode?>: <?=$errorTitle?></h1>
        <p><?=$errorMessage?></p>
        <?php if (isset($errorLinkText) || $errorCode == 404) { ?>
        <p><a class="button" href="<?= isset($errorLinkHref) ? $errorLinkHref : $this->basePath; ?>">
            <?= isset($errorLinkText) ? $errorLinkText : (
                $errorCode == 404 ? "Go back to the home page" : ""); ?>
        </a></p>
        <?php } ?>
    </div>
    <?php require_once("include/footer.php"); ?>
</body>

</html>
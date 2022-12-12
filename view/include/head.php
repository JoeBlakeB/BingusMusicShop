<?php
if (!isset($description)) {
    $description = "The best place to buy instruments and music sheets online.";
}
?>
<title><?php
if (isset($title)) {
    echo $title . " - ";
} ?>Bingus Music Shop</title>
<meta property="og:title" content="<?php
if (isset($title)) {
    echo $title . " - ";
} ?>Bingus Music Shop" />
<meta property="og:image" content="<?= isset($mediaImage) ? $mediaImage : "/static/images/Logo.png"; ?>" />
<meta property="og:description" content="<?= $description; ?>" />
<meta charset="UTF-8">
<meta name="author" content="Joe Baker">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="icon" type="image/x-icon" href="/static/images/favicon.ico">
<link rel="stylesheet" href="/static/fontawesome/fontawesome.6.2.0.css">
<link rel="stylesheet" href="/static/styles/main.css">
<meta name="description" content="<?= $description; ?>">
<meta name="keywords" content="Bingus Music Shop">
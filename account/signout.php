<?php

/**
 * Destroy the users session and tell them they are now 
 * logged out. Will give them a button to the home page 
 * and to sign back in.
 * 
 * @author Joe Baker
 * @copyright Copyright (c) 2022 JoeBlakeB, all rights reserved.
 */

$rootPath = "../";
set_include_path("{$rootPath}include");
session_start();

$alreadySignedOut = !isset($_SESSION["account"]);

session_destroy();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Bingus Music Shop</title>
    <meta name="description" content="Bingus Music Shop Sign In">
    <meta name="keywords" content="Bingus Music Shop, Log In, Sign In">
    <?php require "head.php"; ?>
    <link rel="stylesheet" href="<?php echo $rootPath; ?>static/styles/signin.css">
</head>

<body>
    <div id="signinHeader">
        <a href="..">
            <img src="<?php echo $rootPath; ?>static/images/Logo.png" alt="Logo">
            <h1>Bingus Music Shop</h1>
        </a>
    </div>
    <div class="signInContent" id="signinBox">
        <div class="form">
            <h1>Sign Out</h1>
            <h2>You are 
                <?php echo $alreadySignedOut ? "already" : "now" ?> 
                signed out.
            </h2>

            <div class="signOutButton"><a href="../">
                    <p>Go to the homepage</p>
                </a>
            </div>

            <div class="signOutButton"><a href="signin.php">
                    <p><?php
                        echo $alreadySignedOut ? "Sign In" :
                        "Didnt mean to sign out? <br>Sign back In";
                    ?></p>
                </a>
            </div>
        </div>
    </div>
</body>

</html>
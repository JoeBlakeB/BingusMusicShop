<!DOCTYPE html>
<html lang="en">

<head>
    <?php
    $title = "Sign Out";
    require_once("include/head.php");
    ?>
    <link rel="stylesheet" href="/static/styles/signin.css">
</head>

<body>
    <div id="signinHeader">
        <a href="..">
            <img src="/static/images/Logo.png" alt="Logo">
            <h1>Bingus Music Shop</h1>
        </a>
    </div>
    <div class="signInContent" id="signinBox">
        <div class="form">
            <h1>Sign Out</h1>
            <h2>
                You are 
                <?= $alreadySignedOut ? "already" : "now" ?> 
                signed out.
            </h2>

            <div class="signOutButton">
                <a href="..">
                    <p>Go to the homepage</p>
                </a>
            </div>

            <div class="signOutButton">
                <a href="signin">
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
<?php
// Copyright (c) 2022 JoeBlakeB, all rights reserved.

$rootPath = "../";
set_include_path("{$rootPath}include");
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
        <form method="post">
            <h1>Sign In</h1>

            <div class="inputContainer" id="emailContainer">
                <label for="email">Email Address:</label>
                <input maxlength="256" type="text" name="email" id="email" value="<?php
                    if (isset($_POST["email"])) {
                        echo $_POST["email"];
                    }?>" required>
                <p></p>
            </div>

            <div class="inputContainer">
                <label for="password">Password:</label>
                <input maxlength="256" type="password" name="password" id="password" required>
                <p></p>
            </div>

            <input type="checkbox" id="remember" name="remember" value="true" <?php
                if (isset($_POST["remember"])) {
                    echo "checked";
                }?>>
            <label for="remember"> Keep me signed in.</label>

            <input type="submit" value="Sign In" formType="signin" id="submitButton">
        </form>
    </div>
    <div class="signInContent" id="switchSignInButton"><a href="register.php">
        <p>New customer? <br>Create an account</p></a>
    </div>

    <script src="<?php echo $rootPath; ?>static/scripts/signinValidation.js"></script>
</body>

</html>
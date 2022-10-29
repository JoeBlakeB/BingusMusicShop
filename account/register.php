<?php
// Copyright (c) 2022 JoeBlakeB, all rights reserved.

$rootPath = "../";
set_include_path("{$rootPath}include");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Bingus Music Shop</title>
    <meta name="description" content="Bingus Music Shop Register">
    <meta name="keywords" content="Bingus Music Shop, Register">
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
            <h1>Register</h1>

            <?php
            // Tell user if there was invalid data.
            // Should not be needed with the javascript validation.
            // TODO
            if (isset($invalid)) {
                echo "<p style=\"color:red\">Invalid input</p>";
            }
            ?>

            <div class="inputContainer" id="nameContainer">
                <label for="name">Your Full Name:</label>
                <input maxlength="256" type="text" name="name" id="name" value="<?php
                    if (isset($_POST["name"])) {
                        echo $_POST["name"];
                    }?>" required>
                <p></p>
            </div>
            
            <div class="inputContainer" id="emailContainer">
                <label for="email">Email Address:</label>
                <input maxlength="256" type="text" name="email" id="email" value="<?php
                    if (isset($_POST["email"])) {
                        echo $_POST["email"];
                    }?>" required>
                <p></p>
            </div>

            <div class="inputContainer" id="passwordContainer">
                <label for="password">Password:</label>
                <input maxlength="256" type="password" name="password" id="password" value="<?php
                    if (isset($_POST["password"])) {
                        echo $_POST["password"];
                    }?>" required>
                <p></p>
            </div>
            
            <div class="inputContainer" id="passwordConfContainer">
                <label for="passwordConf">Confirm Password:</label>
                <input maxlength="256" type="password" name="passwordConf" id="passwordConf" value="<?php
                    if (isset($_POST["passwordConf"])) {
                        echo $_POST["passwordConf"];
                    }?>" required>
                <p></p>
            </div>

            <input type="submit" value="Register" formType="register" id="submitButton">
        </form>
    </div>
    <div class="signInContent" id="switchSignInButton"><a href="signin.php">
        <p>Already have an account? <br>Sign In</p>
    </a></div>

    <script src="<?php echo $rootPath; ?>static/scripts/signinValidation.js"></script>
</body>

</html>
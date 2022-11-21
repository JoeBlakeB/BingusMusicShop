<!DOCTYPE html>
<html lang="en">

<head>
    <?php
    $title = "Register";
    require "head.php";
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
        <form method="post" class="form">
            <h1>Sign In</h1>

            <?php if ($error) { ?>
                <div class="error">
                    <p><?=$error;?></p>
                </div>
            <?php } ?>

            <div class="inputContainer <?= $valid["name"] ? "" : "error" ?>" id="nameContainer">
                <label for="name">Your Full Name:</label>
                <input maxlength="256" type="text" name="name" id="name" value="<?php
                    if (isset($_POST["name"])) {
                        echo htmlspecialchars($_POST["name"]);
                    }?>" required>
                <p><?php
                    if (!$valid["name"]) {
                        echo "Please enter your full name.";
                    }
                ?></p>
            </div>
            <div class="inputContainer <?= ($valid["email"] && !isset($account)) ? "" : "error" ?>" id="emailContainer">
                <label for="email">Email Address:</label>
                <input maxlength="256" type="text" name="email" id="email" value="<?php
                    if (isset($_POST["email"])) {
                        echo htmlspecialchars($_POST["email"]);
                    }?>" required>
                <p><?php
                    if (isset($account)) {
                        echo "This email address is already in use, please sign into your existing account instead.";
                    }
                    else if (!$valid["email"]) {
                        echo "Please enter a valid email address.";
                    }
                ?></p>
            </div>

            <div class="inputContainer <?= $valid["password"] ? "" : "error" ?>" id="passwordContainer">
                <label for="password">Password:</label>
                <input maxlength="256" type="password" name="password" id="password" value="<?php
                    if (isset($_POST["password"])) {
                        echo htmlspecialchars($_POST["password"]);
                    }?>" required>
                <p><?php
                    if (!$valid["password"]) {
                        echo "Passwords must be 8-256 characters and contain at least:<br>a lowercase letter, an uppercase letter, and a number.";
                    }
                ?></p>
            </div>
            
            <div class="inputContainer <?= $valid["passwordConf"] ? "" : "error" ?>" id="passwordConfContainer">
                <label for="passwordConf">Confirm Password:</label>
                <input maxlength="256" type="password" name="passwordConf" id="passwordConf" value="<?php
                    if (isset($_POST["passwordConf"])) {
                        echo htmlspecialchars($_POST["passwordConf"]);
                    }?>" required>
                <p><?php
                    if (!$valid["passwordConf"]) {
                        echo "Passwords do not match";
                    }
                ?></p>
            </div>

            <input type="submit" value="Register" formType="register" id="submitButton">
        </form>
    </div>
    
    <div class="signInContent" id="switchSignInButton">
        <a href="signin">
            <p>Already have an account? <br>Sign In</p>
        </a>
    </div>

    <script src="/static/scripts/signinValidation.js"></script>
</body>

</html>
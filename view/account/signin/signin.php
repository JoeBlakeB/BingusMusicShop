<!DOCTYPE html>
<html lang="en">

<head>
    <?php
    $title = "Sign In";
    require "include/head.php";
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

            <?php if (isset($error)) { ?>
                <div class="error">
                    <p><?=$error;?></p>
                </div>
            <?php } ?>

            <div class="inputContainer" id="emailContainer">
                <label for="email">Email Address:</label>
                <input maxlength="256" type="text" name="email" id="email" value="<?php
                    if (isset($_POST["email"])) {
                        echo htmlspecialchars($_POST["email"]);
                    } ?>" required>
                <p></p>
            </div>

            <div class="inputContainer">
                <label for="password">Password:</label>
                <input maxlength="256" type="password" name="password" id="password" required>
                <p></p>
            </div>

            <input type="submit" value="Sign In" formType="signin" id="submitButton">
        </form>
    </div>

    <div class="signInContent" id="switchSignInButton">
        <a href="register">
            <p>New customer? <br>Create an account</p>
        </a>
    </div>

    <script src="/static/scripts/signinValidation.js"></script>
</body>

</html>
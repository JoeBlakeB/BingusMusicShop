<!DOCTYPE html>
<html lang="en">

<head>
    <?php require "head.php"; ?>
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
            <h1><?php echo ($formType == "signin") ? "Two Factor Authentication" : "Verify Your Account"; ?></h1>
            
            <?php
                if (isset($errorMsg)) {
                    ?>
                    <div class="error"><p>
                        <?php echo $errorMsg; ?>
                    </p></div>
                    <?php
                }
                else if (isset($authorised)) {
                    ?>
                    <div class="error"><p>
                        Invalid or expired verification code. 
                    </p></div>
                    <?php
                }

                if ($alreadyHaveEmail) {
                    if ($formType == "register") {
                        ?><p>
                            A verification code has been sent to your email address, please check your spam folder.
                        </p><?php
                    }
                    else {
                        ?><p>
                            You must verify your account before you can sign in, please check your email for a verification code.
                        </p><?php
                    }
                }
                else {
            ?>
            
            <div class="inputContainer" id="emailContainer">
                <label for="email">Email Address:</label>
                <input maxlength="256" type="text" name="email" id="email" value="<?php
                    if (isset($_POST["email"])) {
                        echo htmlspecialchars($_POST["email"]);
                    }?>" required>
                <p></p>
            </div>

            <?php } ?>

            <div class="inputContainer" id="authContainer">
                <label for="auth">Verification Code:</label>
                <input maxlength="6" type="text" name="auth" id="auth" placeholder="6-digit code" value="<?php
                    if (isset($_POST["auth"])) {
                        echo htmlspecialchars($_POST["auth"]);
                    }?>" autocomplete="off" required>
                <p></p>
            </div>

            <input type="submit" value="Verify" formType="<?php echo (isset($_GET["action"])) ? "auth" : "authWithEmail" ?>" id="submitButton">
        </form>
    </div>

    <script src="/static/scripts/signinValidation.js"></script>
</body>

</html>
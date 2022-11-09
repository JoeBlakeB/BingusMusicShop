<?php
/**
 * Sign in to an existing account, will then redirect to account
 * index page or verification page if 2fa is enabled.
 * 
 * @author Joe Baker
 * @copyright Copyright (c) 2022 JoeBlakeB, all rights reserved.
 */

$rootPath = "../";
set_include_path("{$rootPath}include");
session_start();

// Redirect to account details if already signed in
if (isset($_SESSION["account"])) {
    header("Location: details.php");
    die();
}

$error = false;
if (!empty($_POST["email"]) && !empty($_POST["password"])) {
    $email = strtolower($_POST["email"]);
    $password = $_POST["password"];
    try {
        include "utils.php";

        // Check if email and password are correct
        $dbh = sqlConnect();
        $stmt = $dbh->prepare("SELECT * FROM accounts WHERE email = :email");
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        $account = $stmt->fetch();

        if ($account && password_verify($password, $account["passwordHash"])) {
            session_unset();

            // Check if account is verified
            $stmt = $dbh->prepare("SELECT * FROM unverifiedAccounts WHERE accountID = :accountID");
            $stmt->bindParam(":accountID", $account["accountID"]);
            $stmt->execute();
            $unverified = $stmt->fetch();
            if ($unverified) {
                // Redirect to verification page
                $_SESSION["email"] = $email;
                header("Location: verification.php?action=signinUnverified");
                die();
            }

            // If 2fa is enabled, redirect to 2fa page
            if ($account["2faEnabled"]) {
                $verificationCode = str_pad(rand(1, 999999), 6, 0, STR_PAD_LEFT);
                $subject = "Bingus Music Shop Two Factor Authentication";
                $message = "Your Two Factor Authentication code is: $verificationCode";
                mail($email, $subject, $message);

                $_SESSION["email"] = $email;
                $_SESSION["2faCode"] = $verificationCode;
                header("Location: verification.php");
                die();
            }

            // Sign in if verified and not using 2fa
            signin($account);
            die();
        }
        else {
            $error = "The email or password you entered is incorrect.";
        }
        $dbh = null;
    }
    catch (PDOException $e) {
        $error = "An error occurred while signing in, please try again later.";
    }
}

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
        <form method="post" class="form">
            <h1>Sign In</h1>

            <?php
            if ($error) {
                ?>
                <div class="error"><p>
                    <?php echo $error; ?>
                </p></div>
                <?php
            }
            ?>

            <div class="inputContainer" id="emailContainer">
                <label for="email">Email Address:</label>
                <input maxlength="256" type="text" name="email" id="email" value="<?php
                    if (isset($_POST["email"])) {
                        echo htmlspecialchars($_POST["email"]);
                    }?>" required>
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
    <div class="signInContent" id="switchSignInButton"><a href="register.php">
        <p>New customer? <br>Create an account</p></a>
    </div>

    <script src="<?php echo $rootPath; ?>static/scripts/signinValidation.js"></script>
</body>

</html>
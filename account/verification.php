<?php
// Copyright (c) 2022 JoeBlakeB, all rights reserved.

/*
 * Check verification codes sent via email, if the code is valid
 * the user will be signed in, and if its a new account the
 * account will be verified by deleting them from unverifiedAccounts.
 * If there are any expired codes, their accounts will be deleted.
 */

$rootPath = "../";
set_include_path("{$rootPath}include");
session_start();

// Redirect to account info if already signed in
if (isset($_SESSION["authorised"])) {
    header("Location: ../account");
    die();
}

// Get email from form or session
if (isset($_POST["email"])) {
    $email = $_POST["email"];
}
else if (isset($_SESSION["emailToVerify"]) && isset($_GET["action"])) {
    $email = $_SESSION["emailToVerify"];
}

// TODO check that this is for account verification and not 2fa
// Check code and delete expired accounts
if (isset($email) && isset($_POST["auth"])) {
    include "sql.php";
    try {
        $dbh = sqlConnect();

        // Delete expired accounts first
        $stmt = $dbh->prepare("DELETE accounts, unverifiedAccounts FROM accounts
            INNER JOIN unverifiedAccounts ON accounts.accountID = unverifiedAccounts.accountID
            WHERE expires < NOW();");
        $stmt->execute();

        // Check code is valid
        $email = strtolower($email);
        $stmt = $dbh->prepare("SELECT accounts.accountID FROM unverifiedAccounts
            INNER JOIN accounts ON unverifiedAccounts.accountID = accounts.accountID
            WHERE email = :email AND verificationCode = :verificationCode;");
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":verificationCode", $_POST["auth"]);
        $stmt->execute();
        $authorised = ($stmt->rowCount() != 0);
        $accountID = $stmt->fetch()["accountID"];
        
        // Verify account by deleting it from unverifiedAccounts
        if ($authorised) {
            $stmt = $dbh->prepare("DELETE FROM unverifiedAccounts WHERE accountID = :accountID;");
            $stmt->bindParam(":accountID", $accountID);
            $stmt->execute();
        }

        $dbh = null;
    }
    catch (PDOException $e) {
        $errorMsg = "There was an error accessing the database, please try again later.";
    }
}

// Log in user
if (isset($authorised) && $authorised) {
    die("Account created successfully, nothing more is implemented yet.");
}

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
            <h1>Verify Your Account</h1>
            <!-- TODO >Two Factor Authentication< for 2fa -->
            
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
                
                if (isset($_GET["action"])) {
                    echo "<p>The verification code has been sent to your email address, please check your spam folder.</p>";
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
            <!-- TODO value="Authenticate" for 2fa -->
        </form>
    </div>

    <script src="<?php echo $rootPath; ?>static/scripts/signinValidation.js"></script>
</body>

</html>
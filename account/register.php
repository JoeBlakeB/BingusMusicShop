<?php
/**
 * Register a new user account. The user will also
 * get added to the unverifiedAccounts table and
 * an authentication email will be sent to their email 
 * address, they will be redirected to verification.php
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

// Set everything except "all" to true so theres no errors when the user first loads the page
$valid = ["name"=>true, "email"=>true, "password"=>true, "passwordConf"=>true, "all"=>false];
// Check everything is valid
if (!empty($_POST["name"])
        && !empty($_POST["email"])
        && !empty($_POST["password"])
        && !empty($_POST["passwordConf"])) {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $password1 = $_POST["password"];
    $password2 = $_POST["passwordConf"];
    
    $valid["name"] = ($name != "" && strlen($name) <= 256);

    $valid["email"] = (strlen($email) <= 256 &&
        preg_match("/^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/", $email));

    $valid["password"] = (8 <= strlen($password1)
        && strlen($password1) <= 256
        && preg_match("/[a-z]/", $password1)
        && preg_match("/[A-Z]/", $password1)
        && preg_match("/[0-9]/", $password1));

    $valid["passwordConf"] = $password1 === $password2;

    $valid["all"] = $valid["name"] && $valid["email"] && $valid["password"] && $valid["passwordConf"];
}

// Everything is valid
if ($valid["all"]) {
    include "utils.php";
    try {
        $dbh = sqlConnect();

        // Check email is not used
        $email = strtolower($email);
        $stmt = $dbh->prepare("SELECT * FROM accounts WHERE email = :email");
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        $emailUsed = ($stmt->rowCount() != 0);

        // Email is not used, create account
        if (!$emailUsed) {
            // Generate other details for database
            $passwordHash = password_hash($password1, PASSWORD_DEFAULT);
            $verificationCode = str_pad(rand(1, 999999), 6, 0, STR_PAD_LEFT);
            $expires = date("Y-m-d H:i:s", strtotime("+1 day"));

            // Add account to database
            $stmt = $dbh->prepare("INSERT INTO accounts (email, passwordHash, fullName)
                VALUES (:email, :passwordHash, :fullName);
                INSERT INTO unverifiedAccounts (accountID, verificationCode, expires)
                VALUES (LAST_INSERT_ID(), :verificationCode, :expires);");
            $stmt->bindParam(":email", $email);
            $stmt->bindParam(":passwordHash", $passwordHash);
            $stmt->bindParam(":fullName", $name);
            $stmt->bindParam(":verificationCode", $verificationCode);
            $stmt->bindParam(":expires", $expires);
            $stmt->execute();

            // Do the rest after closing sql connection
            $success = true;
        }

        $dbh = null;
    }
    catch (PDOException $e) {
        $errorMsg = "There was an error accessing the database, please try again later.";
    }
}

if (isset($success)) {
    // Add email to session
    session_unset();
    $_SESSION["email"] = $email;

    // Send verification email
    $subject = "Bingus Music Shop Account Verification";
    $message = "Your verification code is: $verificationCode\n" .
        "Please go to https://s5411045.bucomputing.uk/wpassignment/account/verification.php to continue.\n" .
        "The code is valid for 24 hours.";
    mail($email, $subject, $message);

    // Redirect to verification page
    header("Location: verification.php?action=register");
    die();
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
        <form method="post" class="form">
            <h1>Register</h1>

            <?php
            // Tell user if there was an error creating the account.
            if ($valid["all"]) {
                ?>
                <div class="error"><p>
                    There has been an error creating your account.
                    <?php if (isset($errorMsg)) echo "<br>" . $errorMsg; ?>
                </p></div>
                <?php
            }
            ?>

            <div class="inputContainer <?php echo $valid["name"] ? "" : "error" ?>" id="nameContainer">
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
            <div class="inputContainer <?php echo ($valid["email"] && !isset($emailUsed)) ? "" : "error" ?>" id="emailContainer">
                <label for="email">Email Address:</label>
                <input maxlength="256" type="text" name="email" id="email" value="<?php
                    if (isset($_POST["email"])) {
                        echo htmlspecialchars($_POST["email"]);
                    }?>" required>
                <p><?php
                    if (isset($emailUsed)) {
                        echo "This email address is already in use, please sign into your existing account instead.";
                    }
                    else if (!$valid["email"]) {
                        echo "Please enter a valid email address.";
                    }
                ?></p>
            </div>

            <div class="inputContainer <?php echo $valid["password"] ? "" : "error" ?>" id="passwordContainer">
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
            
            <div class="inputContainer <?php echo $valid["passwordConf"] ? "" : "error" ?>" id="passwordConfContainer">
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
    <div class="signInContent" id="switchSignInButton"><a href="signin.php">
        <p>Already have an account? <br>Sign In</p>
    </a></div>

    <script src="<?php echo $rootPath; ?>static/scripts/signinValidation.js"></script>
</body>

</html>
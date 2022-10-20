<?php
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

            <label for="name">Your Name:</label>
            <input type="text" name="name" id="name" required>

            <label for="email">Email Address:</label>
            <input type="text" name="email" id="email" required>

            <label for="password1">Password:</label>
            <input type="password" name="password1" id="password1" required>

            <label for="password2">Confirm Password:</label>
            <input type="password" name="password2" id="password2" required>

            <input type="submit" value="Register">
        </form>
    </div>
    <button class="signInContent" id="switchSignInButton">Already have an account? <br>Sign In</button>
</body>

</html>
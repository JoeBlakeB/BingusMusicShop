<?php
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

            <label for="email">Email Address:</label>
            <input type="text" name="email" id="email" required>

            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required>

            <input type="checkbox" id="remember" name="remember" value="true">
            <label for="remember"> Keep me signed in.</label>

            <input type="submit" value="Log In">
        </form>
    </div>
    <button class="signInContent" id="switchSignInButton">New customer? <br>Create an account</button>
</body>

</html>
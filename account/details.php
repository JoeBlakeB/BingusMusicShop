<?php
/**
 * Check verification codes sent via email, if the code is valid
 * the user will be signed in, and if its a new account the
 * account will be verified by deleting them from unverifiedAccounts.
 * If there are any expired codes, their accounts will be deleted.
 * 
 * @author Joe Baker
 * @copyright Copyright (c) 2022 JoeBlakeB, all rights reserved.
 */

$rootPath = "../";
set_include_path("{$rootPath}include");
session_start();

// Redirect to login if not signed in
if (!isset($_SESSION["account"])) {
    header("Location: signin.php");
    die();
}

var_dump($_SESSION);
?>
<br>
You are signed in as <?php echo $_SESSION["account"]["fullName"]; ?>.
<br>
<a href="signout.php">Sign out</a>
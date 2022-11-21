<?php

/**
 * Show the login, register, and other account pages.
 * 
 * @author Joe Baker
 * @copyright Copyright (c) 2022 JoeBlakeB, all rights reserved.
 */

require "model/accountModel.php";

class AccountController extends AbstractController {
    /**
     * Decide which account page to show.
     * 
     * @param array $uri The path of the page.
     */
    public function invoke() {
        if (!isset($this->uri[1])) {
            return $this->detailsPage();
        }
        else if (!isset($this->uri[2])) {
            $this->runPageMethod($this->uri[1]);
        }
        $this->showError(404, "Page Not Found", "The page you requested could not be found.");
    }

    /**
     * Show the account details.
     */
    public function detailsPage() {
        if (!isset($_SESSION["account"])) {
            return header("Location: signin");
        }
        require "view/account/details.php";
    }

    /**
     * Sign in to an existing account, will then redirect to account
     * index page or verification page if 2fa is enabled.
     */
    public function signinPage() {
        // If the user is already signed in, redirect to the details page
        if (isset($_SESSION["account"])) {
            return header("Location: .");
        }
        
        // If the form was submitted, check the credentials and sign in
        if (isset($_POST["email"]) && isset($_POST["password"])) {
            try {
                $model = new AccountModel();
                $account = $model->getAccountByEmail($_POST["email"]);
                if ($account && password_verify($_POST["password"], $account["passwordHash"])) {
                    session_unset();

                    // In unverified, redirect to verification page
                    $verificationCode = $model->getVerificationCode($account["accountID"]);
                    if ($verificationCode) {
                        $_SESSION["verification"] = [
                            "email" => $account["email"],
                            "code" => $verificationCode
                        ];
                        return header("Location: verification");
                    }

                    // If 2fa is enabled, redirect to 2fa page
                    if ($account["2faEnabled"]) {
                        $this->send2faCode($_POST["email"]);
                        return header("Location: authentication");
                    }

                    // Sign in
                    return $this->signIn($account);
                }
                else {
                    $error = "The email or password you entered is incorrect.";
                    http_response_code(401);
                }
                $model = null;
            }
            catch (PDOException $e) {
                $error = "An error occurred while signing in. Please try again later.";
                http_response_code(500);
            }
        }
        
        // Show the sign in page
        require "view/account/signin.php";
    }

    /**
     * Create a new account that is unverified and send an email
     * for the user to verify their account.
     */
    public function registerPage() {
        // If the user is already signed in, redirect to the details page
        if (isset($_SESSION["account"])) {
            return header("Location: .");
        }

        // If the form was submitted, check the credentials
        if (isset($_POST["name"]) && isset($_POST["email"]) && isset($_POST["password"]) && isset($_POST["passwordConf"])) {
            $valid = [
                "name" => ($_POST["name"] != "" && strlen($_POST["name"]) <= 256),
                "email" => $this->isEmailValid($_POST["email"]),
                "password" => $this->isPasswordValid($_POST["password"]),
                "passwordConf" => ($_POST["password"] == $_POST["passwordConf"])
            ];

            // Everything is valid
            if ($valid["name"] && $valid["email"] && $valid["password"] && $valid["passwordConf"]) {
                try {
                    $model = new AccountModel();
                    $account = $model->getAccountByEmail($_POST["email"]);
                    if (!$account) {
                        $verificationCode = $this->generateVerificationCode();
                        $model->createAccount($_POST["name"], $_POST["email"], $_POST["password"], $verificationCode);
                        $model = null;
                        $this->sendVerificationEmail($_POST["email"], $verificationCode);
                        return header("Location: verification");
                    }
                    else {
                        $model = null;
                        $error = "An account with that email address already exists.";
                        http_response_code(403);
                    }
                }
                catch (PDOException $e) {
                    $error = "An error occurred while creating your account. Please try again later.";
                    http_response_code(500);
                }
            }
        }
        // Dont show errors if the form was not submitted
        else {
            $valid = [
                "name" => true,
                "email" => true,
                "password" => true,
                "passwordConf" => true
            ];
        }

        require 'view/account/register.php';
    }

    /**
     * Clear the session and tell the user they are now signed out.
     */
    public function signoutPage() {
        $alreadySignedOut = !isset($_SESSION["account"]);
        session_destroy();
        require 'view/account/signout.php';
    }

    /**
     * Show the page to verify the user's email address.
     * Can verify via code in form or via URL.
     * 
     * With URL verification, the is email is base64 encoded
     * verification?code=000000&email=ZXhhbXBsZUBleGFtcGxlLmNvbQ==
     */
    public function verificationPage() {
        if (isset($_SESSION["account"])) {
            return header("Location: .");
        }

        // Get the verification code and email from various input methods
        if (isset($_POST["auth"])) {
            $submit["code"] = $_POST["auth"];
            if (isset($_POST["email"])) {
                $submit["email"] = $_POST["email"];
            }
            else if (isset($_SESSION["verification"]["email"])) {
                $submit["email"] = $_SESSION["verification"]["email"];
            }
        }
        else if (isset($_GET["code"]) && isset($_GET["email"])) {
            $submit["code"] = $_GET["code"];
            $submit["email"] = base64_decode($_GET["email"]);
        }

        // If the data was submitted, verify the code
        if (isset($submit["email"])) {
            // Check code before loading database to save time if its in session
            if (isset($_SESSION["verification"]) &&
                $_SESSION["verification"]["email"] == $submit["email"]) {
                    $precheck = $_SESSION["verification"]["code"] == $submit["code"];
            }
            else {
                $precheck = true;
            }
            // Check the code and sign in if it is correct
            if ($precheck) {
                try {
                    $model = new AccountModel();
                    $account = $model->getAccountByEmail($submit["email"]);
                    $verificationCode = $model->getVerificationCode($account["accountID"]);
                    if ($verificationCode == $submit["code"]) {
                        $model->verifyAccount($account["accountID"]);
                        $model = null;
                        return $this->signIn($account);
                    }
                    else {
                        $model = null;
                        $error = "The verification code you entered is incorrect.";
                        http_response_code(401);
                    }
                }
                catch (PDOException $e) {
                    $error = "An error occurred while verifying your account. Please try again later.";
                    http_response_code(500);
                }
            }
        }

        $alreadyHaveEmail = isset($_SESSION["verification"]["email"]);
        $formType = "register";
        require 'view/account/verification.php';
    }

    /**
     * Check the authentication code and sign in if its correct.
     */
    public function authenticationPage() {
        if (!isset($_SESSION["2fa"])) {
            return header("Location: .");
        }

        if (isset($_POST["auth"])) {
            if ($_POST["auth"] == $_SESSION["2fa"]["code"]) {
                $model = new AccountModel();
                $account = $model->getAccountByEmail($_SESSION["2fa"]["email"]);
                $model = null;
                return $this->signIn($account);
            }
            else {
                $error = "The code you entered is incorrect.";
                http_response_code(401);
            }
        }

        $alreadyHaveEmail = true;
        $formType = "signin";
        require 'view/account/verification.php';
    }

    /**
     * Set the session to the account details.
     * 
     * @param array $account The account record
     */
    public function signIn($account) {
        $_SESSION["account"] = [
            "fullName" => $account["fullName"],
            "email" => $account["email"],
            "isAdmin" => $account["isAdmin"]
        ];
        header("Location: .");
    }

    /**
     * Check email address is valid.
     * 
     * @param string $email The email to check
     * @return boolean for if its valid
     */
    public function isEmailValid($email) {
        return (strlen($email) <= 256 &&
            preg_match("/^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/", $email));
    }

    /**
     * Check password is valid.
     * 
     * @param string $password The password to check
     * @return boolean for if its valid
     */
    public function isPasswordValid($password) {
        return (8 <= strlen($password)
            && strlen($password) <= 256
            && preg_match("/[a-z]/", $password)
            && preg_match("/[A-Z]/", $password)
            && preg_match("/[0-9]/", $password));
    }

    /**
     * Generate a 6 digit verification code.
     * 
     * @return string The code
     */
    public function generateVerificationCode() {
        return str_pad(rand(1, 999999), 6, 0, STR_PAD_LEFT);
    }

    /**
     * Send a 2fa code to the user's email and set session variable.
     * 
     * @param string $email The email to send to
     */
    public function send2faCode($email) {
        $verificationCode = $this->generateVerificationCode();
        $subject = "Bingus Music Shop Two Factor Authentication";
        $message = "Your Two Factor Authentication code is: $verificationCode";
        mail($email, $subject, $message);
        $_SESSION["2fa"] = [
            "email" => $email,
            "code" => $verificationCode
        ];
    }

    /**
     * Send a verification email to the user and set session variable.
     * 
     * @param string $email The email to send to
     * @param string $verificationCode The code
     */
    public function sendVerificationEmail($email, $verificationCode) {
        $subject = "Bingus Music Shop Account Verification";
        $message = "Your verification code is: $verificationCode\n" .
            "Please go to https://s5411045.bucomputing.uk/BingusMusicShop.php/account/verification?code=$verificationCode&email=" .
            rtrim(base64_encode($email), "=") . 
            " to verify your account.\nThe code is valid for 24 hours.";
        mail($email, $subject, $message);
        session_unset();
        $_SESSION["verification"] = [
            "email" => $email,
            "code" => $verificationCode
        ];
    }
}

<?php

/**
 * Show the login, register, and other account pages.
 * 
 * @author Joe Baker
 * @copyright Copyright (c) 2022 JoeBlakeB, all rights reserved.
 */

require_once("model/accountModel.php");

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
        $this->maxPathLength(2);
        $this->runPageMethod($this->uri[1]);
    }

    /**
     * Show the account details.
     */
    public function detailsPage() {
        if (!isset($_SESSION["account"])) {
            return header("Location: $this->basePath/account/signin");
        }
        require_once("view/account/details.php");
    }

    /**
     * Manage the users addresses
     */
    public function addressesPage() {
        if (!isset($_SESSION["account"])) {
            return header("Location: signin");
        }
        $accountModel = new AccountModel();
        $account = $accountModel->getAccountByID($_SESSION["account"]["id"]);

        if (isset($_GET["new"])) {
            $valid = $this->validateAddress($_POST, $accountModel->countriesList);
            if (!$valid[0]) {
                return require_once("view/account/manage/editAddress.php");
            }
            try {
                $account->addAddress($_POST);
            }
            catch (PDOException $e) {
                $this->showError(500, "Internal Server Error", "An error occurred while adding the address. Please try again later.");
            }
            return header("Location: addresses");
        }
        else if (isset($_GET["edit"])) {
            $address = $account->getAddress($_GET["edit"]);
            $valid = $this->validateAddress($_POST, $accountModel->countriesList);
            if (!$address) {
                return $this->showError(404, "Address Not Found", "The address you are trying to edit does not exist.", "addresses", "Back to All Addresses");
            }
            if ($valid[0]) {
                try {
                    $address->update($_POST);
                    return header("Location: addresses");
                }
                catch (PDOException $e) {
                    $this->showError(500, "Internal Server Error", "An error occurred while editing the address. Please try again later.");
                }
            }
            return require_once("view/account/manage/editAddress.php");
        }
        else if (isset($_GET["delete"])) {
            $address = $account->getAddress($_GET["delete"]);
            if (!$address) {
                return $this->showError(404, "Address Not Found", "The address you are trying to delete does not exist.", "addresses", "Back to All Addresses");
            }
            try {
                $address->delete();
            }
            catch (PDOException $e) {
                $this->showError(500, "Internal Server Error", "An error occurred while deleting the address. Please try again later.");
            }
            return header("Location: addresses");
        }

        $addresses = $account->getAddresses();
        require_once("view/account/manage/addresses.php");
    }

    /**
     * Manage the users payment methods
     */
    public function paymentsPage() {
        if (!isset($_SESSION["account"])) {
            return header("Location: signin");
        }
        $accountModel = new AccountModel();
        $account = $accountModel->getAccountByID($_SESSION["account"]["id"]);

        if (isset($_GET["new"])) {
            $valid = $this->validateCard($_POST);
            if (!$valid[0]) {
                return require_once("view/account/manage/editPaymentCard.php");
            }
            try {
                $account->addCard($_POST);
            } catch (PDOException $e) {
                $this->showError(500, "Internal Server Error", "An error occurred while adding the address. Please try again later.");
            }
            return header("Location: payments");
        }
        else if (isset($_GET["edit"])) {
            $card = $account->getCard($_GET["edit"]);
            $valid = $this->validateCard($_POST);
            if (!$card) {
                return $this->showError(404, "Payment Method Not Found", "The payment method you are trying to edit does not exist.", "payments", "Back to All Payment Methods");
            }
            if ($valid[0]) {
                try {
                    $card->update($_POST);
                    return header("Location: payments");
                } catch (PDOException $e) {
                    $this->showError(500, "Internal Server Error", "An error occurred while editing the payment method. Please try again later.");
                }
            }
            return require_once("view/account/manage/editPaymentCard.php");
        }
        else if (isset($_GET["delete"])) {
            $card = $account->getCard($_GET["delete"]);
            if (!$card) {
                return $this->showError(404, "Payment Method Not Found", "The payment method you are trying to delete does not exist.", "payments", "Back to All Payment Methods");
            }
            try {
                $card->delete();
            } catch (PDOException $e) {
                $this->showError(500, "Internal Server Error", "An error occurred while deleting the payment method. Please try again later.");
            }
            return header("Location: payments");
        }

        $cards = $account->getCards();
        require_once("view/account/manage/payments.php");
    }

    /**
     * Manage the users password and two factor authentication
     */
    public function securityPage() {
        if (!isset($_SESSION["account"])) {
            return header("Location: signin");
        }
        $accountModel = new AccountModel();
        $account = $accountModel->getAccountByID($_SESSION["account"]["id"]);
        if (isset($_GET["2fa"])) {
            $account->setTwoFactorEnabled($_GET["2fa"] == "enable");
        }
        if (!empty($_POST)) {
            $valid = [
                "currentPassword" => $account->verifyPassword($_POST["currentPassword"]),
                "newPassword" => $this->isPasswordValid($_POST["newPassword"]),
                "confirmPassword" => $_POST["newPassword"] == $_POST["confirmPassword"]
            ];
            if ($valid["currentPassword"] && $valid["newPassword"] && $valid["confirmPassword"]) {
                try {
                    $account->changePassword($_POST["newPassword"]);
                    $success = "Your password has been changed.";
                }
                catch (PDOException $e) {
                    $this->showError(500, "Internal Server Error", "An error occurred while changing the password. Please try again later.");
                }
            }
        }
        require_once("view/account/manage/security.php");
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
                if ($account && $account->verifyPassword($_POST["password"])) {
                    session_unset();

                    // In unverified, redirect to verification page
                    $unverified = $account->getIsUnverified();
                    if ($unverified) {
                        $_SESSION["verification"] = [
                            "email" => $account->getRealEmail(),
                            "code" => $unverified["code"],
                        ];
                        return header("Location: verification");
                    }

                    // If 2fa is enabled, redirect to 2fa page
                    if ($account->getIsTwoFactorEnabled()) {
                        $this->send2faCode($_POST["email"]);
                        return header("Location: authentication");
                    }

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
        
        require_once("view/account/signin/signin.php");
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

        require_once("view/account/signin/register.php");
    }

    /**
     * Clear the session and tell the user they are now signed out.
     */
    public function signoutPage() {
        $alreadySignedOut = !isset($_SESSION["account"]);
        session_destroy();
        require_once("view/account/signin/signout.php");
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
            try {
                if ($precheck) {
                    $model = new AccountModel();
                    $account = $model->getAccountByEmail($submit["email"]);
                    $unverified = $account->getIsUnverified();
                    if (($unverified && $unverified["code"] == $submit["code"]) || !$unverified) {
                        $account->verify();
                        return $this->signIn($account);
                    }
                }
                $error = "The verification code you entered is incorrect.";
                http_response_code(401);
            }
            catch (PDOException $e) {
                $error = "An error occurred while verifying your account. Please try again later.";
                http_response_code(500);
            }
            
        }

        $alreadyHaveEmail = isset($_SESSION["verification"]["email"]);
        $formType = "register";
        require_once("view/account/signin/verification.php");
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
        require_once("view/account/signin/verification.php");
    }

    /**
     * Set the session to the account details.
     * 
     * @param Account $account The account record
     */
    public function signIn($account) {
        $_SESSION["account"] = [
            "id" => $account->getId(),
            "fullName" => $account->getFullName(),
            "email" => $account->getEmail(),
            "isAdmin" => $account->getIsAdmin()
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
            "Please go to https://" . $_SERVER["HTTP_HOST"] . $this->basePath . 
            "/account/verification?code=$verificationCode&email=" . 
            rtrim(base64_encode($email), "=") . 
            " to verify your account.\nThe code is valid for 24 hours.";
        mail($email, $subject, $message);
        session_unset();
        $_SESSION["verification"] = [
            "email" => $email,
            "code" => $verificationCode
        ];
    }

    /**
     * Validate a users address.
     * 
     * @param array $data The data to validate.
     * @return array of bools for each field.
     */
    public function validateAddress($data, $countriesList) {
        if (empty($data)) {
            return [
                0 => false,
                "name" => true,
                "address1" => true,
                "address2" => true,
                "city" => true,
                "county" => true,
                "postcode" => true,
                "country" => true
            ];
        }
        $valid = [];
        $valid["name"] = (isset($data["name"]) &&
            strlen($data["name"]) <= 256 &&
            strlen($data["name"]) > 0);
        $valid["address1"] = (isset($data["address1"]) &&
            strlen($data["address1"]) <= 128 &&
            strlen($data["address1"]) > 0);
        $valid["address2"] = (isset($data["address2"]) &&
            strlen($data["address2"]) <= 128);
        $valid["city"] = (isset($data["city"]) &&
            strlen($data["city"]) <= 64 &&
            strlen($data["city"]) > 0);
        $valid["county"] = (isset($data["county"]) &&
            strlen($data["county"]) <= 64);
        $valid["country"] = (isset($data["country"]) &&
            array_key_exists($data["country"], $countriesList));

        if ($data["country"] == "GB") {
            $valid["postcode"] = !!(isset($data["postcode"]) &&
                preg_match("/^[A-Z]{1,2}[0-9]([0-9A-Z]|)(| )[0-9][A-Z]{2}$/",
                strtoupper($data["postcode"])));
        }
        else {
            $valid["postcode"] = (isset($data["postcode"]) &&
                strlen($data["postcode"]) <= 8);
        }
        $valid[0] = (
            $valid["name"] &&
            $valid["address1"] &&
            $valid["address2"] &&
            $valid["city"] &&
            $valid["county"] &&
            $valid["country"] &&
            $valid["postcode"]);
        return $valid;
    }

    /**
     * Validate a users card.
     * 
     * @param array $data The data to validate.
     * @return array of bools for each field.
     */
    public function validateCard($data) {
        if (empty($data)) {
            return [
                0 => false,
                "name" => true,
                "cardNumber" => true,
                "expiryMonth" => true,
                "expiryYear" => true,
                "securityCode" => true
            ];
        }
        $valid = [];
        $valid["name"] = (isset($data["name"]) &&
            strlen($data["name"]) <= 256 &&
            strlen($data["name"]) > 0);
        $valid["cardNumber"] = (isset($data["cardNumber"]) &&
            preg_match("/^[0-9]{16}$/", $data["cardNumber"]));
        $valid["expiryMonth"] = (isset($data["expiryMonth"]) &&
            $data["expiryMonth"] >= 1 &&
            $data["expiryMonth"] <= 12);
        $valid["expiryYear"] = (isset($data["expiryYear"]) &&
            $data["expiryYear"] >= date("Y") &&
            $data["expiryYear"] <= date("Y") + 15);
        $valid["securityCode"] = (isset($data["securityCode"]) &&
            preg_match("/^[0-9]{3}$/", $data["securityCode"]));
        $valid[0] = (
            $valid["name"] &&
            $valid["cardNumber"] &&
            $valid["expiryMonth"] &&
            $valid["expiryYear"] &&
            $valid["securityCode"]);
        return $valid;
    }
}

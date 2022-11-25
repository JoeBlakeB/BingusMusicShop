<?php

/**
 * The class for managing accounts.
 * 
 * @author Joe Baker
 * @copyright Copyright (c) 2022 JoeBlakeB, all rights reserved.
 */

include "model/abstractModel.php";

class AccountModel extends AbstractModel {
    public function __construct() {
        parent::__construct();
        $this->deleteExpiredAccounts();
    }

    /**
     * Get an account by its email address.
     * 
     * @param string $email The email address of the account
     * @return Account The account
     */
    public function getAccountByEmail($email) {
        $email = strtolower($email);
        $stmt = $this->dbh->prepare(
            "SELECT * FROM accounts
            LEFT JOIN unverifiedAccounts
            USING (accountID)
            WHERE email = :email");
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        $account = $stmt->fetch();
        if ($account) {
            return new Account($this->dbh, $account);
        }
        return null;
    }

    /**
     * Get an account by its ID.
     * 
     * @param int $accountID The ID of the account
     * @return Account The account
     */
    public function getAccountByID($accountID) {
        $stmt = $this->dbh->prepare(
            "SELECT * FROM accounts
            LEFT JOIN unverifiedAccounts
            USING (accountID)
            WHERE accountID = :accountID");
        $stmt->bindParam(":accountID", $accountID);
        $stmt->execute();
        $account = $stmt->fetch();
        if ($account) {
            return new Account($this->dbh, $account);
        }
        return null;
    }

    /**
     * Create a new account, will be unverified by default.
     * 
     * @param string $name The full name of the user
     * @param string $email The email address of the user
     * @param string $password The password to store the hash of
     * @param string The verification code to send the user
     */
    public function createAccount($name, $email, $password, $verificationCode) {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $expires = date("Y-m-d H:i:s", strtotime("+1 day"));

        $stmt = $this->dbh->prepare(
            "INSERT INTO accounts (email, passwordHash, fullName)
            VALUES (:email, :passwordHash, :fullName);
            INSERT INTO unverifiedAccounts (accountID, verificationCode, expires)
            VALUES (LAST_INSERT_ID(), :verificationCode, :expires);");
        $stmt->bindParam(":email", strtolower($email));
        $stmt->bindParam(":passwordHash", $passwordHash);
        $stmt->bindParam(":fullName", $name);
        $stmt->bindParam(":verificationCode", $verificationCode);
        $stmt->bindParam(":expires", $expires);
        $stmt->execute();
    }

    /**
     * Delete old unverified accounts if they have expired.
     */
    public function deleteExpiredAccounts() {
        $stmt = $this->dbh->prepare(
            "DELETE accounts, unverifiedAccounts FROM accounts
            INNER JOIN unverifiedAccounts
            ON accounts.accountID = unverifiedAccounts.accountID
            WHERE expires < NOW();");
        $stmt->execute();
    }

    /**
     * Get all accounts including unverified accounts.
     * 
     * @return array The accounts
     */
    public function getAllAccounts() {
        $stmt = $this->dbh->prepare(
            "SELECT * FROM accounts
            LEFT JOIN unverifiedAccounts
            USING (accountID)
            ORDER BY isAdmin DESC, accountID");
        $stmt->execute();
        return $this->createObjectArray($stmt->fetchAll(), $dbh, Account::class);
    }
}

class Account implements ModelObjectInterface {
    private $dbh;
    private $id;
    private $email;
    private $passwordHash;
    private $fullName;
    private $isAdmin;
    private $twoFactorEnabled;
    private $verificationCode;
    private $expires;

    public function __construct(&$dbh, $data) {
        $this->dbh = $dbh;
        $this->id = $data["accountID"];
        $this->email = $data["email"];
        $this->passwordHash = $data["passwordHash"];
        $this->fullName = $data["fullName"];
        $this->isAdmin = $data["isAdmin"];
        $this->twoFactorEnabled = $data["twoFactorEnabled"];
        $this->verificationCode = $data["verificationCode"];
        $this->expires = $data["expires"];
    }

    /**
     * @return int The ID of the account
     */
    public function getID() {
        return $this->id;
    }

    /**
     * @return bool Whether the account is admin
     */
    public function getIsAdmin() {
        return !!$this->isAdmin;
    }

    /**
     * @return bool Whether the account has two factor enabled
     */
    public function getIsTwoFactorEnabled() {
        return !!$this->twoFactorEnabled;
    }

    /**
     * @return string The email address escaped for HTML
     */
    public function getEmail() {
        return htmlspecialchars($this->email);
    }

    /**
     * @return string The original email address
     */
    public function getRealEmail() {
        return $this->email;
    }

    /**
     * @return string The full name escaped for HTML
     */
    public function getFullName() {
        return htmlspecialchars($this->fullName);
    }

    /**
     * If the account is unverified,
     * get the verification code and expiry date.
     * 
     * @return array The verification code and expiry date
     */
    public function getIsUnverified() {
        if (is_null($this->verificationCode)) {
            return false;
        }
        return [
            "code" => $this->verificationCode,
            "expires" => $this->expires
        ];
    }

    /**
     * Verify the account by deleting it from the unverifiedAccounts table.
     */
    public function verify() {
        $stmt = $this->dbh->prepare(
            "DELETE FROM unverifiedAccounts
            WHERE accountID = :accountID");
        $stmt->bindParam(":accountID", $this->id);
        $stmt->execute();
        $this->verificationCode = null;
        $this->expires = null;
    }

    /**
     * Delete the account from the database.
     */
    public function delete() {
        $stmt = $this->dbh->prepare(
            "DELETE FROM accounts
            WHERE accountID = :accountID");
        $stmt->bindParam(":accountID", $this->id);
        $stmt->execute();
        $this->id = null;
    }

    /**
     * Set admin status for the account.
     * 
     * @param bool $isAdmin Whether to set admin status to true or false
     */
    public function setAdmin($isAdmin) {
        $stmt = $this->dbh->prepare(
            "UPDATE accounts
            SET isAdmin = :isAdmin
            WHERE accountID = :accountID");
        $stmt->bindParam(":accountID", $this->id);
        $isAdmin = (int)$isAdmin;
        $stmt->bindParam(":isAdmin", $isAdmin);
        $stmt->execute();
        $this->isAdmin = $isAdmin;
    }

    /**
     * Check if the password matches the hash.
     * 
     * @param string $password The password to check
     * @return bool Whether the password matches the hash
     */
    public function verifyPassword($password) {
        return password_verify($password, $this->passwordHash);
    }
}



    // /**
    //  * See if an account is verified,
    //  * and get the verification code if not.
    //  * 
    //  * @param int $accountID The accounts ID
    //  * @return string the verification code if unverified
    //  * @return bool false if verified
    //  */
    // public function getVerificationCode($accountID) {
    //     $stmt = $this->dbh->prepare(
    //         "SELECT * FROM unverifiedAccounts 
    //         WHERE accountID = :accountID");
    //     $stmt->bindParam(":accountID", $accountID);
    //     $stmt->execute();
    //     $unverifiedAccount = $stmt->fetch();
    //     if (!is_null($unverifiedAccount)) {
    //         return $unverifiedAccount["verificationCode"];
    //     }
    //     return false;
    // }

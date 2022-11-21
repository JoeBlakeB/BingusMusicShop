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
     * @return array The account
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
        return $stmt->fetch();
    }

    /**
     * Get an account by its ID.
     * 
     * @param int $accountID The ID of the account
     * @return array The account
     */
    public function getAccountByID($accountID) {
        $stmt = $this->dbh->prepare(
            "SELECT * FROM accounts
            LEFT JOIN unverifiedAccounts
            USING (accountID)
            WHERE accountID = :accountID");
        $stmt->bindParam(":accountID", $accountID);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * See if an account is verified,
     * and get the verification code if not.
     * 
     * @param int $accountID The accounts ID
     * @return string the verification code if unverified
     * @return bool false if verified
     */
    public function getVerificationCode($accountID) {
        $stmt = $this->dbh->prepare(
            "SELECT * FROM unverifiedAccounts 
            WHERE accountID = :accountID");
        $stmt->bindParam(":accountID", $accountID);
        $stmt->execute();
        $unverifiedAccount = $stmt->fetch();
        if (!is_null($unverifiedAccount)) {
            return $unverifiedAccount["verificationCode"];
        }
        return false;
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
     * Verify an account by deleting it from the unverifiedAccounts table.
     * 
     * @param int $accountID The ID of the account to verify
     */
    public function verifyAccount($accountID) {
        $stmt = $this->dbh->prepare(
            "DELETE FROM unverifiedAccounts
            WHERE accountID = :accountID");
        $stmt->bindParam(":accountID", $accountID);
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
        return $stmt->fetchAll();
    }

    /**
     * Set admin status for an account.
     * 
     * @param int $accountID Who to set admin status for
     * @param bool $isAdmin Whether to set admin status to true or false
     */
    public function setAdmin($accountID, $isAdmin) {
        $stmt = $this->dbh->prepare(
            "UPDATE accounts
            SET isAdmin = :isAdmin
            WHERE accountID = :accountID");
        $stmt->bindParam(":accountID", $accountID);
        $isAdmin = (int)$isAdmin;
        $stmt->bindParam(":isAdmin", $isAdmin);
        $stmt->execute();
    }

    /**
     * Delete an account.
     * 
     * @param int $accountID The account to delete
     */
    public function deleteAccount($accountID) {
        $stmt = $this->dbh->prepare(
            "DELETE FROM accounts
            WHERE accountID = :accountID");
        $stmt->bindParam(":accountID", $accountID);
        $stmt->execute();
    }
}
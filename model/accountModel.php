<?php

/**
 * The class for managing accounts.
 * 
 * @author Joe Baker
 * @copyright Copyright (c) 2022 JoeBlakeB, all rights reserved.
 */

require "model/abstractModel.php";

class AccountModel extends AbstractModel {
    public function __construct() {
        parent::__construct();
        require "model/countriesList.php";
        $this->countriesList = $countriesList;
    }

    /**
     * Gets an account by a column and value.
     * 
     * @param string $where The column to search by.
     * @param string $value The value to search for.
     */
    private function getAccountBase($where, $value) {
        $this->deleteExpiredAccounts();
        $stmt = $this->dbh->prepare(
            "SELECT * FROM accounts
            LEFT JOIN unverifiedAccounts
            USING (accountID)
            WHERE " . $where . " = :value");
        $stmt->execute(["value" => $value]);
        $account = $stmt->fetch();
        if ($account) {
            return new Account($this->dbh, $account);
        }
        return null;
    }

    /**
     * Get an account by its email address.
     * 
     * @param string $email The email address of the account
     * @return Account The account
     */
    public function getAccountByEmail($email) {
        $email = strtolower($email);
        return $this->getAccountBase("email", $email);
    }

    /**
     * Get an account by its ID.
     * 
     * @param int $accountID The ID of the account
     * @return Account The account
     */
    public function getAccountByID($accountID) {
        return $this->getAccountBase("accountID", $accountID);
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
        $stmt->execute([
            "email" => strtolower($email),
            "passwordHash" => $passwordHash,
            "fullName" => $name,
            "verificationCode" => $verificationCode,
            "expires" => $expires
        ]);
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
        return $this->createObjectArray($stmt->fetchAll(), Account::class);
    }
}

class Account extends AccountModel implements ModelObjectInterface {
    protected $dbh;
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
        $stmt->execute([
            "accountID" => $this->id
        ]);
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
        $stmt->execute([
            "accountID" => $this->id
        ]);
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
        $stmt->execute([
            "accountID" => $this->id,
            "isAdmin" => (int)$isAdmin
        ]);
        $this->isAdmin = $isAdmin;
    }

    /**
     * Set two factor status for the account.
     * 
     * @param bool $twoFactorEnabled Whether to set two factor status to true or false
     */
    public function setTwoFactorEnabled($twoFactorEnabled) {
        $stmt = $this->dbh->prepare(
            "UPDATE accounts
            SET twoFactorEnabled = :twoFactorEnabled
            WHERE accountID = :accountID");
        $stmt->execute([
            "accountID" => $this->id,
            "twoFactorEnabled" => (int)$twoFactorEnabled
        ]);
        $this->twoFactorEnabled = $twoFactorEnabled;
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

    /**
     * Change the password for the account.
     * 
     * @param string $password The new password
     */
    public function changePassword($password) {
        $stmt = $this->dbh->prepare(
            "UPDATE accounts
            SET passwordHash = :passwordHash
            WHERE accountID = :accountID");
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $stmt->execute([
            "accountID" => $this->id,
            "passwordHash" => $passwordHash
        ]);
        $this->passwordHash = $passwordHash;
    }

    /**
     * Add a new address to the account.
     * 
     * @param array $data The data to add
     */
    public function addAddress($data) {
        $stmt = $this->dbh->prepare(
            "INSERT INTO addresses (accountID, fullName, addressLine1, addressLine2, city, county, postcode, country)
            VALUES (:accountID, :name, :address1, :address2, :city, :county, :postcode, :country)");
        $stmt->execute([
            "accountID" => $this->id,
            "name" => $data["name"],
            "address1" => $data["address1"],
            "address2" => $data["address2"],
            "city" => $data["city"],
            "county" => $data["county"],
            "postcode" => $data["postcode"],
            "country" => $data["country"]
        ]);
    }

    /**
     * Get an array of addresses for the account.
     * 
     * @return array The addresses
     */
    public function getAddresses() {
        $stmt = $this->dbh->prepare(
            "SELECT * FROM addresses
            WHERE accountID = :accountID");
        $stmt->execute([
            "accountID" => $this->id
        ]);
        return $this->createObjectArray($stmt->fetchAll(), Address::class);
    }

    /**
     * Get a specific address for the account.
     */
    public function getAddress($addressID) {
        $stmt = $this->dbh->prepare(
            "SELECT * FROM addresses
            WHERE accountID = :accountID
            AND addressID = :addressID");
        $stmt->execute([
            "accountID" => $this->id,
            "addressID" => $addressID
        ]);
        $data = $stmt->fetch();
        if ($data === false) {
            return null;
        }
        return new Address($this->dbh, $data);
    }

    /**
     * Add a new card to the account
     * 
     * @param array $data The data to add.
     */
    public function addCard($data) {
        $stmt = $this->dbh->prepare(
            "INSERT INTO cards (accountID, fullName, cardNumber, securityCode, expiryMonth, expiryYear)
            VALUES (:accountID, :name, :cardNumber, :securityCode, :expiryMonth, :expiryYear)");
        $stmt->execute([
            "accountID" => $this->id,
            "name" => $data["name"],
            "cardNumber" => $data["cardNumber"],
            "securityCode" => $data["securityCode"],
            "expiryMonth" => $data["expiryMonth"],
            "expiryYear" => $data["expiryYear"]
        ]);
    }

    /**
     * Get an array of cards for the account.
     * 
     * @return array The cards
     */
    public function getCards() {
        $stmt = $this->dbh->prepare(
            "SELECT * FROM cards
            WHERE accountID = :accountID");
        $stmt->execute([
            "accountID" => $this->id
        ]);
        return $this->createObjectArray($stmt->fetchAll(), Card::class);
    }

    /**
     * Get a specific card for the account.
     */
    public function getCard($cardID) {
        $stmt = $this->dbh->prepare(
            "SELECT * FROM cards
            WHERE accountID = :accountID
            AND cardID = :cardID");
        $stmt->execute([
            "accountID" => $this->id,
            "cardID" => $cardID
        ]);
        $data = $stmt->fetch();
        if ($data === false) {
            return null;
        }
        return new Card($this->dbh, $data);
    }
}

class Address extends AccountModel implements ModelObjectInterface {
    protected $dbh;
    private $accountID;
    private $id;
    private $fullName;
    private $addressLine1;
    private $addressLine2;
    private $city;
    private $county;
    private $postcode;
    private $country;

    public function __construct(&$dbh, $data) {
        parent::__construct($dbh);
        $this->dbh = $dbh;
        $this->id = $data["addressID"];
        $this->accountID = $data["accountID"];
        $this->fullName = $data["fullName"];
        $this->addressLine1 = $data["addressLine1"];
        $this->addressLine2 = $data["addressLine2"];
        $this->city = $data["city"];
        $this->county = $data["county"];
        $this->postcode = $data["postcode"];
        $this->country = $data["country"];
    }
    
    public function __toString() {
        $address = [
            htmlspecialchars($this->fullName),
            htmlspecialchars($this->addressLine1),
            htmlspecialchars($this->addressLine2),
            htmlspecialchars($this->city),
            htmlspecialchars($this->county),
            htmlspecialchars($this->postcode),
            $this->countriesList[$this->country]
        ];
        return implode(",<br>", array_filter($address));
    }

    /**
     * @return int The address ID
     */
    public function getID() {
        return $this->id;
    }

    /**
     * @return string The full name on the address
     */
    public function getFullName() {
        return htmlspecialchars($this->fullName);
    }

    /**
     * @return string The first line of the address
     */
    public function getAddress1() {
        return htmlspecialchars($this->addressLine1);
    }

    /**
     * @return string The second line of the address (or empty string if not set)
     */
    public function getAddress2() {
        return htmlspecialchars($this->addressLine2);
    }

    /**
     * @return string The city
     */
    public function getCity() {
        return htmlspecialchars($this->city);
    }

    /**
     * @return string The county (or empty string if not set)
     */
    public function getCounty() {
        return htmlspecialchars($this->county);
    }

    /**
     * @return string The postcode
     */
    public function getPostcode() {
        return htmlspecialchars($this->postcode);
    }

    /**
     * @return string The two character country code
     */
    public function getCountryCode() {
        return $this->country;
    }

    /** 
     * Update the address.
     * 
     * @param array $data The data to update
     */
    public function update($data) {
        var_dump($data);
        $stmt = $this->dbh->prepare(
            "UPDATE addresses
            SET fullName = :name,
                addressLine1 = :address1,
                addressLine2 = :address2,
                city = :city,
                county = :county,
                postcode = :postcode,
                country = :country
            WHERE addressID = :addressID");
        $stmt->execute([
            "addressID" => $this->id,
            "name" => $data["name"],
            "address1" => $data["address1"],
            "address2" => $data["address2"],
            "city" => $data["city"],
            "county" => $data["county"],
            "postcode" => $data["postcode"],
            "country" => $data["country"]
        ]);
    }

    /**
     * Delete the address.
     */
    public function delete() {
        $stmt = $this->dbh->prepare(
            "DELETE FROM addresses
            WHERE addressID = :addressID");
        $stmt->execute([
            "addressID" => $this->id
        ]);
    }
}

class Card extends AccountModel implements ModelObjectInterface {
    protected $dbh;
    private $accountID;
    private $id;
    private $fullName;
    private $cardNumber;
    private $securityCode;
    private $expiryMonth;
    private $expiryYear;

    public function __construct(&$dbh, $data) {
        parent::__construct($dbh);
        $this->dbh = $dbh;
        $this->id = $data["cardID"];
        $this->accountID = $data["accountID"];
        $this->fullName = $data["fullName"];
        $this->cardNumber = $data["cardNumber"];
        $this->securityCode = $data["securityCode"];
        $this->expiryMonth = $data["expiryMonth"];
        $this->expiryYear = $data["expiryYear"];
    }

    public function __toString() {
        return $this->getFullName() . "<br>" . 
            $this->getCardNumberHidden() . "<br>" .
            ($this->isExpired() ? "Expired: " : "Expiry: ") .
            $this->getExpiry();
    }

    /**
     * @return int The card ID
     */
    public function getID() {
        return $this->id;
    }

    /**
     * @return string the full name on the card.
     */
    public function getFullName() {
        return htmlspecialchars($this->fullName);
    }

    /**
     * @return string 12 • and the last 4 digits of the card number
     */
    public function getCardNumberHidden() {
        return "•••• •••• •••• " . substr($this->cardNumber, -4);
    }

    /**
     * Get the expiry date in the format MM/YY
     */
    public function getExpiry() {
        return str_pad($this->expiryMonth, 2, "0", STR_PAD_LEFT) . "/" . 
            substr($this->expiryYear, -2);
    }

    /**
     * Check if the card is expired.
     *
     * @return bool True if the card is expired
     */
    public function isExpired() {
        $expiry = new DateTime($this->expiryYear . "-" . $this->expiryMonth . "-01");
        $now = new DateTime();
        return $expiry < $now;
    }

    /**
     * @return string The 16 digit card number
     */
    public function getCardNumber() {
        return $this->cardNumber;
    }

    /**
     * @return string The CCV/CVC
     */
    public function getSecurityCode() {
        return $this->securityCode;
    }

    /**
     * @return int The expiry month
     */
    public function getExpiryMonth() {
        return $this->expiryMonth;
    }

    /**
     * @return int The expiry year
     */
    public function getExpiryYear() {
        return $this->expiryYear;
    }

    /** 
     * Update the card.
     * 
     * @param array $data The data to update
     */
    public function update($data) {
        $stmt = $this->dbh->prepare(
            "UPDATE cards
            SET fullName = :name,
                cardNumber = :cardNumber,
                securityCode = :securityCode,
                expiryMonth = :expiryMonth,
                expiryYear = :expiryYear
            WHERE cardID = :cardID");
        $stmt->execute([
                "cardID" => $this->id,
                "name" => $data["name"],
                "cardNumber" => $data["cardNumber"],
                "securityCode" => $data["securityCode"],
                "expiryMonth" => $data["expiryMonth"],
                "expiryYear" => $data["expiryYear"]
        ]);
    }

    /**
     * Delete the card.
     */
    public function delete() {
        $stmt = $this->dbh->prepare(
            "DELETE FROM cards
            WHERE cardID = :cardID"
        );
        $stmt->execute([
            "cardID" => $this->id
        ]);
    }
}
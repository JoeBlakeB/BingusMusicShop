-- Copyright (c) 2022 JoeBlakeB, all rights reserved.

-- Create tables

CREATE TABLE IF NOT EXISTS accounts (
    accountID           INT UNSIGNED NOT NULL AUTO_INCREMENT,
    email               VARCHAR(256) NOT NULL,
    passwordHash        VARCHAR(60)  NOT NULL,
    twoFactorEnabled    BOOLEAN      NOT NULL DEFAULT FALSE,
    isAdmin             BOOLEAN      NOT NULL DEFAULT FALSE,
    fullName            VARCHAR(256),
    PRIMARY KEY (accountID),
    UNIQUE (email)
);

CREATE TABLE IF NOT EXISTS unverifiedAccounts (
    accountID        INT UNSIGNED NOT NULL AUTO_INCREMENT,
    verificationCode VARCHAR(6)   NOT NULL,
    expires          DATETIME     NOT NULL,
    PRIMARY KEY (accountID),
    FOREIGN KEY (accountID)
        REFERENCES accounts (accountID)
        ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS addresses (
    addressID       INT UNSIGNED NOT NULL AUTO_INCREMENT,
    accountID       INT UNSIGNED NOT NULL,
    fullName        VARCHAR(256) NOT NULL,
    addressLine1    VARCHAR(128) NOT NULL,
    addressLine2    VARCHAR(128),
    city            VARCHAR(64)  NOT NULL,
    county          VARCHAR(64)  NOT NULL,
    postcode        VARCHAR(8)   NOT NULL,
    country         VARCHAR(2)   NOT NULL,
    PRIMARY KEY (addressID),
    FOREIGN KEY (accountID)
        REFERENCES accounts (accountID)
        ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS cards (
    cardID          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    accountID       INT UNSIGNED NOT NULL,
    fullName        VARCHAR(256) NOT NULL,
    cardNumber      VARCHAR(16)  NOT NULL,
    securityCode    VARCHAR(3)   NOT NULL,
    expiryMonth     INT UNSIGNED NOT NULL,
    expiryYear      INT UNSIGNED NOT NULL,
    PRIMARY KEY (cardID),
    FOREIGN KEY (accountID)
        REFERENCES accounts (accountID)
        ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS orders (

);

CREATE TABLE IF NOT EXISTS orderItems (
    
);

CREATE TABLE IF NOT EXISTS products (
    productID   INT UNSIGNED NOT NULL AUTO_INCREMENT,
    name        VARCHAR(128) UNIQUE NOT NULL,
    description TEXT,
    price       DECIMAL(7,2) NOT NULL,
    stock       INT UNSIGNED NOT NULL,
    PRIMARY KEY (productID)
);

CREATE TABLE IF NOT EXISTS images (
    imageID     INT UNSIGNED NOT NULL AUTO_INCREMENT,
    productID   INT UNSIGNED NOT NULL,
    fileHash    VARCHAR(32) NOT NULL,
    fileType    VARCHAR(4) NOT NULL,
    PRIMARY KEY (imageID),
    FOREIGN KEY (productID)
        REFERENCES products (productID)
        ON DELETE CASCADE
);

-- Create admin user
-- Password is "Password123"
INSERT INTO accounts (email, passwordHash, isAdmin, fullName)
VALUES (
    "admin@example.com",
    "$2y$10$FhNu9AAHCHCi.dypJfbdQeb0Dnj3vsb5KwK.WwfdxUnQ7bexHNyo6",
    true, "Admin"
);

-- Delete tables
DROP TABLE IF EXISTS unverifiedAccounts;
DROP TABLE IF EXISTS accounts;
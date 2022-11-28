-- Copyright (c) 2022 JoeBlakeB, all rights reserved.

-- Create tables

CREATE TABLE IF NOT EXISTS accounts (
    accountID           INT UNSIGNED NOT NULL AUTO_INCREMENT,
    email               VARCHAR(255) NOT NULL,
    passwordHash        VARCHAR(60)  NOT NULL,
    twoFactorEnabled    BOOLEAN      NOT NULL DEFAULT FALSE,
    isAdmin             BOOLEAN      NOT NULL DEFAULT FALSE,
    fullName            VARCHAR(255),
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
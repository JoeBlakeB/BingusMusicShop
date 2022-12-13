<?php

/**
 * The base class with methods in all models.
 * 
 * @author Joe Baker
 * @copyright Copyright (c) 2022 JoeBlakeB, all rights reserved.
 */

abstract class AbstractModel {
    protected $dbh;

    /**
     * Connect to the MySQL Server with PDO
     * 
     * Uses the credentials stored in the databaseCredentials.json file
     * which should be in the model directory.
     * Example file:
     * {
     *   "database": "",
     *   "hostname": "",
     *   "username": "",
     *   "password": "",
     *   "port":     0
     * }
     */
    public function __construct() {
        $filename = "model/databaseCredentials.json";
        $file = file_get_contents($filename, true);
        $credentials = json_decode($file, true);
        $dsn = "mysql:host=" . $credentials["hostname"] . ";dbname=" . $credentials["database"] . ";port=" . $credentials["port"];
        try {
            $this->dbh = new PDO(
                $dsn,
                $credentials["username"],
                $credentials["password"],
                array(
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
                    PDO::MYSQL_ATTR_SSL_CAPATH => "/public_html",
                    PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => 0
                )
            );
        }
        catch (PDOException $e) {
            global $controller;
            $controller->showError(503, "Database Connection Failed", "We are unable to access the database. Please try again later.");
            header("Retry-After: 10");
            exit();
        }
    }

    /**
     * Close the database connection.
     */
    public function __destruct() {
        $this->db = null;
    }

    /**
     * Create an array of objects from an array from the database.
     * 
     * @param array $items The items from the database.
     * @return array The items as objects.
     */
    public function createObjectArray($items, $class) {
        $itemArray = [];
        foreach ($items as $item) {
            $itemArray[] = new $class($this->dbh, $item);
        }
        return $itemArray;
    }
}

interface ModelObjectInterface {
    /**
     * Take the output from the database and create an object.
     * 
     * @param dbh $dbh The database connection
     * @param array $data The row from the database.
     */
    public function __construct(&$dbh, $data);
}

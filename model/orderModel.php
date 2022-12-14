<?php

/**
 * The class for managing orders.
 * 
 * @author Joe Baker
 * @copyright Copyright (c) 2022 JoeBlakeB, all rights reserved.
 */

require_once("model/abstractModel.php");
require_once("model/accountModel.php");
require_once("model/productModel.php");

class OrderModel extends AbstractModel {
    /**
     * Get all orders.
     * 
     * @param int $accountID If only showing the orders for a specific account.
     * @return array The orders as an array of arrays.
     */
    public function getAllOrders($accountID = null) {
        $stmt = $this->getOrderStatement($accountID, null);
        $stmt->execute();
        $data = $stmt->fetchAll();
        $orders = [];
        foreach ($data as $order) {
            $orders[] = $this->createOrderArray($order);
        }
        return $orders;
    }

    /**
     * Get an order by ID.
     * 
     * @param int $accountID If not admin so users cant see others orders.
     * @param int $orderID The order ID.
     * @return array The order as an array.
     */
    public function getOrder($accountID, $orderID) {
        $stmt = $this->getOrderStatement($accountID, $orderID);
        $stmt->execute();
        $data = $stmt->fetch();
        if ($data == null) {
            return null;
        }
        return $this->createOrderArray($data);
    }

    /**
     * Create the statement for getting an order.
     * 
     * @param int $accountID The account ID (or null)
     * @param int $orderID The order ID (or null)
     * @return PDOStatement The statement.
     */
    private function getOrderStatement($accountID=null, $orderID=null) {
        $where = "";
        if (!empty($accountID)) {
            $where = "WHERE orders.accountID = :accountID ";
        }
        if (!empty($orderID)) {
            $where .= (!empty($accountID) ? "AND " : "WHERE ") .
                "orders.orderID = :orderID ";
        }
        $stmt = $this->dbh->prepare(
            "SELECT ANY_VALUE(orders.orderID) as orderID,
                ANY_VALUE(orders.orderDate) as orderDate,

                JSON_ARRAYAGG(products.productID) as productIDs,
                JSON_ARRAYAGG(products.name) as productNames,
                JSON_ARRAYAGG(orderItems.priceAtPurchase) as productPrices, 
                JSON_ARRAYAGG(orderItems.quantity) as productQuantities,

                ANY_VALUE(cards.cardID) as cardID,
                ANY_VALUE(cards.fullName) as cardFullName,
                ANY_VALUE(cards.cardNumber) as cardNumber,

                ANY_VALUE(addresses.addressID) as addressID,
                ANY_VALUE(addresses.fullName) as addressFullName,
                ANY_VALUE(addresses.addressLine1) as addressLine1,
                ANY_VALUE(addresses.addressLine2) as addressLine2,
                ANY_VALUE(addresses.city) as addressCity,
                ANY_VALUE(addresses.county) as addressCounty,
                ANY_VALUE(addresses.postcode) as addressPostcode,
                ANY_VALUE(addresses.country) as addressCountry,

                ANY_VALUE(accounts.accountID) as accountID,
                ANY_VALUE(accounts.fullName) as accountFullName,
                ANY_VALUE(accounts.email) as accountEmail
            FROM orders LEFT JOIN orderItems ON orders.orderID = orderItems.orderID
            LEFT JOIN products ON orderItems.productID = products.productID
            LEFT JOIN accounts ON orders.accountID = accounts.accountID
            LEFT JOIN cards ON orders.cardID = cards.cardID
            LEFT JOIN addresses ON orders.addressID = addresses.addressID
            $where
            GROUP BY orders.orderID
            ORDER BY orders.orderID DESC");
        if (!empty($accountID)) {
            $stmt->bindParam(":accountID", $accountID, PDO::PARAM_INT);
        }
        if (!empty($orderID)) {
            $stmt->bindParam(":orderID", $orderID, PDO::PARAM_INT);
        }
        return $stmt;
    }

    /**
     * Convert the array output from the database into an array
     * with objects for products, addresses and cards.
     * 
     * @param array $data The output from the database.
     * @return array The order as an array.
     */
    private function createOrderArray($data) {
        $order = [
            "orderID" => $data["orderID"],
            "orderDate" => $data["orderDate"]
        ];

        $order["products"] = [];
        $productIDs = json_decode($data["productIDs"]);
        $productNames = json_decode($data["productNames"]);
        $productPrices = json_decode($data["productPrices"]);
        $productQuantities = json_decode($data["productQuantities"]);

        for ($i = 0; $i < count($productIDs); $i++) {
            $order["products"][] = new Product($this->dbh, [
                "productID" =>  $productIDs[$i],
                "name" =>       $productNames[$i],
                "price" =>      $productPrices[$i],
                "quantity" =>   $productQuantities[$i]
            ]);
        }

        $order["address"] = new Address($this->dbh, [
            "addressID" =>      $data["addressID"],
            "fullName" =>       $data["addressFullName"],
            "addressLine1" =>   $data["addressLine1"],
            "addressLine2" =>   $data["addressLine2"],
            "city" =>           $data["addressCity"],
            "county" =>         $data["addressCounty"],
            "postcode" =>       $data["addressPostcode"],
            "country" =>        $data["addressCountry"]
        ]);

        $order["totalPrice"] = 0;
        foreach ($order["products"] as $product) {
            $order["totalPrice"] += $product->getPriceDouble() * $product->getQuantity();
        }

        $order["card"] = new Card($this->dbh, [
            "cardID" =>     $data["cardID"],
            "fullName" =>   $data["cardFullName"],
            "cardNumber" => $data["cardNumber"]
        ]);

        $order["account"] = [
            "accountID" =>  $data["accountID"],
            "fullName" =>   $data["accountFullName"],
            "email" =>      $data["accountEmail"]
        ];

        return $order;
    }

    /**
     * Order one type of product for a user with a card and address.
     * 
     * @param Account $account The account.
     * @param Product $product The product.
     * @param int $quantity The quantity of the product
     * @param Address $address The address to put on the order
     * @param Card $card The card to charge (not implemented).
     * @return int The order ID.
     */
    public function purchaseOneProduct($account, $product, $quantity, $address, $card) {
        $stockUpdated = $product->decreaseStock($quantity);
        if (!$stockUpdated) {
            return null;
        }
        $stmt = $this->dbh->prepare(
            "INSERT INTO orders (accountID, cardID, addressID, orderDate)
            VALUES (:accountID, :cardID, :addressID, NOW());
            INSERT INTO orderItems (orderID, productID, priceAtPurchase, quantity)
            VALUES (LAST_INSERT_ID(), :productID, :priceAtPurchase, :quantity)");
        $stmt->execute([
            ":accountID" =>         $account->getID(),
            ":cardID" =>            $card->getID(),
            ":addressID" =>         $address->getID(),
            ":productID" =>         $product->getID(),
            ":priceAtPurchase" =>   $product->getPriceDouble(),
            ":quantity" =>          $quantity
        ]);
        return $this->dbh->lastInsertId();
    }
}
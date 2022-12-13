<?php

/**
 * The orders view page used by the account and admin controllers.
 * 
 * @author Joe Baker
 * @copyright Copyright (c) 2022 JoeBlakeB, all rights reserved.
 */

require_once("model/orderModel.php");

trait OrdersTrait {
    abstract public function ordersPage();

    /**
     * Show the view all orders page
     * 
     * @param string $viewPath admin or account - where the view is located.
     * @param int $accountID If only showing the orders for a specific account.
     */
    public function ordersView($viewPath, $accountID = null) {
        $orderModel = new OrderModel();
        if (isset($_GET["orderID"])) {
            $orderID = $_GET["orderID"];
            $order = $orderModel->getOrder($accountID, $orderID);
            if ($order == null) {
                return $this->showError(404, "Order Not Found", "The order you are looking for could not be found.");
            }
            return require_once("$viewPath/orders/view.php");
        }

        try {
            $orders = $orderModel->getAllOrders($accountID);
        }
        catch (PDOException $e) {
            return $this->showError(500, "Error Getting Orders", "An error occurred while trying to get the orders.");
        }

        require_once("$viewPath/orders/all.php");
    }
}
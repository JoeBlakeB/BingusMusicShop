<?php

/**
 * Show the pages for the site control.
 * 
 * @author Joe Baker
 * @copyright Copyright (c) 2022 JoeBlakeB, all rights reserved.
 */

class AdminController extends AbstractController {
    /**
     * Decide which admin page to show.
     * If the user is not admin then show an error.
     * 
     * @param array $uri The path of the page.
     */
    public function invoke() {
        if (!isset($_SESSION["account"])) {
            return header("Location: /BingusMusicShop.php/account/signin");
        }
        if (!$_SESSION["account"]["isAdmin"]) {
            return $this->showError(403, "Forbidden", "You do not have permission to access this page.");
        }

        if (!isset($this->uri[1])) {
            return $this->overview();
        }
        else {
            $this->runPageMethod($this->uri[1]);
        }
        $this->pageNotFound();
    }

    /**
     * Show the admin details page.
     */
    public function overview() {
        require "admin/overview.php";
    }

    /**
     * Show the user management page.
     * - Verify, make admin, and delete users
     * - View specific user details
     * - View a list of all users
     */
    public function usersPage() {
        $this->maxPathLength(2, $this->uri);
        require "model/accountModel.php";
        $accountModel = new AccountModel();

        if (isset($_POST["action"]) && isset($_GET["accountID"])) {
            switch ($_POST["action"]) {
                case "Verify User":
                    $accountModel->verifyAccount($_GET["accountID"]);
                    $actionMessage = "Verified user #" . $_GET["accountID"];
                    break;
                case "Make Admin":
                    $accountModel->setAdmin($_GET["accountID"], true);
                    $actionMessage = "Made user #" . $_GET["accountID"] . " an admin.";
                    break;
                case "Remove Admin":
                    $accountModel->setAdmin($_GET["accountID"], false);
                    $actionMessage = "User #" . $_GET["accountID"] . " is no longer an admin.";
                    break;
                case "Delete User":
                    $accountModel->deleteAccount($_GET["accountID"]);
                    $actionMessage = "Deleted user #" . $_GET["accountID"];
                    unset($_GET["accountID"]);
                    break;
                default:
                    $actionMessage =  "Invalid action.";
                    http_response_code(400);
                    break;
            }
        }

        if (isset($_GET["accountID"])) {
            $account = $accountModel->getAccountByID($_GET["accountID"]);
            if ($account == null) {
                http_response_code(404);
            }
        }

        $accounts = $accountModel->getAllAccounts();
        require "admin/users.php";
    }
}


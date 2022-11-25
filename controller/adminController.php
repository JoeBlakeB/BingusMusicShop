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
            return header("Location: $this->basePath/account/signin");
        }
        if (!$_SESSION["account"]["isAdmin"]) {
            return $this->showError(403, "Forbidden", "You do not have permission to access this page.");
        }

        if (!isset($this->uri[1])) {
            return $this->overview();
        }
        $this->runPageMethod($this->uri[1]);
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
        if (isset($_GET["accountID"])) {
            $account = $accountModel->getAccountByID($_GET["accountID"]);
            if ($account == null) {
                http_response_code(404);
            }
        }

        try {
            if (isset($_POST["action"]) && isset($_GET["accountID"])) {
                switch ($_POST["action"]) {
                    case "Verify User":
                        $actionMessage = "Verified user #" . $account->getID();
                        $account->verify();
                        break;
                    case "Make Admin":
                        $actionMessage = "Made user #" . $account->getID() . " an admin.";
                        $account->setAdmin(true);
                        break;
                    case "Remove Admin":
                        $actionMessage = "User #" . $account->getID() . " is no longer an admin.";
                        $account->setAdmin(false);
                        break;
                    case "Delete User":
                        $actionMessage = "Deleted user #" . $account->getID();
                        $account->delete();
                        break;
                    default:
                        $actionMessage =  "Invalid action.";
                        http_response_code(400);
                        break;
                }
            }
        }
        catch (Exception $e) {
            $actionMessage = "An error occured while performing that action.";
            http_response_code(500);
        }

        $allAccounts = $accountModel->getAllAccounts();
        require "admin/users.php";
    }
}
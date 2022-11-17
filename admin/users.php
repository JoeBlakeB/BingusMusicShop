<?php
/**
 * List all users
 * 
 * @author Joe Baker
 * @copyright Copyright (c) 2022 JoeBlakeB, all rights reserved.
 */

$rootPath = "../";
set_include_path("{$rootPath}include");
session_start();

// Check if the user is an admin.
if (!isset($_SESSION["account"]) || !$_SESSION["account"]["isAdmin"]) {
    header("Location: {$rootPath}products/orders.php");
    die();
}

include "utils.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Users</title>
    <meta name="description" content="Bingus Music Shop Users Admin">
    <meta name="keywords" content="Bingus Music Shop, Users, Admin">
    <?php require "head.php"; ?>
</head>

<body>
    <?php require "header.php"; ?>

    <div class="basicContent">
    <h1>Users</h1>

    <a href="overview.php" class="button">
        <p>Back to Admin Overview</p>
    </a>

    <?php
    try {
        $dbh = sqlConnect();
    }
    catch (PDOException $e) {
        echo "Error connecting to database: " . $e->getMessage();
        die();
    }

    // Run update action on user from buttons
    if (isset($_POST["action"]) && isset($_GET["accountID"])) {
        try {
            switch ($_POST["action"]) {
                case "Verify User":
                    $stmt = $dbh->prepare("DELETE FROM unverifiedAccounts WHERE accountID = :accountID;");
                    echo "<p>Verified user #" . $_GET["accountID"] . ".</p>";
                    break;
                case "Make Admin":
                    $stmt = $dbh->prepare("UPDATE accounts SET isAdmin = 1 WHERE accountID = :accountID;");
                    echo "<p>User #" . $_GET["accountID"] . " is now an admin.</p>";
                    break;
                case "Remove Admin":
                    $stmt = $dbh->prepare("UPDATE accounts SET isAdmin = 0 WHERE accountID = :accountID;");
                    echo "<p>User #" . $_GET["accountID"] . " is no longer an admin.</p>";
                    break;
                case "Delete User":
                    $stmt = $dbh->prepare("DELETE FROM accounts WHERE accountID = :accountID;");
                    echo "<p>Deleted user #" . $_GET["accountID"] . ".</p>";
                    break;
                default:
                    $stmt = null;
                    echo "<p>Invalid action.</p>";
                    break;
            }
            if ($stmt) {
                $stmt->bindParam(":accountID", $_GET["accountID"]);
                $stmt->execute();
                if ($_POST["action"] == "Delete User") {
                    unset($_GET["accountID"]);
                }
            }
        }
        catch (PDOException $e) {
            echo "There was an error with completing that action: " . $e->getMessage();
            die();
        }
    }

    // Show specific user
    if (isset($_GET["accountID"])) {
        try {
            $stmt = $dbh->prepare("SELECT * FROM accounts
                LEFT JOIN unverifiedAccounts
                ON accounts.accountID = unverifiedAccounts.accountID
                WHERE accounts.accountID = :accountID");
            $stmt->bindParam(":accountID", $_GET["accountID"]);
            $stmt->execute();
            $account = $stmt->fetch();

            if ($account) {
                ?>
                <h2>Selected User: <?php echo htmlspecialchars($account["fullName"]); ?></h2>
                <p>Account ID: <?php echo $account[0]; ?></p>
                <p>Email: <?php echo htmlspecialchars($account["email"]); ?></p>
                <p>Admin: <?php echo $account["isAdmin"] ? "Yes" : "No"; ?></p>
                <p>Verified: <?php echo !isset($account["verificationCode"]) ? "Yes" : "No"; ?></p>
                <?php
                if (isset($account["verificationCode"])) {
                    ?>
                    <p>Verification Code: <?php echo $account["verificationCode"]; ?></p>
                    <?php
                }
                ?>

                <form action="users.php?accountID=<?php
                    echo $account[0];
                ?>" method="post">
                    <?php
                    if (isset($account["verificationCode"])) {
                        ?>
                        <input type="submit" name="action" value="Verify User">
                        <?php
                    }
                    if ($account["isAdmin"]) {
                        ?>
                        <input type="submit" name="action" value="Remove Admin">
                        <?php
                    }
                    else {
                        ?>
                        <input type="submit" name="action" value="Make Admin">
                        <?php
                    }
                    ?>
                    <input type="submit" name="action" value="Delete User">
                </form>
                <?php 
            }
            else {
                ?>
                <p>Account not found.</p>
                <?php
            }
        }
        catch (PDOException $e) {
            echo "Error getting user information: " . $e->getMessage();
        }
    }

    // Show a list of all users
    try {
        $stmt = $dbh->prepare("SELECT * FROM accounts
            LEFT JOIN unverifiedAccounts
            ON accounts.accountID = unverifiedAccounts.accountID
            ORDER BY isAdmin DESC, accounts.accountID");
        $stmt->execute();
        $accounts = $stmt->fetchAll();

        if ($accounts) {
            if (isset($_GET["accountID"])) {
                ?>
                <h2>All Users</h2>
                <?php
            }
            ?>
            <table>
                <tr class="headerRow">
                    <th>ID</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Admin</th>
                    <th>Verified</th>
                </tr>
                <?php
                foreach ($accounts as $account) {
                    ?>
                    <tr onclick="window.location.href='users.php?accountID=<?=$account[0];?>'">
                        <td><?=$account[0];?></td>
                        <td class="columnBig"><?=htmlspecialchars($account["fullName"]);?></td>
                        <td class="columnBig"><?=htmlspecialchars($account["email"]);?></td>
                        <td><?=$account["isAdmin"] ? "Yes" : "No";?></td>
                        <td><?=!isset($account["verificationCode"]) ? "Yes" : "No";?></td>
                    </tr>
                    <?php
                }
                ?>
            </table>
            <?php
        }
    }
    catch (PDOException $e) {
        echo "Error getting list of users: " . $e->getMessage();
    }
    
    ?>
    </div>
</body>

</html>

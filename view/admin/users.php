<!DOCTYPE html>
<html lang="en">

<head>
    <?php
    $title = "Users - Admin";
    require "head.php";
    ?>
</head>

<body>
    <?php require "header.php"; ?>

    <div class="basicContent">
        <h1>Admin - Users</h1>

        <a href="." class="button">
            <p>Back to Admin Overview</p>
        </a>

        <?php
        if (isset($actionMessage)) {
            echo "<p>$actionMessage</p>";
        }

        if (isset($account) && !empty($account)) {
        ?>
            <h2>Selected User: <?php echo htmlspecialchars($account["fullName"]); ?></h2>
            <p>Account ID: <?= $account["accountID"]; ?></p>
            <p>Email: <?= htmlspecialchars($account["email"]); ?></p>
            <p>Admin: <?= $account["isAdmin"] ? "Yes" : "No"; ?></p>
            <p>Verified: <?= !isset($account["verificationCode"]) ? "Yes" : "No"; ?></p>

            <?php if (isset($account["verificationCode"])) { ?>
                <p>Verification Code: <?= $account["verificationCode"]; ?></p>
            <?php } ?>

            <form action="users?accountID=<?= $account["accountID"]; ?>" method="post">
                <?php if (isset($account["verificationCode"])) { ?>
                    <input type="submit" name="action" value="Verify User">
                <?php }
                if ($account["isAdmin"]) { ?>
                    <input type="submit" name="action" value="Remove Admin">
                <?php } else { ?>
                    <input type="submit" name="action" value="Make Admin">
                <?php } ?>
                <input type="submit" name="action" value="Delete User">
            </form>
        <?php } else if (isset($account)) {
            echo "<p>Account not found.</p>";
        } ?>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Admin</th>
                    <th>Verified</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($accounts as $account) { ?>
                    <tr onclick="window.location.href='<?= $this->basePath ?>/admin/users?accountID=<?= $account["accountID"] ?>'">
                        <td><?= $account["accountID"]; ?></td>
                        <td class="big"><?= htmlspecialchars($account["fullName"]); ?></td>
                        <td class="big"><?= htmlspecialchars($account["email"]); ?></td>
                        <td><?= $account["isAdmin"] ? "Yes" : "No"; ?></td>
                        <td><?= !isset($account["verificationCode"]) ? "Yes" : "No"; ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>

</html>
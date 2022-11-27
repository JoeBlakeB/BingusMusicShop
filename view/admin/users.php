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

        <p><a href=".." class="button">
            Back to Admin Overview
        </a></p>

        <?php
        if (isset($actionMessage)) {
            echo "<p>$actionMessage</p>";
        }

        if (isset($account)) {
        ?>
            <h2>Selected User: <?= $account->getFullName(); ?></h2>
            <p>Account ID: <?= $account->getID(); ?></p>
            <p>Email: <?= $account->getEmail(); ?></p>
            <p>Admin: <?= $account->getIsAdmin() ? "Yes" : "No"; ?></p>
            <?php $unverified = $account->getIsUnverified(); ?>
            <p>Verified: <?= $unverified ? "No" : "Yes"; ?></p>

            <?php if ($unverified) { ?>
                <p>Verification Code: <?= $unverified["code"]; ?></p>
                <p>Expires: <?= $unverified["expires"]; ?></p>
            <?php } ?>

            <form action="users?accountID=<?= $account->getID(); ?>" method="post">
                <?php if ($unverified) { ?>
                    <input type="submit" name="action" value="Verify User">
                <?php }
                if ($account->getIsAdmin()) { ?>
                    <input type="submit" name="action" value="Remove Admin">
                <?php } else { ?>
                    <input type="submit" name="action" value="Make Admin">
                <?php } ?>
                <input type="submit" name="action" value="Delete User">
            </form>
            <br>
        <?php } else if (isset($account) && is_null($account->getID())) {
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
                <?php foreach ($allAccounts as $account) { ?>
                    <tr onclick="window.location.href='<?= $this->basePath ?>/admin/users?accountID=<?= $account->getID(); ?>'">
                        <td><?= $account->getID(); ?></td>
                        <td class="big"><?= $account->getFullName(); ?></td>
                        <td class="big"><?= $account->getEmail(); ?></td>
                        <td><?= $account->getIsAdmin() ? "Yes" : "No"; ?></td>
                        <td><?= $account->getIsUnverified() ? "No" : "Yes"; ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>

</html>
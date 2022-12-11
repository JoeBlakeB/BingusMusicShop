<!DOCTYPE html>
<html lang="en">

<head>
    <?php
    $title = "Account Security";
    require "include/head.php";
    $twoFactorEnabled = $account->getIsTwoFactorEnabled();

    if (!isset($valid)) {
        $valid = [
            "currentPassword" => true,
            "newPassword" => true,
            "confirmPassword" => true
        ];
    }

    if (isset($success)) {
        unset($_POST);
    }
    ?>
</head>

<body>
    <?php require "include/header.php"; ?>
    <div class="content">
        <h1>Account Security</h1>
        <p><a class="button" href=".">Back to Account</a></p>
        <h2>Two Factor Authentication</h2>
        <p>
            Recieve an email with a code to enter when signing in.
            You currently have Two Factor Authentication
            <strong class="twoFactor<?= $twoFactorEnabled ? "Enabled" : "Disabled"; ?>"><?= $twoFactorEnabled ? "Enabled" : "Disabled"; ?></strong>.
        </p>
        <p><a class="button" href="security?2fa=<?= $twoFactorEnabled ? "disable" : "enable"; ?>">
                <?= $twoFactorEnabled ? "Disable" : "Enable"; ?> Two Factor Authentication
            </a></p>
        <h2>Change Password</h2>

        <?php if (isset($success)) { ?>
            <div class="success">
                <p><?= $success; ?></p>
            </div>
        <?php } ?>
    </div>

    <form action="security" method="post" class="editForm basicResponsiveForm passwordEditForm">
        <div class="inputContainer <?= $valid["currentPassword"] ? "" : "error"; ?>" id="currentPasswordContainer">
            <label for="currentPassword">Current Password:</label>
            <input type="password" name="currentPassword" id="currentPassword" maxlength="256" required>
            <p><?= $valid["currentPassword"] ? "" : "Your current password is incorrect."; ?></p>
        </div>
        <div class="inputContainer <?= $valid["newPassword"] ? "" : "error"; ?>" id="passwordContainer">
            <label for="newPassword">New Password:</label>
            <input type="password" name="newPassword" id="newPassword" maxlength="256" value="<?= isset($_POST["newPassword"]) ? $_POST["newPassword"] : ""; ?>" required>
            <p><?= $valid["newPassword"] ? "" : "Your new password is not valid."; ?></p>
        </div>
        <div class="inputContainer <?= $valid["confirmPassword"] ? "" : "error"; ?>" id="passwordConfContainer">
            <label for="confirmPassword">Confirm New Password:</label>
            <input type="password" name="confirmPassword" id="confirmPassword" maxlength="256" value="<?= isset($_POST["confirmPassword"]) ? $_POST["confirmPassword"] : ""; ?>" required>
            <p><?= $valid["confirmPassword"] ? "" : "Your passwords do not match."; ?></p>
        </div>
        <input type="submit" value="Change Password" formtype="changePassword" id="submitButton">
    </form>

    <script src="/static/scripts/signinValidation.js"></script>
</body>

</html>
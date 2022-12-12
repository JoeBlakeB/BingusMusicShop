<footer>
    <div id="sitemap">
        <div>
            <h3>Navigation</h3>
            <p><a href="<?= $this->basePath; ?>/">Home</a></p>
            <p><a href="<?= $this->basePath; ?>/search?order=newest">New Products</a></p>
        </div>
        <div>
            <h3>Account</h3>
            <?php if (isset($_SESSION["account"])) { ?>
                <p><a href="<?= $this->basePath; ?>/account">Account</a></p>
                <p><a href="<?= $this->basePath; ?>/account/signout">Sign-Out</a></p>
            <?php } else { ?>
                <p><a href="<?= $this->basePath; ?>/account/signin">Sign-In</a></p>
                <p><a href="<?= $this->basePath; ?>/account/register">Register</a></p>
            <?php } ?>
        </div>
        <?php if (isset($_SESSION["account"]) && $_SESSION["account"]["isAdmin"]) { ?>
            <div>
                <h3>Admin</h3>
                <p><a href="<?= $this->basePath; ?>/admin/products">Products</a></p>
                <p><a href="<?= $this->basePath; ?>/admin/users">Users</a></p>
            </div>
        <?php } ?>
    </div>
    <p>
        Bingus Music Shop &copy; 2022 Joe Baker <br>
        (JoeBlakeB <a href="https://joeblakeb.com/"><i class="fas fa-link"></i></a>)
        all rights reserved.
    </p>
</footer>
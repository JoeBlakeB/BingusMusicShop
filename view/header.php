<header>
    <div id="headerName">
        <a href="<?= $this->basePath ?>">
            <img height="64px" src="/static/images/Logo.png" alt="Logo">
            <h1>Bingus Music Shop</h1>
        </a>
    </div>
    <div id="headerSearch">
        <input type="text" placeholder="Search..." value="<?php 
        if (isset($_GET['search'])) {
            echo $_GET['search'];
        }
        ?>"><button type="submit"><i class="fa fa-search"></i></button>
    </div>
    <button onclick="document.querySelector('header').classList.toggle('showDropdown')" id="headerButtonsDropdown"><i class="fa-solid fa-bars"></i></button>
    <div id="headerButtons">
        <a href="<?= "$this->basePath/account/" . 
            (isset($_SESSION["account"]) ? "" : "signin"); ?>">
            <div>
                <h3><?= (
                    isset($_SESSION["account"]) ?
                    "Account" : "Sign In"); ?></h3>
                <p><?= (
                    isset($_SESSION["account"]) ?
                    $_SESSION["account"]["fullName"] :
                    "or Register"); ?></p>
            </div>
        </a>
        <a href="#">
        <a href="<?= $this->basePath .
            ((isset($_SESSION["account"]) && $_SESSION["account"]["isAdmin"]) ?
                "/admin" : "/products/orders"); ?>">
            <div class="headerButtonCenter">
                <h3>
                    <?= (isset($_SESSION["account"]) && $_SESSION["account"]["isAdmin"]) ?
                        "Admin" : "Orders"; ?></h3>
            </div>
        </a>
    </div>
</header>
<header>
    <div id="headerName">
        <a href="<?= $this->basePath ?>">
            <img height="64px" src="/static/images/Logo.png" alt="Logo">
            <h1>Bingus Music Shop</h1>
        </a>
    </div>
    <div id="headerSearch">
        <input type="text" id="searchBar" placeholder="Search..." value="<?php 
        if (isset($searchTerm)) {
            echo $searchTerm;
        }
        ?>"><button id="searchButton" type="submit"><i class="fa fa-search"></i></button>
    </div>
    <button id="headerButtonsDropdown" <?php
        if (isset($_SESSION["account"]) && $_SESSION["account"]["isAdmin"]) {
            echo "onclick=\"document.querySelector('header').classList.toggle('showDropdown')\"><i class=\"fa-solid fa-bars\"";
        }
        else {
            echo "onclick=\"window.location.href = '$this->basePath/account'\"><i class=\"fa-solid fa-user\"";
        }
    ?>></i></button>
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
        <?php if (isset($_SESSION["account"]) && $_SESSION["account"]["isAdmin"]) { ?>
        <a href="<?= $this->basePath; ?>/admin">
            <div class="headerButtonCenter">
                <h3>Admin</h3>
            </div>
        </a>
        <?php } ?>
    </div>
    <script> var basePath = "<?= $this->basePath; ?>"; </script>
    <script src="/static/scripts/searchbar.js"></script>
</header>
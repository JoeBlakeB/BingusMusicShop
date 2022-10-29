<?php
/**
 * The header for most pages on the website.
 * 
 * @author Joe Baker
 * @copyright Copyright (c) 2022 JoeBlakeB, all rights reserved.
 */
?>

<header>
    <div id="headerName">
        <img height="64px" src="<?php echo $rootPath; ?>static/images/Logo.png" alt="Logo">
        <h1>Bingus Music Shop</h1>
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
        <a href="<?php echo (false) ? "#" : $rootPath . "account/signin.php"; ?>">
            <div>
                <h3>
                    <?php echo (false) ? "Admin" : "Account"; ?>
                </h3>
                <p>
                    <?php echo (false) ? "Username" : "Sign In"; ?>
                </p>
            </div>
        </a>
        <a href="#">
            <div class="headerButtonCenter">
                <h3>Orders</h3>
            </div>
        </a>
    </div>
</header>
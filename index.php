<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Bingus Music Shop</title>
    <meta name="author" content="Joe Baker">
    <meta name="description" content="Bingus Music Shop Homepage">
    <meta name="keywords" content="Bingus Music Shop Homepage">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="static/styles/main.css">
    <link rel="stylesheet" href="static/fontawesome/fontawesome.6.2.0.css">
    <link rel="icon" type="image/x-icon" href="static/images/favicon.ico">
</head>

<body>
    <header>
        <div id="headerName">
            <img height="64px" src="static/images/Logo.png" alt="Logo">
            <h1>Bingus Music Shop</h1>
        </div>
        <div id="headerSearch">
            <input type="text" placeholder="Search..."><button type="submit"><i class="fa fa-search"></i></button>
        </div>
        <button onclick="document.querySelector('header').classList.toggle('showDropdown')" id="headerButtonsDropdown"><i class="fa-solid fa-bars"></i></button>
        <div id="headerButtons">
            <a href="#"><div>
                <h3>
                    <?php echo (false) ? "Admin" : "Account"; ?>
                </h3>
                <p>
                    <?php echo (false) ? "Username" : "Sign In"; ?>
                </p>
            </div></a>
            <a href="#"><div class="headerButtonCenter">
                <h3>Orders</h3>
            </div></a>
        </div>
    </header>
</body>

</html>
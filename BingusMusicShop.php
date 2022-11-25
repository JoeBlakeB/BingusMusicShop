<?php

/**
 * Invoke the MVC controller
 * 
 * @author Joe Baker
 * @copyright Copyright (c) 2022 JoeBlakeB, all rights reserved.
 */

require "controller/main.php";
$controller = new Controller("/BingusMusicShop.php");
$controller->invoke();

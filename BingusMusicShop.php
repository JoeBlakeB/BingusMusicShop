<?php

/**
 * Redirect if URL is not all lowercase,
 * then invoke the MVC controller
 * 
 * @author Joe Baker
 * @copyright Copyright (c) 2022 JoeBlakeB, all rights reserved.
 */

require "controller/main.php";
$controller = new Controller();
$controller->invoke();

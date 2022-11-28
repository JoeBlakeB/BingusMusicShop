<?php

/**
 * Show the home page.
 * 
 * @author Joe Baker
 * @copyright Copyright (c) 2022 JoeBlakeB, all rights reserved.
 */

class HomeController extends AbstractController {
    public function invoke() {
        if (empty($this->uri)) {
            require "view/home.php";
        }
        else {
            $this->showError(404, "Page Not Found", "The page you requested could not be found.");
        }
    }
}

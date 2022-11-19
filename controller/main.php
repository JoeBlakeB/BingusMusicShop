<?php

/**
 * Select which controller to use based on the URI.
 * 
 * @author Joe Baker
 * @copyright Copyright (c) 2022 JoeBlakeB, all rights reserved.
 */

abstract class AbstractController {
    abstract public function invoke($uri);

    public function showError($errorCode, $errorTitle, $errorMessage) {
        http_response_code($errorCode);
        require "view/error.php";
    }
}

class Controller extends AbstractController {
    /**
     * Decide which controller to use and invoke it or show an error.
     */
    public function invoke($uri=null) {
        $uri = $this->getUri();
        try {
            $controller = isset($uri[0]) ? $uri[0] : "home";
            $controller = $controller . "Controller";
            if (file_exists("controller/$controller.php")) {
                require "controller/$controller.php";
                $controller = new $controller();
                $controller->invoke($uri);
            }
            else {
                $this->showError(404, "Page Not Found", "The page you requested could not be found.");
            }
        }
        catch (Throwable $e) {
            $this->showError(500, "Internal Server Error", $e->getMessage());
        }
    }

    /**
     * Get the URI after the base URL.
     * 
     * @return array The URI
     */
    public function getUri() {
        $uri = $_SERVER['REQUEST_URI'];
        $pos = strpos($uri, "BingusMusicShop.php");
        $uri = substr($uri, $pos + 20);
        $uri = explode("/", $uri);
        $uri = array_values(array_diff($uri, [""]));
        return $uri;
    }
}

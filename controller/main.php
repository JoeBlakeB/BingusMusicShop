<?php

/**
 * Select which controller to use based on the URI.
 * 
 * @author Joe Baker
 * @copyright Copyright (c) 2022 JoeBlakeB, all rights reserved.
 */

abstract class AbstractController {
    public $uri;

    /**
     * Create a new controller.
     * 
     * @param array $uri The URI of the page.
     */
    public function __construct($uri) {
        $this->uri = $uri;
    }

    /**
     * The method ran when the controller is called by the main controller.
     */
    abstract public function invoke();

    /**
     * Show an error page.
     * 
     * @param int $code The error code
     * @param string $title The error title
     * @param string $message The error message
     */
    public static function showError($errorCode, $errorTitle, $errorMessage) {
        http_response_code($errorCode);
        require "view/error.php";
    }

    /**
     * 404 page not found, just runs showError with specific message.
     */
    public static function pageNotFound() {
        self::showError(404, "Page Not Found", "The page you requested could not be found.");
    }

    /**
     * Run a pages method if it exists.
     * For example, for the page "account/signin",
     * the method would be "signinPage".
     * 
     * @param string $name The name of the page
     * @param string $suffix The suffix of the method, default is "Page"
     */
    public function runPageMethod($name, $suffix = "Page") {
        $pageMethod = $name . $suffix;
        if (method_exists($this, $pageMethod)) {
            exit($this->$pageMethod());
        }
    }

    /**
     * Check if the URI is too long and 404 if it is.
     * 
     * @param int $max The maximum number of sub directories
     */
    public function maxPathLength($max) {
        if (count($this->uri) > $max) {
            exit($this->pageNotFound());
        }
    }
}

class Controller extends AbstractController {
    public function __construct() {
        set_include_path("view");
        session_start();
    }

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
                $controller = new $controller($uri);
                $controller->invoke();
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
        $uri = strstr($uri, "?", true) ?: $uri;
        $uri = explode("/", $uri);
        $uri = array_values(array_diff($uri, [""]));
        return $uri;
    }
}

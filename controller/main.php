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
     * @param string $basePath The base path of the page.
     */
    public function __construct($uri, $basePath) {
        $this->uri = $uri;
        $this->basePath = $basePath;
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
     * @param string $linkHref The link href - optional
     * @param string $linkText The link text - optional
     */
    public function showError($errorCode, $errorTitle, $errorMessage, $errorLinkHref = null, $errorLinkText = null) {
        http_response_code($errorCode);
        exit(require_once("view/error.php"));
    }

    /**
     * 404 page not found, just runs showError with specific message.
     */
    public function pageNotFound() {
        $this->showError(404, "Page Not Found", "The page you requested could not be found.");
    }

    /**
     * Run a pages method if it exists.
     * For example, for the page "account/signin",
     * the method would be "signinPage".
     * 404 if the method does not exist.
     * 
     * @param string $name The name of the page
     * @param string $suffix The suffix of the method, default is "Page"
     */
    public function runPageMethod($name, $suffix = "Page") {
        $pageMethod = $name . $suffix;
        if (method_exists($this, $pageMethod)) {
            return $this->$pageMethod();
        }
        $this->pageNotFound();
    }

    /**
     * Check if the URI is too long or too short and 404 if it is.
     * 
     * @param int $max The maximum number of sub directories
     * @param int $min The minimum number of sub directories (optional)
     */
    public function maxPathLength($max, $min = 0) {
        if (count($this->uri) > $max || count($this->uri) < $min) {
            exit($this->pageNotFound());
        }
    }

    /**
     * Respond with a JSON
     * 
     * @param array $data The data to send
     * @param int $http The http response code
     */
    public function respondWithJson($data, $http = 200) {
        header("Content-Type: application/json; charset=utf-8");
        http_response_code($http);
        echo json_encode($data);
        exit();
    }
}

class Controller extends AbstractController {
    /**
     * Set variables and start session.
     * 
     * @param string $basePath The php file at the root of the site.
     */
    public function __construct($basePath) {
        $this->basePath = $basePath;
        set_include_path("view");
        session_name("BingusMusicShopID");
        session_start();
    }

    /**
     * Decide which controller to use and invoke it or show an error.
     * 
     * @param array $uri The URI of the page.
     */
    public function invoke() {
        $uri = $this->getUri();
        try {
            $controller = isset($uri[0]) ? $uri[0] : "home";
            $controller = $controller . "Controller";
            if (file_exists("controller/$controller.php")) {
                require_once("controller/$controller.php");
                $controller = new $controller($uri, $this->basePath);
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
        $uri = $_SERVER["REQUEST_URI"];
        $pos = strpos($uri, $this->basePath);
        $uri = substr($uri, $pos + 20);
        $uri = strstr($uri, "?", true) ?: $uri;
        $uri = explode("/", $uri);
        $uri = array_values(array_diff($uri, [""]));
        return $uri;
    }
}

<?php
namespace Core;

/**
 * Smart Router
 * 
 * Maps incoming URL requests to application request handlers and view templates.
 */
// Prevent direct script access by verifying application root definition.
defined('ROOTPATH') OR die("Direct access denied.");

class Route {

    /**
     * Handle incoming client requests, parse URL parameters, validate security tokens, and dispatch routing.
     *
     * @return void
     */
    public function handle() {
        // Retrieve and sanitize the requested route segment from query parameters, defaulting to 'home'.
        $url = isset($_GET['url']) ? $_GET['url'] : "home";
        $url = trim($url, "/");
        $parts = explode("/", $url);
        
        // Determine whether the current request targets the backend administrative area or frontend.
        $isAdmin = ($parts[0] === 'admin');
        $folder = $isAdmin ? 'backend' : 'frontend';
        
        // Resolve the target page name based on the application context.
        $page = $isAdmin ? ($parts[1] ?? 'dashboard') : $parts[0];
        
        // Process POST requests by verifying CSRF tokens and executing request controller logic.
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['csrf_token'] ?? '';
            
            // Validate the CSRF token security parameter.
            if (!validateCsrfToken($token)) {
                Session::flash("error", "Security Alert: Invalid or expired CSRF token. Please refresh the page and try again.");
                Response::back();
            }

            // Load and execute corresponding request controller logic upon successful validation.
            $this->loadCatch($page, $isAdmin);
        }

        // Render the corresponding template layout and view file.
        $this->loadGaze($page, $folder);
    }

    /**
     * Load and execute the corresponding request handler file for POST submissions.
     *
     * @param string $page Target page name
     * @param bool $isAdmin Flag indicating administrative context
     * @return void
     */
    private function loadCatch($page, $isAdmin) {
        $basePath = ROOTPATH; // Powered by framework root constant
        
        // Determine the target subdirectory based on administrative context.
        $subFolder = $isAdmin ? "admin/" : "auth/";
        
        // Construct target request filename with standard extension.
        $fileName = strtolower($page) . ".catch.php"; 
        
        $path = $basePath . "app/Requests/" . $subFolder . $fileName;
    
        if (file_exists($path)) {
            require $path;
        } 
    }

    /**
     * Load and render the template layout and view file for the requested page.
     *
     * @param string $page Target page name
     * @param string $folder Section folder identifier ('backend' or 'frontend')
     * @return void
     */
    private function loadGaze($page, $folder) {
        $basePath = ROOTPATH; // Powered by framework root constant
        
        // Resolve the target section path for views.
        $sectionPath = $basePath . "views/" . $folder; 
        
        $layoutPath = $sectionPath . "/layout.gaze.php"; 
        $viewPath = $sectionPath . "/" . $page . ".gaze.php";    
    
        if (file_exists($viewPath) && file_exists($layoutPath)) {
            // Expose context variables for template consumption.
            $page_name = $page;
            $view_folder = $folder;
            
            require $layoutPath; // Render section layout template
        } else {
            // Fallback to global 404 error view if the target view is missing.
            $errorPath = $basePath . "views/404.gaze.php";
             
            if (file_exists($errorPath)) {
                require $errorPath;
            } else {
                die("Error 404: Page not found.");
            }
        }
    }
}
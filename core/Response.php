<?php
namespace Core;

/**
 * Response & Navigation Handler
 * 
 * Manages HTTP redirects, API JSON responses, page refreshes, and navigation routing utilities.
 */
// Prevent direct script access by verifying application root definition.
defined('ROOTPATH') OR die("Direct access denied.");

class Response {

    /**
     * Redirect the client to a specified URL path with automatic environment and base routing resolution.
     *
     * @param string $path Target path or route to redirect toward
     * @param string $type Application context type ('frontend' or 'backend')
     * @return void
     */
    public static function redirect($path, $type = 'frontend') {
        $base = ($type === 'backend' || strpos($_GET['url'] ?? '', 'admin') === 0) ? AROOT : UROOT;
        
        // Ensure path normalization prevents double-slash concatenation.
        $path = ltrim($path, '/');
        
        header("Location: " . $base . "/" . $path);
        exit;
    }

    /**
     * Redirect the client back to the previous requesting page or fallback to the root URL.
     *
     * @return void
     */
    public static function back() {
        if (!empty($_SERVER['HTTP_REFERER'])) {
            header("Location: " . $_SERVER['HTTP_REFERER']);
        } else {
            // Fallback to application root if HTTP_REFERER is absent.
            header("Location: " . UROOT);
        }
        exit;
    }

    /**
     * Send a JSON-encoded HTTP response with a given status code and terminate execution.
     *
     * @param mixed $data Data payload to encode into JSON
     * @param int $code HTTP response status code, defaulting to 200
     * @return void
     */
    public static function json($data, $code = 200) {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
        exit;
    }

    /**
     * Refresh the current browser page immediately.
     *
     * @return void
     */
    public static function refresh() {
        header("Refresh:0");
        exit;
    }
}
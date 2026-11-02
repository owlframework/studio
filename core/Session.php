<?php
namespace Core;

/**
 * Session Manager
 * 
 * Handles user state persistence, authentication tracking, and temporary flash messages.
 */
// Prevent direct script access by verifying application root definition.
defined('ROOTPATH') OR die("Direct access denied.");

class Session {

    /**
     * Store a value in the session under a specified key.
     *
     * @param string $key Session storage key
     * @param mixed $value Value to store
     * @return void
     */
    public static function set($key, $value) {
        $_SESSION[$key] = $value;
    }

    /**
     * Retrieve a value from the session by key, with an optional default fallback.
     *
     * @param string $key Session storage key
     * @param mixed|null $default Default value to return if the key does not exist
     * @return mixed
     */
    public static function get($key, $default = null) {
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Determine whether a specific key exists in the current session.
     *
     * @param string $key Session storage key to check
     * @return bool Returns true if the key exists, false otherwise
     */
    public static function has($key) {
        return isset($_SESSION[$key]);
    }

    /**
     * Set a temporary flash message or retrieve and clear it for the next request cycle.
     *
     * @param string $key Flash message identifier
     * @param mixed|null $value Flash message content to set, or null to retrieve and clear
     * @return mixed Returns message value when fetching, or void when setting
     */
    public static function flash($key, $value = null) {
        if ($value !== null) {
            // Store the flash message value for single-request persistence.
            $_SESSION['_flash'][$key] = $value;
        } else {
            // Retrieve and immediately clear the flash message to expire it.
            $message = $_SESSION['_flash'][$key] ?? null;
            unset($_SESSION['_flash'][$key]);
            return $message;
        }
    }

    /**
     * Remove a specific key and its data from the session storage.
     *
     * @param string $key Session key to unset
     * @return void
     */
    public static function unset($key) {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }

    /**
     * Destroy the entire session, clear session variables, and invalidate client cookies.
     *
     * @return void
     */
    public static function destroy() {
        // Clear all active session variables from runtime memory.
        $_SESSION = [];
    
        // Invalidate and delete the session tracking cookie from the client's browser.
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
    
        // Terminate the server-side session storage file.
        session_destroy();
    }    
}
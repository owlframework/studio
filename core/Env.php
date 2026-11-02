<?php
namespace Core;

/**
 * Environment configuration loader.
 *
 * Parses and loads environment variables from a .env file into superglobals and environment space.
 */
class Env {
    /**
     * Load environment variables from a file path.
     *
     * @param string $path Path to the environment configuration file
     * @return void
     */
    public static function load($path) {
        if (!file_exists($path) || !is_readable($path)) {
            return;
        }

        // Read the environment file into an array, ignoring empty lines.
        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            $line = trim($line);

            // Skip comment lines and lines lacking an assignment operator.
            if (empty($line) || strpos($line, '#') === 0 || strpos($line, '=') === false) {
                continue;
            }

            // Split the line into variable name and value at the first assignment operator.
            list($name, $value) = explode('=', $line, 2);
            
            $name = trim($name);
            $value = trim($value);

            // Strip surrounding single or double quotes from the value.
            if (preg_match('/\A([\'"])(.*)\1\z/', $value, $matches)) {
                $value = $matches[2];
            }

            // Populate environment superglobals for reliable application access.
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;

            // Set runtime environment variable via putenv if available.
            if (function_exists('putenv')) {
                @putenv("$name=$value");
            }
        }
    }

    /**
     * Retrieve an environment variable value with an optional default fallback.
     *
     * @param string $key Environment variable name
     * @param mixed|null $default Default value to return if the key does not exist
     * @return mixed
     */
    public static function get($key, $default = null) {
        return $_ENV[$key] ?? $_SERVER[$key] ?? $default;
    }
}
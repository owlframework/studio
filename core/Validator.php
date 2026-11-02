<?php
namespace Core;

/**
 * Input Validator
 * 
 * Manages form data validation rules, error tracking, and constraint verification.
 */
// Prevent direct script access by verifying application root definition.
defined('ROOTPATH') OR die("Direct access denied.");

class Validator {

    public static $errors = [];

    /**
     * Validate input data against a defined set of validation rules.
     *
     * @param array $data Input dataset to validate (e.g., $_POST)
     * @param array $rules Associative array mapping field names to rule strings
     * @return bool Returns true if validation passes with no errors, false otherwise
     */
    public static function validate($data, $rules) {
        self::$errors = []; // Clear previous validation errors

        foreach ($rules as $field => $ruleset) {
            $ruleArray = explode('|', $ruleset);
            $value = $data[$field] ?? null;

            foreach ($ruleArray as $rule) {
                // Parse rule parameters if specified (e.g., min:8)
                $param = null;
                if (strpos($rule, ':') !== false) {
                    list($rule, $param) = explode(':', $rule);
                }

                // Execute individual rule constraint validations
                if ($rule === 'required' && empty($value)) {
                    self::$errors[$field] = ucfirst($field) . " is required.";
                } elseif ($rule === 'email' && !empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    self::$errors[$field] = "Please enter a valid email address.";
                } elseif ($rule === 'min' && !empty($value) && strlen($value) < $param) {
                    self::$errors[$field] = ucfirst($field) . " must be at least $param characters.";
                } elseif ($rule === 'matches' && $value !== ($data[$param] ?? null)) {
                    self::$errors[$field] = ucfirst($field) . " does not match " . $param . ".";
                }
            }
        }

        return empty(self::$errors);
    }

    /**
     * Retrieve all validation error messages generated during the last validation check.
     *
     * @return array Associative array of field-specific error messages
     */
    public static function getErrors() {
        return self::$errors;
    }
}
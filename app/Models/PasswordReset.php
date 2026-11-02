<?php
// Prevent direct script access by verifying application root definition.
defined('ROOTPATH') OR die("Direct access denied.");
class PasswordReset extends Model {
    // Specify the table
    protected $table = 'password_resets';

    // Critical: These must match the keys in your $_POST or data arrays
    protected $allowedColumns = [
        'email',
        'token',
        'used',
        'created_at'
    ];
}
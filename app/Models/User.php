<?php
// Prevent direct script access by verifying application root definition.
defined('ROOTPATH') OR die("Direct access denied.");
class User extends Model {
    protected $table = 'users';

    // Only allow fields I need to be inserted 
    protected $allowedColumns = [
        'username',
        'email',
        'phone',
        'password',
        'code',
        'status',
        'avatar',
        'email_verified_at'
    ];

}
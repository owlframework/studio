<?php
 // Prevent direct script access by verifying application root definition.
defined('ROOTPATH') OR die("Direct access denied.");

 class Login extends Model {
    protected $table = 'users';

    //only allow fields i need to be inserted 
    protected $allowedColumns = [
        'username',
        'email',
        'password',
        'role',
        'email_verified_at',
        'login_attempts', 
        'lockout_time'
    ];

 }
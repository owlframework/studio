<?php
 // Prevent direct script access by verifying application root definition.
defined('ROOTPATH') OR die("Direct access denied.");

 class Password extends Model {
    protected $table = 'users';

    //only allow fields i need to be inserted 
    protected $allowedColumns = [
        'username',
        'email',
        'password'
    ];

 }
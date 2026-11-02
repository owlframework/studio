<?php
// Prevent direct script access by verifying application root definition.
defined('ROOTPATH') OR die("Direct access denied.");

class Register extends Model {
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
    
    /**
     * Generates a unique 5-digit verification code
     * @param string $column The database column to check (e.g., 'code')
     * @return int
     */
    public function generateUniqueCode($column = 'code') {
        $exists = true;
        $code = 0;
    
        // Keep looping until we find a code that DOES NOT exist in the DB
        while ($exists) {
            // 1. Generate a random 5-digit number (10000 to 99999)
            $code = mt_rand(10000, 99999);
    
            // 2. Check the database using the Model's first() method
            $check = $this->first([$column => $code]);
    
            // 3. If no user is found, $check is false, and we can stop looping
            if (!$check) {
                $exists = false;
            }
        }
    
        return $code;
    }
}
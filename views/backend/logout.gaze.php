<?php
/**
* Owl Framework - Logout Request
* Clears the session and redirects to the home/login page.
*/
// 1. Destroy all session data (Clears USER_ID, CSRF, and Flash messages)
Session::destroy();
// 2. Optional: Start a fresh session if you want to show a "Goodbye" message
 
// 3. Redirect to the login page or home page
Response::redirect('login');
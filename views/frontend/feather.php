<?php
// views/frontend/feather.php
defined('ROOTPATH') OR die("Direct access denied.");

// 1. Enforce strict GET request discipline
verify_get_request();

// 2. Protect the page and get user data (Cached or lightweight check)
$access = user(true); 


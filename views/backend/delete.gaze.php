<?php
require 'feather.php';
$userModel = new User();
$id = $_GET['id'];

$deleteUser = $userModel->delete($id);
Response::redirect('users');
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require_once '../app/Core/AuthHelper.php';
require_once '../config/constants.php';
require_once '../app/Core/App.php';
require_once '../app/Core/Controller.php';
require_once '../app/Core/Database.php';
require_once '../app/Core/FlashHelper.php';

$app = new App();

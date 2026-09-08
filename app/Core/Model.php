<?php
require_once '../config/constants.php';
require_once '../app/Core/Database.php';
require_once '../app/Core/Controller.php';
require_once '../app/Core/FlashHelper.php';


class Model
{
    protected Database $db;

  public function __construct(?Database $db = null)
{
    $this->db = $db ?? new Database();
}
}
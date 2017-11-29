<?php
session_start();

define("DEBUG","true");
define("INIT","true");

require_once "db.php";
require_once "controller.php";
require_once "view.php";

View::render();
<?php
error_reporting(E_ERROR | E_PARSE);
require_once '../autoload.php';
use core\classes\Middleware;
use core\helpers\Env;
session_start();
Env::link('../../');

Middleware::getInstance()->getResponse()?->toJson();
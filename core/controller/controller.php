<?php
error_reporting(E_ERROR | E_PARSE);
require_once $_SERVER['DOCUMENT_ROOT'].'/core/autoload.php';
use core\classes\Middleware;
use core\helpers\Env;
session_start();
Env::link($_SERVER['DOCUMENT_ROOT'].'/');

Middleware::getInstance()->getResponse()?->toJson();
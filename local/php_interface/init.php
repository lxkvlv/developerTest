<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

define('CACHE_TIME', '3600');

use \Bitrix\Main\Loader;
Loader::registerAutoLoadClasses($module = null, [
  'DeveloperTest\\IB' => '/local/php_interface/Classes/DeveloperTest/IB.php',
]);
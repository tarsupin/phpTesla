<?php

// Make sure that the environment was set
if(!defined("ENVIRONMENT") or !ENVIRONMENT) { die("The ENVIRONMENT constant has not been set. You may need to create /teslaEngine/environment.php from /teslaEngine/environment-example.php"); }

// Report errors locally, but not on staging or production 
error_reporting(E_ALL);
ini_set("display_errors", ENVIRONMENT == "local" ? 1 : 0);

// Prepare the Auto-Loader
spl_autoload_register(null, false);
spl_autoload_extensions('.php');

// Create our custom Auto-Loader Function
function AutoLoader($class)
{
	$preClass = str_replace("_", "/", $class);
	
	// Search Application Classes
	if($classFile = realpath(APP_PATH . "/classes/$preClass/" . $class . ".php"))
	{
		require($classFile); return true;
	}
	
	// Search System Classes
	if($classFile = realpath(SYS_PATH . "/classes/$preClass/" . $class . ".php"))
	{
		require($classFile); return true;
	}
		
	// The class was not located. Return false.
	return false;
}

// Register our custom Auto-Loader
spl_autoload_register('AutoLoader');

// Prepare the URL and strip out any query string data (if used)
$url_relative = explode("?", rawurldecode($_SERVER['REQUEST_URI']));

// Sanitize any unsafe characters from the URL
$url_relative = trim(preg_replace("/[^a-zA-Z0-9_ \-\/\.\+\']/", "", $url_relative[0]), "/");

// Section the URL into multiple segments so that each can be added to the array individually
$url = explode("/", $url_relative);

/****** Session Handling ******/
session_name(SITE_HANDLE);
session_set_cookie_params(0, '/', '.' . $_SERVER['HTTP_HOST']);

session_start();

// Make sure the base session value used is available
if(!isset($_SESSION[SITE_HANDLE]))
{
	$_SESSION[SITE_HANDLE] = array();
}

/****** Process Security Functions ******/
Security_Fingerprint::run();

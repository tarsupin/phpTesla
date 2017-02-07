<?php

// Quick Debugging Display
function ezdie( ) {
	
	$args = func_get_args();
	
	foreach($args as $result) {
		
		if(is_string($result)) {
			if($json = json_decode($result, true)) {
				var_dump($json); exit;
				$result = json_encode($json, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
			}
		}
		else if(is_array($result)) {
			$result = json_encode($result, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
		}
		
		// Display
		echo '<pre>';
		echo stripslashes(htmlentities($result));
		echo '</pre>';
	}
	
	// End the page
	exit;
}

/****** Create Custom Error Handling ******/
function customErrorHandler($errorNumber, $errorString, $errorFile, $errorLine)
{
	// Prepare Values
	$errorType = "Error";
	
	switch($errorNumber)
	{
		case E_USER_NOTICE:		$errorType = "Notice";			$importance = 0;	break;
		case E_USER_WARNING:	$errorType = "Warning";			$importance = 2;	break;
		case E_USER_ERROR:		$errorType = "Fatal Error";		$importance = 4;	break;
		default:				$errorType = "Unknown Error";	$importance = 8;	break;
	}
	
	// Run the Backtrace
	$backtrace = debug_backtrace();
	
	if(isset($backtrace[1]))
	{
		// Prepare Backtrace Values
		$origin = $backtrace[1];
		$behind = $backtrace[0];
		
		// Identify the current URL
		$urlData = URL::parse($_SERVER['SERVER_NAME'] . "/" . $_SERVER['REQUEST_URI']);
		
		// If the error was triggered with trigger_error(), simplify the logging
		if($origin['function'] == "trigger_error")
		{
			// Prepare Logging Values
			$class = "";
			$function = "trigger_error";
			$argString = $origin['args'][0];
			$filePath = str_replace(SYS_PATH, "", $origin['file']);
			$fileLine = (int) $origin['line'];
			
			// Local Environment
			if(ENVIRONMENT == "local")
			{
				$cons = get_defined_constants(true);
				
				$debugData = array(
					"Backtrace"		=> array_splice($backtrace, 1)
				,	"URL"			=> $urlData
				,	"Constants"		=> $cons['user']
				,	"_GET"			=> $_GET
				,	"_POST"			=> $_POST
				,	"_COOKIE"		=> $_COOKIE
				,	"_SESSION"		=> $_SESSION
				,	"_SERVER"		=> $_SERVER
				);
			}
		}
		else
		{
			// Prepare Logging Values
			$class = isset($origin['class']) ? $origin['class'] : "";
			$function  = isset($origin['function']) ? $origin['function'] : "";
			$argString = isset($origin['args']) ? Data_Utilities::convertArrayToArgumentString($origin['args']) : "";
			$filePath = (isset($behind['file']) ? str_replace(dirname(SYS_PATH), "", $behind['file']) : '');
			$fileLine = (isset($behind['line']) ? $behind['line'] : 0);
			
			// Skip instances of the autoloader
			if($errorType == "Unknown Error" and strpos($function, "spl_autoload") !== false)
			{
				return false;
			}
		}
		
		// Debug files in the local environment
		if(ENVIRONMENT == "local")
		{
			if(!isset($urlData['path']))
			{
				$urlData['path'] = "home";
			}
			
			// Add an entry to the debug timeline
			File::write(SYS_PATH . "/debug/" . microtime(true) . "-" . str_replace("/", "_", $urlData['path']) . ".php", print_r((isset($debugData) ? $debugData : $backtrace), true));
			
			// Add an entry to the primary debug page
			File::prepend(SYS_PATH . "/debug/_primaryDebug.php", print_r(array("Domain" => strtolower($_SERVER['SERVER_NAME']), "URL" => $urlData['full'], "Error" => $errorType . ": " . $argString, "File" => "[Line " . $fileLine . "] " . $filePath, "Timestamp" => microtime(true)), true));
			
			// Add an entry to the debugging page
			File::prepend(SYS_PATH . "/debug/by-site/" . strtolower($_SERVER['SERVER_NAME']) . "/" . $urlData['path'] . ".php", print_r(array("URL" => $urlData['full'], "Error" => $errorType . ": " . $behind['args'][1], "File" => "[Line " . $fileLine . "] " . $filePath, "Timestamp" => microtime(true)), true));
			
			// Prune the debug pages so that they don't get overloaded
			File::prune(SYS_PATH . "/debug/_primaryDebug.php", 300);
			File::prune(SYS_PATH . "/debug/by-site/" . strtolower($_SERVER['SERVER_NAME']) . "/" . $urlData['path'] . ".php", 120);
			
			// Prune the timeline debug files so that they don't exhaust the system
			if(mt_rand(0, 25) == 22)
			{
				$debugFiles = Dir::getFiles(SYS_PATH . "/debug");
				
				foreach($debugFiles as $dbf)
				{
					if($dbf[0] != "_")
					{
						$exp = explode(".", $dbf);
						
						if($exp[0] < time() + 86400)
						{
							File::delete(SYS_PATH . "/debug/" . $dbf);
						}
						else
						{
							break;
						}
					}
				}
			}
		}
		
		// End the Error Handler
		return false;		// TRUE to run standard error logging afterward
	}
	
	// Returning FALSE will activate the default PHP Handler after ours runs.
	// Returning TRUE will prevent the default PHP Handler from running.
	return true;
}

// Register our custom error handler
set_error_handler("customErrorHandler");
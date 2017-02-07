<?php

/*
	Choose the appropriate environment for your server.
	
	By default, the engine provides three available environments:
		
		1. The "production" environment is your live server, where your final product is visible.
		2. The "staging" environment is your staging server, where you're testing it to be production-ready.
		3. The "local" environment is on your own personal computer.
*/

define("ENVIRONMENT", "local");

// Set a global salt used on this server
// Note: This is only one part of the salts used on your applications.
// It will be used for Cookies, Forms, etc - it does not permanently fix to anything (such as for passwords)
// Try to keep this value between 50 - 70 characters long
define("SERVER_SALT", "Set_a_server_salt_here_that_is_at_least_fifty_characters_long");
//						|    5   10   15   20   25   30   35   40   45   50   55   60   65   |

// DATABASE CONFIGURATIONS
// If you're configuring a database, set this block to true and update the values contained within.
if(false) {
	switch(ENVIRONMENT)
	{
		// Production Database Configurations
		case "production":
			
			define("DATABASE_USER", "example_db_user");
			define("DATABASE_PASS", "database_password");
			
			define("DATABASE_ADMIN_USER", "example_root_user");
			define("DATABASE_ADMIN_PASS", "database_password");
			
			define("DATABASE_HOST", "127.0.0.1");
			define("DATABASE_ENGINE", "mysql");
			
			break;
			
		// Development Database Configurations
		case "staging":
		case "development":
			
			define("DATABASE_USER", "example_db_user");
			define("DATABASE_PASS", "database_password");
			
			define("DATABASE_ADMIN_USER", "example_root_user");
			define("DATABASE_ADMIN_PASS", "database_password");
			
			define("DATABASE_HOST", "127.0.0.1");
			define("DATABASE_ENGINE", "mysql");
			
			break;
			
		// Production Database Configurations
		case "local":
		default:
			
			define("DATABASE_USER", "example_root_user");
			define("DATABASE_PASS", "database_password");
			
			define("DATABASE_ADMIN_USER", "example_root_user");
			define("DATABASE_ADMIN_PASS", "database_password");
			
			define("DATABASE_HOST", "127.0.0.1");
			define("DATABASE_ENGINE", "mysql");
			
			break;
	}
}
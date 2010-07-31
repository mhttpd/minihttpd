<?php
/**
 * This packed script is the first to be called by the non-console exe created by
 * phpack. It sets the initial environment variables, cleans up some phpack
 * settings, then calls the external server launch script.
 * 
 * The exe needs to be packed with these settings:
 *
 * - EXE with external DLL (using the full php5ts.dll from the 5.3.3 release)
 * - Use UPX Compression
 * - Windowed mode
 *
 * @link http://winbinder.org/forum/viewtopic.php?f=8&t=1148 phpack v0.7.5
 */
 
// Initialize the environment
$path = realpath("..\\..\\").DIRECTORY_SEPARATOR;
putenv('MHTTPD_ROOT='.$path);
$_ENV['MHTTPD_ROOT'] = $path;

// Cleanup GLOBALS
unset($GLOBALS['payload']);
unset($GLOBALS['PHPARCHIVE_TEMP_VAR']);

// Clean up the include paths
set_include_path(preg_replace('|pa://.*?;|', '', get_include_path()));

// Add local include paths
set_include_path(get_include_path()
	.PATH_SEPARATOR.$path.'lib'
	.PATH_SEPARATOR.$path.'lib\pear\classes'
);

// Change the working directory
chdir($path.'lib\minihttpd');

// Start the server
require 'launch.php';

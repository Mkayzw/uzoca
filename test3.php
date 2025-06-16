<?php
// Force error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Basic test
echo "PHP is working!";

// Test PHP configuration
echo "<br>PHP Version: " . phpversion();
echo "<br>PHP SAPI: " . php_sapi_name();
echo "<br>PHP ini location: " . php_ini_loaded_file();
?> 
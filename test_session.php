<?php
session_start();
error_log("Session test started");
error_log("Session ID: " . session_id());
error_log("Session data: " . print_r($_SESSION, true));

// Test setting a session variable
$_SESSION['test'] = 'Hello World';
error_log("Set test session variable");

// Test reading it back
error_log("Test session variable value: " . $_SESSION['test']);

echo "Session test completed. Check the error log for details.";
?> 
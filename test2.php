<?php
// Test 1: Basic echo
echo "Test 1: Basic echo works<br>";

// Test 2: HTML mixed with PHP
?>
<h2>Test 2: HTML mixed with PHP</h2>
<?php
echo "This is PHP code between HTML<br>";

// Test 3: Variables
$test = "Hello World";
echo "Test 3: Variable test - $test<br>";

// Test 4: Function
function testFunction() {
    return "Test 4: Function works<br>";
}
echo testFunction();

// Test 5: Error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
echo "Test 5: Error reporting enabled<br>";

// Test 6: PHP version
echo "Test 6: PHP Version - " . phpversion() . "<br>";
?> 
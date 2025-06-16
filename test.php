<!DOCTYPE html>
<html>
<head>
    <title>PHP Test</title>
</head>
<body>
    <h1>PHP Test Page</h1>
    
    <?php
    // Basic PHP test
    echo "<p>This is a test paragraph.</p>";
    
    // Test PHP version
    echo "<p>PHP Version: " . phpversion() . "</p>";
    
    // Test if we can use PHP variables
    $test = "Hello World";
    echo "<p>Variable test: " . $test . "</p>";
    
    // Test if we can use PHP functions
    echo "<p>Current time: " . date('Y-m-d H:i:s') . "</p>";
    ?>
</body>
</html> 
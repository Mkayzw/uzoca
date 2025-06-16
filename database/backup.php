<?php
require_once '../includes/init.php';

// Set backup directory
$backupDir = __DIR__ . '/backups';
if (!file_exists($backupDir)) {
    mkdir($backupDir, 0777, true);
}

// Generate backup filename with timestamp
$timestamp = date('Y-m-d_H-i-s');
$backupFile = $backupDir . '/uzoca_backup_' . $timestamp . '.sql';

try {
    // Get database configuration
    $host = DB_HOST;
    $user = DB_USER;
    $pass = DB_PASS;
    $dbname = DB_NAME;

    // Create backup command
    $command = sprintf(
        'mysqldump --host=%s --user=%s --password=%s %s > %s',
        escapeshellarg($host),
        escapeshellarg($user),
        escapeshellarg($pass),
        escapeshellarg($dbname),
        escapeshellarg($backupFile)
    );

    // Execute backup command
    exec($command, $output, $returnVar);

    if ($returnVar === 0) {
        echo "Database backup created successfully at: " . $backupFile;
        
        // Create a backup info file
        $infoFile = $backupDir . '/backup_info.txt';
        $info = "Backup created on: " . date('Y-m-d H:i:s') . "\n";
        $info .= "Backup file: " . basename($backupFile) . "\n";
        $info .= "Database: " . $dbname . "\n";
        $info .= "Tables included:\n";
        
        // Get list of tables
        $tables = $conn->query("SHOW TABLES");
        while ($table = $tables->fetch_array()) {
            $info .= "- " . $table[0] . "\n";
        }
        
        file_put_contents($infoFile, $info, FILE_APPEND);
        
        echo "\nBackup information has been recorded.";
    } else {
        throw new Exception("Backup command failed with return code: " . $returnVar);
    }
} catch (Exception $e) {
    echo "Error creating backup: " . $e->getMessage();
    
    // Log error
    $errorLog = $backupDir . '/backup_errors.log';
    $error = date('Y-m-d H:i:s') . " - Error: " . $e->getMessage() . "\n";
    file_put_contents($errorLog, $error, FILE_APPEND);
} 
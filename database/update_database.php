<?php
require_once '../includes/init.php';

// Function to create backup
function createBackup() {
    $backupDir = __DIR__ . '/backups';
    if (!file_exists($backupDir)) {
        mkdir($backupDir, 0777, true);
    }

    $timestamp = date('Y-m-d_H-i-s');
    $backupFile = $backupDir . '/uzoca_backup_' . $timestamp . '.sql';

    try {
        $host = DB_HOST;
        $user = DB_USER;
        $pass = DB_PASS;
        $dbname = DB_NAME;

        $command = sprintf(
            'mysqldump --host=%s --user=%s --password=%s %s > %s',
            escapeshellarg($host),
            escapeshellarg($user),
            escapeshellarg($pass),
            escapeshellarg($dbname),
            escapeshellarg($backupFile)
        );

        exec($command, $output, $returnVar);

        if ($returnVar === 0) {
            return [
                'success' => true,
                'file' => $backupFile,
                'message' => "Database backup created successfully at: " . $backupFile
            ];
        } else {
            throw new Exception("Backup command failed with return code: " . $returnVar);
        }
    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => "Error creating backup: " . $e->getMessage()
        ];
    }
}

// Function to execute SQL file
function executeSQLFile($file) {
    global $conn;
    
    try {
        $sql = file_get_contents($file);
        $statements = array_filter(array_map('trim', explode(';', $sql)));
        
        foreach ($statements as $statement) {
            if (!empty($statement)) {
                $conn->query($statement);
            }
        }
        
        return [
            'success' => true,
            'message' => "SQL file executed successfully: " . basename($file)
        ];
    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => "Error executing SQL file: " . $e->getMessage()
        ];
    }
}

// Main update process
echo "<h2>Database Update Process</h2>";

// Step 1: Create backup
echo "<h3>Step 1: Creating Database Backup</h3>";
$backupResult = createBackup();
echo "<p>" . $backupResult['message'] . "</p>";

if (!$backupResult['success']) {
    echo "<p style='color: red;'>Backup failed. Update process aborted.</p>";
    exit;
}

// Step 2: Update landlord dashboard
echo "<h3>Step 2: Updating Landlord Dashboard</h3>";
$landlordResult = executeSQLFile(__DIR__ . '/landlord_dashboard.sql');
echo "<p>" . $landlordResult['message'] . "</p>";

if (!$landlordResult['success']) {
    echo "<p style='color: red;'>Landlord dashboard update failed.</p>";
    // Continue with agent dashboard update
}

// Step 3: Update agent dashboard
echo "<h3>Step 3: Updating Agent Dashboard</h3>";
$agentResult = executeSQLFile(__DIR__ . '/agent_dashboard.sql');
echo "<p>" . $agentResult['message'] . "</p>";

if (!$agentResult['success']) {
    echo "<p style='color: red;'>Agent dashboard update failed.</p>";
}

// Final status
echo "<h3>Update Process Complete</h3>";
echo "<p>Backup file: " . basename($backupResult['file']) . "</p>";
echo "<p>Please check the results above for any errors.</p>";

// Add restore instructions
echo "<h3>Restore Instructions</h3>";
echo "<p>If you need to restore the database from backup, use the following command:</p>";
echo "<pre>mysql -h " . DB_HOST . " -u " . DB_USER . " -p " . DB_NAME . " < " . $backupResult['file'] . "</pre>"; 
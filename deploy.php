<?php
// Database configuration
$config = [
    'db_host' => 'localhost',
    'db_name' => 'housingquest',
    'db_user' => 'your_db_user',
    'db_pass' => 'your_db_password',
    'base_url' => 'https://your-domain.com',
    'admin_email' => 'kidmatrixx01@gmail.com'
];

// Create .env file
$env_content = "DB_HOST={$config['db_host']}\n";
$env_content .= "DB_NAME={$config['db_name']}\n";
$env_content .= "DB_USER={$config['db_user']}\n";
$env_content .= "DB_PASS={$config['db_pass']}\n";
$env_content .= "BASE_URL={$config['base_url']}\n";
$env_content .= "ADMIN_EMAIL={$config['admin_email']}\n";

file_put_contents('.env', $env_content);

// Create deployment instructions
$instructions = "Deployment Instructions:\n\n";
$instructions .= "1. Upload all files to your hosting server\n";
$instructions .= "2. Create a MySQL database named 'housingquest'\n";
$instructions .= "3. Import the database structure from database/subscription_tables.sql\n";
$instructions .= "4. Update the .env file with your actual database credentials\n";
$instructions .= "5. Ensure PHP version 7.4 or higher is installed\n";
$instructions .= "6. Set proper permissions (755 for directories, 644 for files)\n";
$instructions .= "7. Configure your web server (Apache/Nginx) to point to the project directory\n";
$instructions .= "8. Enable mod_rewrite for Apache\n";
$instructions .= "9. Set up SSL certificate for secure connections\n";

file_put_contents('DEPLOYMENT.md', $instructions);

echo "Deployment configuration files created successfully!\n";
echo "Please check DEPLOYMENT.md for detailed instructions.\n";
?> 
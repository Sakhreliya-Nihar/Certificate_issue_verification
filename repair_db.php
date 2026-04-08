<?php
// Set the content type to HTML for styled output
header('Content-Type: text/html; charset=utf-8');

echo "<!DOCTYPE html><html lang='en'><head><title>Database Repair</title><style>
body { font-family: sans-serif; background-color: #f0f2f5; color: #333; padding: 20px; }
h1 { color: #27ae60; }
.error { color: #c0392b; border: 1px solid #c0392b; padding: 15px; background-color: #fbeae5; border-radius: 5px; }
</style></head><body>";

// Include the database connection
include 'db.php';

// Array of SQL queries to execute in order
$queries = [
    // 1. Disable Foreign Key Checks
    'SET FOREIGN_KEY_CHECKS=0;',

    // 2. Create User Table
    "CREATE TABLE IF NOT EXISTS user (
        user_id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        username varchar(50) NOT NULL,
        email varchar(100) NOT NULL UNIQUE,
        password varchar(255) NOT NULL,
        role varchar(20) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

    // 3. Create Student Table
    "CREATE TABLE IF NOT EXISTS student (
        student_id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        user_id int(11) DEFAULT NULL,
        first_name varchar(50) NOT NULL,
        last_name varchar(50) NOT NULL,
        register_no varchar(10) NOT NULL,
        year varchar(5) NOT NULL,
        FOREIGN KEY (user_id) REFERENCES user(user_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

    // 4. Create Certificates Table
    "CREATE TABLE IF NOT EXISTS certificates (
        id INT AUTO_INCREMENT PRIMARY KEY,
        student_id INT NOT NULL,
        code VARCHAR(10) UNIQUE NOT NULL,
        status VARCHAR(20) DEFAULT 'issued',
        issue_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (student_id) REFERENCES student(student_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

    // 5. Re-enable Foreign Key Checks
    'SET FOREIGN_KEY_CHECKS=1;'
];

// Execute each query
$all_successful = true;
foreach ($queries as $query) {
    if ($conn->query($query) !== TRUE) {
        // If a query fails, print the error and stop
        echo "<div class='error'><strong>Error executing query:</strong><br>" . $conn->error . "<br><br><strong>Query:</strong><pre>" . htmlspecialchars($query) . "</pre></div>";
        $all_successful = false;
        break; // Exit the loop on first error
    }
}

// If all queries were successful, print the success message
if ($all_successful) {
    echo "<h1>✅ Tables Repaired & Created Successfully!</h1>";
}

echo "</body></html>";

// Close the connection
$conn->close();
?>

<?php
// Set the content type to HTML
header('Content-Type: text/html; charset=utf-8');

// Include the database connection file
include 'db.php';

// SQL query to create the 'certificates' table if it doesn't exist
$sql = "CREATE TABLE IF NOT EXISTS certificates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    code VARCHAR(10) UNIQUE NOT NULL,
    status VARCHAR(20) DEFAULT 'issued',
    issue_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES student(student_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

// Execute the query
if ($conn->query($sql) === TRUE) {
    // On success, print a styled success message
    echo '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Database Setup Success</title>
        <style>
            body { 
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; 
                display: flex; 
                justify-content: center; 
                align-items: center; 
                height: 100vh; 
                background-color: #f4f7f6; 
                margin: 0;
            }
            .container { 
                text-align: center; 
                padding: 40px; 
                background-color: #fff; 
                border-radius: 10px; 
                box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            }
            h1 { 
                color: #27ae60; 
                font-size: 2.5rem;
            }
            p {
                color: #555;
                font-size: 1.1rem;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>✅ Database Updated Successfully!</h1>
            <p>The `certificates` table is ready. You can now delete this file.</p>
        </div>
    </body>
    </html>';
} else {
    // On failure, print the error message
    echo "Error creating table: " . $conn->error;
}

// Close the connection
$conn->close();
?>

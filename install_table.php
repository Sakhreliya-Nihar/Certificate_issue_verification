<?php
// Include the database connection file
include 'db.php';

// SQL query to create the 'certificates' table
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
    echo "Success: Certificates table created.
";
} else {
    echo "Error creating table: " . $conn->error . "
";
}

// Close the connection
$conn->close();
?>

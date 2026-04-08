<?php
session_start();

// SECURITY CHECK: Only allow admins to trigger this update
if (!isset($_SESSION['user_id']) || strtolower($_SESSION['role']) !== 'admin') {
    echo "Unauthorized access";
    exit();
}

include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect Input
    $studentId = $_POST['studentId'];
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    // Add more fields here as needed later!

    // PREPARED STATEMENT: This completely prevents SQL Injection
    $sql = "UPDATE student SET first_name=?, last_name=? WHERE student_id=?";
    
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        echo "Database error: " . $conn->error;
        exit();
    }

    // Bind parameters: "s" for string (names), "i" for integer (student_id)
    $stmt->bind_param("ssi", $firstName, $lastName, $studentId);

    // Execute the secure query
    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "Error updating record: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request";
}
?>
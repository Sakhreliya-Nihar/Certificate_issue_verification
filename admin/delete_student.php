<?php
session_start();
if (!isset($_SESSION['user_id']) || strtolower($_SESSION['role']) !== 'admin') {
    http_response_code(403); // 403 Forbidden
    echo "Unauthorized access.";
    exit();
}

include 'db.php'; // Make sure to include your database connection

if(isset($_GET['student_id'])) {
    $student_id = $_GET['student_id'];
    
    // Use prepared statements to prevent SQL injection
    $stmt = $conn->prepare("DELETE FROM student WHERE student_id = ?");
    $stmt->bind_param("i", $student_id); // 'i' for integer
    
    if ($stmt->execute() === TRUE) {
        echo "success";
    } else {
        echo "error";
    }

    $stmt->close();
    $conn->close();
}
?>
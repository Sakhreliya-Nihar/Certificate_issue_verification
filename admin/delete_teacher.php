<?php
session_start();
if (!isset($_SESSION['user_id']) || strtolower($_SESSION['role']) !== 'admin') {
    http_response_code(403); // 403 Forbidden
    echo "Unauthorized access.";
    exit();
}
include 'db.php'; 

if (isset($_GET['teacher_id'])) {
    $teacher_id = $_GET['teacher_id'];

    // Prepared statement prevents SQLi
    $stmt = $conn->prepare("DELETE FROM teacher WHERE teacher_id = ?");
    $stmt->bind_param("i", $teacher_id); 

    if ($stmt->execute() === TRUE) {
        echo "success";
    } else {
        echo "error";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "invalid_request";
}
?>
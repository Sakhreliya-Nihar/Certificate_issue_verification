<?php
session_start();
if (!isset($_SESSION['user_id']) || strtolower($_SESSION['role']) !== 'teacher') {
    http_response_code(403); // 403 Forbidden
    echo "Unauthorized";
    exit();
}
?>
include 'db.php'; // Make sure to include your database connection

if(isset($_GET['student_id'])) {
    $student_id = $_GET['student_id'];
    $query = "DELETE FROM student WHERE student_id = $student_id";
    $result = $conn->query($query);

    if ($result === TRUE) {
        echo "success";
    } else {
        echo "error";
    }

    $conn->close();
}
?>

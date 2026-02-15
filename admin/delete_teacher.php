<?php
include 'db.php'; // Make sure to include your database connection

if(isset($_GET['teacher_id'])) {
    $teacher_id = $_GET['teacher_id'];

    // Use prepared statements
    $stmt = $conn->prepare("DELETE FROM teacher WHERE teacher_id = ?");
    $stmt->bind_param("i", $teacher_id); // 'i' for integer

    if ($stmt->execute() === TRUE) {
        echo "success";
    } else {
        echo "error";
    }

    $stmt->close();
    $conn->close();
}
?>
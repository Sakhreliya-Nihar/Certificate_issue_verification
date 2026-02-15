<?php
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

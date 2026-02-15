<?php
session_start(); // Start the session
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Use a prepared statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM user WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();

        // Verify the hashed password
        if (password_verify($password, $row['password'])) {
            // Password is correct!
            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['role'] = $row['role'];

            // Check the role and redirect accordingly
            $role = strtolower($row['role']);

            if ($role == 'admin') {
                echo "<script>alert('Login successful'); window.location.href='index.php';</script>";
                exit();
            } elseif ($role == 'teacher') {
                echo "<script>alert('Login successful'); window.location.href='../teacher/index.php';</script>"; // Go to teacher folder
                exit();
            } elseif ($role == 'student') {
                echo "<script>alert('Login successful'); window.location.href='../student/index.php';</script>"; // Go to student folder
                exit();
            } else {
                echo "<script>alert('Login successful, but role is unknown.'); window.location.href='login.html';</script>";
                exit();
            }
        } else {
            // Invalid password
            echo "<script>alert('Invalid email or password'); window.location.href='login.html';</script>";
            exit();
        }
    } else {
        // Invalid email
        echo "<script>alert('Invalid email or password'); window.location.href='login.html';</script>";
        exit();
    }
    $stmt->close();
}
$conn->close();
?>
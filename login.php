<?php
session_start(); // Start the session ONCE at the top
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
            
            // Set session variables (NO second session_start() here)
            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['role'] = $row['role'];

            // Check the role and redirect accordingly
            // Using strtolower() makes the check case-insensitive (e.g., 'Admin' or 'admin')
            $role = strtolower($row['role']);

            if ($role == 'admin') {
                echo "<script>alert('Login successful'); window.location.href='./admin/index.php';</script>";
                exit();
            } elseif ($role == 'teacher') {
                echo "<script>alert('Login successful'); window.location.href='./teacher/index.php';</script>";
                exit();
            } elseif ($role == 'student') {
                echo "<script>alert('Login successful'); window.location.href='./student/index.php';</script>";
                exit();
            } else {
                // Role is not recognized
                echo "<script>alert('Login successful, but user role is unknown.'); window.location.href='login.html';</script>";
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
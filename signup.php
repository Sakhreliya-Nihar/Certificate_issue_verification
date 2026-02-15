<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if(isset($_POST['username']) && isset($_POST['email']) && isset($_POST['password']) && isset($_POST['role'])) {
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $role = $_POST['role'];

        // Check if the email already exists using a prepared statement
        $stmt_check = $conn->prepare("SELECT * FROM user WHERE email=?");
        $stmt_check->bind_param("s", $email);
        $stmt_check->execute();
        $check_result = $stmt_check->get_result();

        if ($check_result->num_rows > 0) {
            echo "<script>alert('Email already registered. Please use a different email.'); window.location.href='login.html';</script>";
            exit();
        }
        $stmt_check->close();

        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert new user using a prepared statement
        $stmt_insert = $conn->prepare("INSERT INTO user (username, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt_insert->bind_param("ssss", $username, $email, $hashed_password, $role);

        if ($stmt_insert->execute() === TRUE) {
            echo "<script>alert('Registration successful'); window.location.href='login.html';</script>";
            exit();
        } else {
            echo "Error: " . $stmt_insert->error;
        }
        $stmt_insert->close();
    } else {
        echo "All fields are required";
    }
}

$conn->close();
?>
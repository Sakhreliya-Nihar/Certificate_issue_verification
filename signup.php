<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if(isset($_POST['username']) && isset($_POST['email']) && isset($_POST['password']) && isset($_POST['role'])) {

        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $password = $_POST['password']; 
        $role = trim($_POST['role']);

        if (!preg_match("/^[a-zA-Z\s]+$/", $username)) {
            echo "<script>alert('Invalid username. Only letters and spaces are allowed. Numbers and special characters are restricted.'); window.history.back();</script>";
            exit();
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo "<script>alert('Invalid email format. Please enter a valid email address.'); window.history.back();</script>";
            exit();
        }

        if (strlen($password) < 8 || 
            !preg_match("/[A-Z]/", $password) || 
            !preg_match("/[a-z]/", $password) || 
            !preg_match("/[0-9]/", $password) || 
            !preg_match("/[\W_]/", $password)) {
            
            echo "<script>alert('Weak Password! It must be at least 8 characters long and include an uppercase letter, a lowercase letter, a number, and a special character.'); window.history.back();</script>";
            exit();
        }

        $stmt_check = $conn->prepare("SELECT * FROM user WHERE email=?");
        $stmt_check->bind_param("s", $email);
        $stmt_check->execute();
        $check_result = $stmt_check->get_result();

        if ($check_result->num_rows > 0) {
            // CLEAN URL READY: Automatically loads the index file without extensions
            echo "<script>alert('Email already registered. Please use a different email.'); window.location.href='/Student-Achievement-System/';</script>";
            exit();
        }
        $stmt_check->close();

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt_insert = $conn->prepare("INSERT INTO user (username, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt_insert->bind_param("ssss", $username, $email, $hashed_password, $role);

        if ($stmt_insert->execute() === TRUE) {
            // CLEAN URL READY: Automatically loads the index file without extensions
            echo "<script>alert('Registration successful'); window.location.href='/Student-Achievement-System/';</script>";
            exit();
        } else {
            echo "Error: " . $stmt_insert->error;
        }
        $stmt_insert->close();
    } else {
        echo "<script>alert('All fields are required'); window.history.back();</script>";
    }
}

$conn->close();
?>
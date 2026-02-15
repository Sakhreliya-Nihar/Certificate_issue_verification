<?php
include 'db.php';

// Helper function to validate input
function validateInput($input, $pattern) {
    if (preg_match($pattern, $input)) {
        return $input;
    } else {
        return htmlspecialchars(strip_tags($input));
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Collect Input
    $first_name = $_POST["first_name"];
    $last_name = $_POST["last_name"];
    $register_no = $_POST["register_no"];
    $roll_no = $_POST["roll_no"];
    $year = $_POST["year"]; 
    $email = $_POST["email"];
    $phone = $_POST["phone"]; 
    $address = $_POST["address"];
    $father = $_POST["father"];
    $mother = $_POST["mother"];
    $aadhar = $_POST["aadhar"];
    $dob = $_POST["dob"]; 
    $gender = $_POST["gender"];
    $dist = $_POST["dist"];
    $pincode = $_POST["pincode"];
    $uploaded = date('Y-m-d H:i:s');

    // 2. Handle File Uploads
    $target_dir = "../uploads/";
    if (!is_dir($target_dir)) { mkdir($target_dir, 0777, true); }

    $image_name = basename($_FILES["image"]["name"]);
    $image_path = $target_dir . $image_name;
    move_uploaded_file($_FILES["image"]["tmp_name"], $image_path);

    $file_name = basename($_FILES["file"]["name"]);
    $file_path = $target_dir . $file_name;
    move_uploaded_file($_FILES["file"]["tmp_name"], $file_path);

    // 3. Prepare SQL
    $sql = "INSERT INTO student (first_name, last_name, register_no, roll_no, `year`, email, phone, address, father, mother, aadhar, dob, gender, dist, pincode, file, image, uploaded)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    // --- ERROR REPORTER ---
    // If the database rejects the query, this will tell you WHY.
    if ($stmt === false) {
        die('<div style="background-color: #ffebee; border: 1px solid #f44336; padding: 20px; color: #d32f2f; font-family: sans-serif;">
                <h2>❌ Database Error</h2>
                <p>The database rejected your request. This usually means a column name in the code does not match the database.</p>
                <strong>MySQL Said:</strong> ' . $conn->error . '<br><br>
                <strong>The Query Was:</strong> <pre>' . $sql . '</pre>
             </div>');
    }
    // ----------------------

    $stmt->bind_param(
        "ssssssssssssssssss",
        $first_name, $last_name, $register_no, $roll_no, $year, $email, $phone, 
        $address, $father, $mother, $aadhar, $dob, $gender, $dist, $pincode, 
        $file_path, $image_path, $uploaded
    );

    if ($stmt->execute()) {
        echo '<script>alert("New student record created successfully");</script>';
        echo '<script>window.location.href = "view_students.php";</script>';
    } else {
        echo "Error execution: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
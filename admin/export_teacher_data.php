<?php
    session_start();

    // SECURITY CHECK: Prevent direct unauthorized access
    if (!isset($_SESSION['user_id']) || strtolower($_SESSION['role']) !== 'admin') {
        die("Unauthorized Access");
    }

    include 'db.php';

    $sql = "SELECT * FROM teacher";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Export data to Excel
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename=teacher_data.xls');

        $output = fopen('php://output', 'w');
        fputcsv($output, array(
            'First Name', 'Last Name', 'Register No', 'Email', 'Phone', 'Address', 'Image Path'
        ), "\t"); 

        while($row = $result->fetch_assoc()) {
            fputcsv($output, array(
                $row['first_name'], $row['last_name'], $row['reg_no'], $row['email'], $row['phone_number'], 
                $row['address'], $row['image']
            ), "\t"); 
        }

        fclose($output);
    } else {
        echo "No teacher found";
    }

    $conn->close();
?>
<?php
    include 'db.php';

    $sql = "SELECT * FROM student";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Export data to Excel
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename=student_data.xls');

        $output = fopen('php://output', 'w');
        fputcsv($output, array(
            'First Name', 'Last Name', 'Register No', 'Roll No', 
            'Year', 'Email', 'Phone', 'Address', 'Father', 'Mother', 
            'Aadhar', 'DOB', 'Gender', 'District', 'Pincode'
        ), "\t"); // Use tab as delimiter

        while($row = $result->fetch_assoc()) {
            fputcsv($output, array(
                $row['first_name'], $row['last_name'], $row['register_no'], 
                $row['roll_no'], $row['year'], $row['email'], $row['phone'], 
                $row['address'], $row['father'], $row['mother'], $row['aadhar'], 
                $row['dob'], $row['gender'], $row['dist'], $row['pincode']
            ), "\t"); // Use tab as delimiter
        }

        fclose($output);
    } else {
        echo "No students found";
    }

    $conn->close();
?>

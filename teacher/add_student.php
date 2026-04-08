<?php
session_start();
if (!isset($_SESSION['user_id']) || strtolower($_SESSION['role']) !== 'teacher') {
    http_response_code(403); // 403 Forbidden
    echo "Unauthorized access.";
    exit();
}
include 'db.php';

// --- PROCESS FORM SUBMISSION ---
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

    if ($stmt === false) {
        die('<div style="background-color: #ffebee; border: 1px solid #f44336; padding: 20px; color: #d32f2f;">
                <h2>❌ Database Error</h2>
                <strong>MySQL Said:</strong> ' . $conn->error . '<br><br>
                <strong>The Query Was:</strong> <pre>' . $sql . '</pre>
             </div>');
    }

    $stmt->bind_param(
        "ssssssssssssssssss",
        $first_name, $last_name, $register_no, $roll_no, $year, $email, $phone, 
        $address, $father, $mother, $aadhar, $dob, $gender, $dist, $pincode, 
        $file_path, $image_path, $uploaded
    );

    if ($stmt->execute()) {
        echo '<script>alert("New student record created successfully");</script>';
        echo '<script>window.location.href = "view_students";</script>'; // Clean URL redirect
    } else {
        echo "<script>alert('Error execution: " . $stmt->error . "');</script>";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Add Student</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <div id="sidebar" class="fixed inset-y-0 left-0 z-30 w-64 transform -translate-x-full overflow-y-auto bg-gray-900 p-4 transition-transform duration-300 ease-in-out md:translate-x-0">
            <div class="mb-8 flex flex-col items-center">
                <img src="avatar.png" alt="Avatar" class="mb-3 h-24 w-24 rounded-full border-4 border-gray-700">
                <h3 class="text-xl font-semibold text-white">Admin</h3>
                <span class="text-sm text-gray-400">Administrator</span>
            </div>

            <nav class="flex flex-col space-y-2">
                <a href="index" class="group flex items-center rounded-lg px-4 py-3 text-gray-300 transition-colors hover:bg-gray-800 hover:text-white">
                    <i class="fas fa-tachometer-alt mr-3 w-5 text-center"></i><span class="font-medium">Dashboard</span>
                </a>
                <a href="class" class="group flex items-center rounded-lg px-4 py-3 text-gray-300 transition-colors hover:bg-gray-800 hover:text-white">
                    <i class="fas fa-users mr-3 w-5 text-center"></i><span class="font-medium">Class</span>
                </a>
                <a href="student" class="group flex items-center rounded-lg bg-indigo-600 px-4 py-3 text-white transition-colors hover:bg-indigo-700">
                    <i class="fas fa-user-graduate mr-3 w-5 text-center"></i><span class="font-medium">Student</span>
                </a>
                <a href="issue_certificate" class="group flex items-center rounded-lg px-4 py-3 text-gray-300 transition-colors hover:bg-gray-800 hover:text-white">
                    <i class="fas fa-certificate mr-3 w-5 text-center"></i>
                    <span class="font-medium">Issue Certificate</span>
                </a>
                <a href="verify_certificate" class="group flex items-center rounded-lg px-4 py-3 text-gray-300 transition-colors hover:bg-gray-800 hover:text-white">
                    <i class="fas fa-certificate mr-3 w-5 text-center"></i>
                    <span class="font-medium">Verify Certificate</span>
                </a>
            </nav>

            <div class="absolute bottom-0 left-0 w-full p-4">
                <a href="../logout" class="group flex items-center rounded-lg px-4 py-3 text-gray-300 transition-colors hover:bg-red-700 hover:text-white">
                    <i class="fas fa-sign-out-alt mr-3 w-5 text-center"></i><span class="font-medium">Logout</span>
                </a>
            </div>
        </div>

        <div class="flex-1 overflow-y-auto md:ml-64">
            <header class="sticky top-0 z-20 flex items-center justify-between border-b border-gray-200 bg-white p-4 shadow-sm">
                <div class="flex items-center">
                    <button id="sidebar-toggle" class="mr-4 rounded-lg p-2 text-gray-600 hover:bg-gray-100 md:hidden">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <h1 class="text-2xl font-bold text-gray-800">Add New Student</h1>
                </div>
                <a href="../logout" class="hidden rounded-lg bg-red-600 px-5 py-2.5 text-sm font-medium text-white shadow-md transition-colors hover:bg-red-700 md:block">Logout</a>
            </header>

            <main class="p-6 md:p-8">
                <div class="rounded-xl bg-white p-6 shadow-lg">
                    <h2 class="mb-6 text-2xl font-semibold text-gray-700">Student Information</h2>
                    
                    <form action="add_student" method="post" enctype="multipart/form-data" onsubmit="return validateForm()">
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div><label class="mb-2 block text-sm font-medium">First Name:</label><input type="text" name="first_name" class="block w-full rounded-lg border p-2.5" required></div>
                            <div><label class="mb-2 block text-sm font-medium">Last Name:</label><input type="text" name="last_name" class="block w-full rounded-lg border p-2.5" required></div>
                            <div><label class="mb-2 block text-sm font-medium">Register No:</label><input type="text" name="register_no" class="block w-full rounded-lg border p-2.5" required></div>
                            <div><label class="mb-2 block text-sm font-medium">Roll No:</label><input type="text" name="roll_no" class="block w-full rounded-lg border p-2.5" required></div>
                            <div><label class="mb-2 block text-sm font-medium">Year (I, II, III, IV, or V):</label><input type="text" name="year" class="block w-full rounded-lg border p-2.5" required></div>
                            <div><label class="mb-2 block text-sm font-medium">Email:</label><input type="email" name="email" class="block w-full rounded-lg border p-2.5" required></div>
                            <div><label class="mb-2 block text-sm font-medium">Phone:</label><input type="tel" name="phone" class="block w-full rounded-lg border p-2.5" required></div>
                            <div><label class="mb-2 block text-sm font-medium">Date of Birth:</label><input type="date" name="dob" class="block w-full rounded-lg border p-2.5" required></div>
                            <div class="md:col-span-2"><label class="mb-2 block text-sm font-medium">Address:</label><textarea name="address" rows="3" class="block w-full rounded-lg border p-2.5" required></textarea></div>
                            <div><label class="mb-2 block text-sm font-medium">Father's Name:</label><input type="text" name="father" class="block w-full rounded-lg border p-2.5" required></div>
                            <div><label class="mb-2 block text-sm font-medium">Mother's Name:</label><input type="text" name="mother" class="block w-full rounded-lg border p-2.5" required></div>
                            <div><label class="mb-2 block text-sm font-medium">Aadhar No:</label><input type="text" name="aadhar" class="block w-full rounded-lg border p-2.5" required></div>
                            
                            <div>
                                <label class="mb-2 block text-sm font-medium">Gender:</label>
                                <div class="flex space-x-4">
                                    <label><input type="radio" name="gender" value="Male" required> Male</label>
                                    <label><input type="radio" name="gender" value="Female" required> Female</label>
                                    <label><input type="radio" name="gender" value="Other" required> Other</label>
                                </div>
                            </div>
                            
                            <div><label class="mb-2 block text-sm font-medium">District:</label><input type="text" name="dist" class="block w-full rounded-lg border p-2.5" required></div>
                            <div><label class="mb-2 block text-sm font-medium">Pincode:</label><input type="text" name="pincode" class="block w-full rounded-lg border p-2.5" required></div>
                            <div><label class="mb-2 block text-sm font-medium">Upload File:</label><input type="file" name="file" class="block w-full border rounded-lg" required></div>
                            <div><label class="mb-2 block text-sm font-medium">Upload Image:</label><input type="file" name="image" class="block w-full border rounded-lg" required></div>
                            
                            <div class="md:col-span-2"><img id="uploadedImage" src="#" style="display: none;" class="w-32 rounded border p-1"></div>
                            <div class="md:col-span-2"><label><input type="checkbox" name="uploaded" value="yes" required> I confirm details are correct.</label></div>
                        </div>
                        <div class="mt-8 text-right"><input type="submit" value="Submit Student Details" class="rounded-lg bg-indigo-600 px-6 py-3 text-white hover:bg-indigo-700 cursor-pointer"></div>
                    </form>
                </div>
            </main>
        </div>
    </div>
    
    <script>
        document.getElementById('sidebar-toggle').addEventListener('click', function () { document.getElementById('sidebar').classList.toggle('-translate-x-full'); });
        document.getElementsByName("image")[0].addEventListener('change', function () {
            var reader = new FileReader();
            reader.onload = function (e) {
                var img = document.getElementById("uploadedImage");
                img.style.display = "block";
                img.src = e.target.result;
            }
            reader.readAsDataURL(this.files[0]);
        });
        function validateForm() { return true; /* Your long validation logic goes here */ }
    </script>
</body>
</html>
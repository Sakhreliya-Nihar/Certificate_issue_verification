<?php
session_start();
include 'db.php';

// Check if teacher is logged in (Optional: Uncomment when ready)
// if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
//     header("Location: ../login.php");
//     exit();
// }

$students = [];
$message = '';
$error = '';

// Fetch all students for the dropdown
$student_result = $conn->query("SELECT student_id, first_name, last_name, register_no FROM student ORDER BY first_name, last_name");
if ($student_result) {
    $students = $student_result->fetch_all(MYSQLI_ASSOC);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['issue_certificate'])) {
    $student_id = $_POST['student_id'];
    
    // Check if a file was uploaded
    if (empty($student_id)) {
        $error = "Please select a student.";
    } elseif (!isset($_FILES['cert_file']) || $_FILES['cert_file']['error'] != 0) {
        $error = "Please upload a valid certificate file (PDF, JPG, PNG).";
    } else {
        // 1. Generate a unique 10-digit code
        $is_unique = false;
        $certificate_code = '';
        while (!$is_unique) {
            $certificate_code = substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 10);
            $check_stmt = $conn->prepare("SELECT id FROM certificates WHERE code = ?");
            $check_stmt->bind_param("s", $certificate_code);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();
            if ($check_result->num_rows == 0) {
                $is_unique = true;
            }
            $check_stmt->close();
        }

        // 2. Handle File Upload
        // We will store certificates in a specific folder
        $target_dir = "../uploads/certificates/";
        
        // Create folder if it doesn't exist
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        // Get file extension (e.g., .pdf, .jpg)
        $file_ext = strtolower(pathinfo($_FILES["cert_file"]["name"], PATHINFO_EXTENSION));
        
        // Allowed file types
        $allowed_types = ['jpg', 'jpeg', 'png', 'pdf'];

        if (!in_array($file_ext, $allowed_types)) {
            $error = "Invalid file type. Only JPG, PNG, and PDF are allowed.";
        } else {
            // Rename file to match the Certificate Code (Clean and Organized)
            // Example: ../uploads/certificates/1RHBCLJQZS.pdf
            $new_filename = $certificate_code . "." . $file_ext;
            $target_file = $target_dir . $new_filename;

            if (move_uploaded_file($_FILES["cert_file"]["tmp_name"], $target_file)) {
                
                // 3. Insert into database (Including the file path)
                $insert_stmt = $conn->prepare("INSERT INTO certificates (student_id, code, status, certificate_file) VALUES (?, ?, 'issued', ?)");
                $insert_stmt->bind_param("iss", $student_id, $certificate_code, $target_file);

                if ($insert_stmt->execute()) {
                    $message = "Successfully issued certificate.<br>Code: <strong>" . htmlspecialchars($certificate_code) . "</strong><br>File saved as: " . htmlspecialchars($new_filename);
                } else {
                    $error = "Database Error: " . $conn->error;
                }
                $insert_stmt->close();

            } else {
                $error = "Sorry, there was an error uploading your file.";
            }
        }
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Dashboard - Issue Certificate</title>
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
                <h3 class="text-xl font-semibold text-white">Teacher</h3>
                <span class="text-sm text-gray-400">Teacher Portal</span>
            </div>
            <nav class="flex flex-col space-y-2">
                <a href="index.php" class="group flex items-center rounded-lg px-4 py-3 text-gray-300 transition-colors hover:bg-gray-800 hover:text-white"><i class="fas fa-tachometer-alt mr-3 w-5 text-center"></i><span class="font-medium">Dashboard</span></a>
                <a href="class.php" class="group flex items-center rounded-lg px-4 py-3 text-gray-300 transition-colors hover:bg-gray-800 hover:text-white"><i class="fas fa-users mr-3 w-5 text-center"></i><span class="font-medium">Class</span></a>
                <a href="student.html" class="group flex items-center rounded-lg px-4 py-3 text-gray-300 transition-colors hover:bg-gray-800 hover:text-white"><i class="fas fa-user-graduate mr-3 w-5 text-center"></i><span class="font-medium">Student</span></a>
                <a href="issue_certificate.php" class="group flex items-center rounded-lg bg-indigo-600 px-4 py-3 text-white transition-colors hover:bg-indigo-700"><i class="fas fa-certificate mr-3 w-5 text-center"></i><span class="font-medium">Issue Certificate</span></a>
            </nav>
            <div class="absolute bottom-0 left-0 w-full p-4">
                <a href="../login.php" class="group flex items-center rounded-lg px-4 py-3 text-gray-300 transition-colors hover:bg-red-700 hover:text-white"><i class="fas fa-sign-out-alt mr-3 w-5 text-center"></i><span class="font-medium">Logout</span></a>
            </div>
        </div>

        <div class="flex-1 overflow-y-auto md:ml-64">
            <header class="sticky top-0 z-20 flex items-center justify-between border-b border-gray-200 bg-white p-4 shadow-sm">
                <div class="flex items-center">
                    <button id="sidebar-toggle" class="mr-4 rounded-lg p-2 text-gray-600 hover:bg-gray-100 md:hidden"><i class="fas fa-bars text-xl"></i></button>
                    <h1 class="text-2xl font-bold text-gray-800">Issue New Certificate</h1>
                </div>
                <a href="../login.php" class="hidden rounded-lg bg-red-600 px-5 py-2.5 text-sm font-medium text-white shadow-md transition-colors hover:bg-red-700 md:block">Logout</a>
            </header>

            <main class="p-6 md:p-8">
                <div class="rounded-xl bg-white p-6 shadow-lg">
                    <h2 class="mb-6 text-2xl font-semibold text-gray-700">Select Student & Upload Certificate</h2>

                    <?php if ($message): ?>
                        <div class="mb-4 rounded-lg bg-green-100 p-4 text-green-700">
                            <?= $message ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($error): ?>
                        <div class="mb-4 rounded-lg bg-red-100 p-4 text-red-700">
                            <?= $error ?>
                        </div>
                    <?php endif; ?>

                    <form action="issue_certificate.php" method="post" enctype="multipart/form-data">
                        <div class="grid grid-cols-1 gap-6">
                            
                            <div>
                                <label for="student_id" class="mb-2 block text-sm font-medium text-gray-900">Student:</label>
                                <select name="student_id" id="student_id" class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-indigo-500 focus:ring-indigo-500" required>
                                    <option value="">-- Select a Student --</option>
                                    <?php foreach ($students as $student): ?>
                                        <option value="<?= $student['student_id'] ?>">
                                            <?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name'] . ' (' . $student['register_no'] . ')') ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div>
                                <label for="cert_file" class="mb-2 block text-sm font-medium text-gray-900">Upload Digital Certificate (PDF, JPG, PNG):</label>
                                <input type="file" name="cert_file" id="cert_file" accept=".pdf, .jpg, .jpeg, .png" class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2 text-sm text-gray-900 focus:border-indigo-500 focus:ring-indigo-500" required>
                                <p class="mt-1 text-xs text-gray-500">File will be renamed automatically to the certificate code.</p>
                            </div>

                        </div>
                        <div class="mt-8 text-right">
                            <button type="submit" name="issue_certificate" class="cursor-pointer rounded-lg bg-indigo-600 px-6 py-3 text-sm font-medium text-white shadow-md transition-colors hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                Generate & Issue Certificate
                            </button>
                        </div>
                    </form>
                </div>
            </main>
        </div>
    </div>

    <script>
        document.getElementById('sidebar-toggle').addEventListener('click', function () {
            var sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('-translate-x-full');
        });
    </script>
</body>
</html>
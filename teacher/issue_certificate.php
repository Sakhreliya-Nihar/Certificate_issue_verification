<?php
session_start();
include 'db.php';

/* =========================
   ACCESS CONTROL
========================= */
if (!isset($_SESSION['user_id']) || strtolower($_SESSION['role']) !== 'teacher') {
    http_response_code(403);
    echo "Unauthorized access.";
    exit();
}

/* =========================
   CSRF TOKEN
========================= */
if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

$students = [];
$message = '';
$error = '';

/* =========================
   FETCH STUDENTS
========================= */
$student_result = $conn->query("SELECT student_id, first_name, last_name, register_no FROM student ORDER BY first_name, last_name");
if ($student_result) {
    $students = $student_result->fetch_all(MYSQLI_ASSOC);
}

/* =========================
   HANDLE FORM
========================= */
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['issue_certificate'])) {

    // CSRF CHECK
    if (!isset($_POST['csrf']) || $_POST['csrf'] !== $_SESSION['csrf']) {
        die("CSRF validation failed");
    }

    $student_id = intval($_POST['student_id']);

    if (empty($student_id)) {
        $error = "Please select a student.";
    } elseif (!isset($_FILES['cert_file']) || $_FILES['cert_file']['error'] != 0) {
        $error = "Please upload a valid certificate file (PDF, JPG, PNG).";
    } else {

        // FILE SIZE LIMIT (5MB)
        if ($_FILES['cert_file']['size'] > 5 * 1024 * 1024) {
            $error = "File too large (Max 5MB).";
        } else {

            $file_ext = strtolower(pathinfo($_FILES["cert_file"]["name"], PATHINFO_EXTENSION));
            $allowed_types = ['jpg', 'jpeg', 'png', 'pdf'];

            // MIME VALIDATION
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $_FILES["cert_file"]["tmp_name"]);
            finfo_close($finfo);

            $allowed_mime = ['image/jpeg', 'image/png', 'application/pdf'];

            if (!in_array($file_ext, $allowed_types) || !in_array($mime, $allowed_mime)) {
                $error = "Invalid file type.";
            } else {

                /* UNIQUE CODE (SECURE) */
                do {
                    $certificate_code = strtoupper(substr(bin2hex(random_bytes(6)), 0, 10));
                    $check_stmt = $conn->prepare("SELECT id FROM certificates WHERE code = ?");
                    $check_stmt->bind_param("s", $certificate_code);
                    $check_stmt->execute();
                    $check_result = $check_stmt->get_result();
                    $exists = $check_result->num_rows > 0;
                    $check_stmt->close();
                } while ($exists);

                /* UPLOAD */
                $target_dir = __DIR__ . "/../uploads/certificates/";

                if (!is_dir($target_dir)) {
                    mkdir($target_dir, 0755, true);
                }

                $new_filename = $certificate_code . "." . $file_ext;
                $target_file = $target_dir . $new_filename;

                if (move_uploaded_file($_FILES["cert_file"]["tmp_name"], $target_file)) {

                    // SAVE RELATIVE PATH
                    $db_path = "uploads/certificates/" . $new_filename;

                    $insert_stmt = $conn->prepare("INSERT INTO certificates (student_id, code, status, certificate_file) VALUES (?, ?, 'issued', ?)");
                    $insert_stmt->bind_param("iss", $student_id, $certificate_code, $db_path);

                    if ($insert_stmt->execute()) {
                        $message = "Successfully issued certificate.<br>Code: <strong>" . htmlspecialchars($certificate_code) . "</strong><br>File saved as: " . htmlspecialchars($new_filename);
                    } else {
                        $error = "Database Error.";
                    }

                    $insert_stmt->close();

                } else {
                    $error = "Sorry, there was an error uploading your file.";
                }
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
                <a href="index" class="group flex items-center rounded-lg px-4 py-3 text-gray-300 hover:bg-gray-800 hover:text-white"><i class="fas fa-tachometer-alt mr-3"></i>Dashboard</a>
                <a href="class" class="group flex items-center rounded-lg px-4 py-3 text-gray-300 hover:bg-gray-800 hover:text-white"><i class="fas fa-users mr-3"></i>Class</a>
                <a href="student" class="group flex items-center rounded-lg px-4 py-3 text-gray-300 hover:bg-gray-800 hover:text-white"><i class="fas fa-user-graduate mr-3"></i>Student</a>
                <a href="teacher" class="group flex items-center rounded-lg px-4 py-3 text-gray-300 hover:bg-gray-800 hover:text-white"><i class="fas fa-chalkboard-teacher mr-3"></i>Teacher</a>
                <a href="issue_certificate" class="group flex items-center rounded-lg bg-indigo-600 px-4 py-3 text-white"><i class="fas fa-certificate mr-3"></i>Issue Certificate</a>
                <a href="verify_certificate" class="group flex items-center rounded-lg px-4 py-3 text-gray-300 hover:bg-gray-800 hover:text-white"><i class="fas fa-certificate mr-3"></i>Verify Certificate</a>
            </nav>
            <div class="absolute bottom-0 w-full p-4">
                <a href="../logout" class="group flex items-center rounded-lg px-4 py-3 text-gray-300 hover:bg-red-700 hover:text-white"><i class="fas fa-sign-out-alt mr-3"></i>Logout</a>
            </div>
        </div>

        <div class="flex-1 overflow-y-auto md:ml-64">
            <header class="bg-white p-4 shadow">
                <h1 class="text-2xl font-bold">Issue New Certificate</h1>
            </header>

            <main class="p-6">
                <div class="bg-white p-6 rounded shadow">

                    <?php if ($message): ?>
                        <div class="bg-green-100 p-4 mb-4 text-green-700"><?= $message ?></div>
                    <?php endif; ?>

                    <?php if ($error): ?>
                        <div class="bg-red-100 p-4 mb-4 text-red-700"><?= $error ?></div>
                    <?php endif; ?>

                    <form method="post" enctype="multipart/form-data">
                        <input type="hidden" name="csrf" value="<?= $_SESSION['csrf'] ?>">

                        <label class="block mb-2">Student</label>
                        <select name="student_id" class="w-full p-2 border mb-4" required>
                            <option value="">-- Select --</option>
                            <?php foreach ($students as $student): ?>
                                <option value="<?= $student['student_id'] ?>">
                                    <?= htmlspecialchars($student['first_name'].' '.$student['last_name'].' ('.$student['register_no'].')') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <label class="block mb-2">Upload Certificate</label>
                        <input type="file" name="cert_file" class="w-full mb-4" required>

                        <button type="submit" name="issue_certificate"
                            class="bg-indigo-600 text-white px-6 py-2 rounded">
                            Generate & Issue Certificate
                        </button>
                    </form>

                </div>
            </main>
        </div>
    </div>
</body>
</html>
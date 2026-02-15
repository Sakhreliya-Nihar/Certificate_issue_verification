<?php
session_start();
include 'db.php';

// Verify user is logged in as a student
if (!isset($_SESSION['user_id']) || strtolower($_SESSION['role']) !== 'student') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$certificates = [];
$student_name = "Student"; // Default name

// Get the student_id from the user_id
$stmt = $conn->prepare("SELECT student_id, first_name, last_name FROM student WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $student_data = $result->fetch_assoc();
    $student_id = $student_data['student_id'];
    $student_name = htmlspecialchars($student_data['first_name']);

    // Fetch certificates for this student
    // UPDATED QUERY: Added 'certificate_file' to the SELECT list
    $cert_stmt = $conn->prepare("SELECT code, status, issue_date, certificate_file FROM certificates WHERE student_id = ? ORDER BY issue_date DESC");
    $cert_stmt->bind_param("i", $student_id);
    $cert_stmt->execute();
    $cert_result = $cert_stmt->get_result();
    if ($cert_result) {
        $certificates = $cert_result->fetch_all(MYSQLI_ASSOC);
    }
    $cert_stmt->close();
} else {
    $certificates = [];
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - My Certificates</title>
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
                <h3 class="text-xl font-semibold text-white"><?= $student_name ?></h3>
                <span class="text-sm text-gray-400">Student Portal</span>
            </div>
            <nav class="flex flex-col space-y-2">
                <a href="index.php" class="group flex items-center rounded-lg px-4 py-3 text-gray-300 transition-colors hover:bg-gray-800 hover:text-white"><i class="fas fa-tachometer-alt mr-3 w-5 text-center"></i><span class="font-medium">Dashboard</span></a>
                <a href="view_my_certificates.php" class="group flex items-center rounded-lg bg-indigo-600 px-4 py-3 text-white transition-colors hover:bg-indigo-700"><i class="fas fa-certificate mr-3 w-5 text-center"></i><span class="font-medium">My Certificates</span></a>
            </nav>
            <div class="absolute bottom-0 left-0 w-full p-4">
                 <a href="../login.php" class="group flex items-center rounded-lg px-4 py-3 text-gray-300 transition-colors hover:bg-red-700 hover:text-white"><i class="fas fa-sign-out-alt mr-3 w-5 text-center"></i><span class="font-medium">Logout</span></a>
            </div>
        </div>

        <div class="flex-1 overflow-y-auto md:ml-64">
            <header class="sticky top-0 z-20 flex items-center justify-between border-b border-gray-200 bg-white p-4 shadow-sm">
                <div class="flex items-center">
                    <button id="sidebar-toggle" class="mr-4 rounded-lg p-2 text-gray-600 hover:bg-gray-100 md:hidden"><i class="fas fa-bars text-xl"></i></button>
                    <h1 class="text-2xl font-bold text-gray-800">My Certificates</h1>
                </div>
                <a href="../login.php" class="hidden rounded-lg bg-red-600 px-5 py-2.5 text-sm font-medium text-white shadow-md transition-colors hover:bg-red-700 md:block">Logout</a>
            </header>

            <main class="p-6 md:p-8">
                <div class="rounded-xl bg-white p-6 shadow-lg">
                    <h2 class="mb-6 text-2xl font-semibold text-gray-700">Issued Certificates</h2>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Certificate Code</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Date of Issue</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                <?php if (empty($certificates)): ?>
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">You have not been issued any certificates yet.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($certificates as $cert): ?>
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?= htmlspecialchars($cert['code']) ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                <span class="inline-flex rounded-full px-2 text-xs font-semibold leading-5 <?= $cert['status'] == 'issued' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                                                    <?= htmlspecialchars(ucfirst($cert['status'])) ?>
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= date('M j, Y', strtotime($cert['issue_date'])) ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <a href="../verify.php?id=<?= htmlspecialchars($cert['code']) ?>" target="_blank" class="text-indigo-600 hover:text-indigo-900 mr-4">
                                                    <i class="fas fa-check-circle"></i> Verify
                                                </a>

                                                <?php if (!empty($cert['certificate_file'])): ?>
                                                    <a href="<?= htmlspecialchars($cert['certificate_file']) ?>" download class="text-green-600 hover:text-green-900">
                                                        <i class="fas fa-download"></i> Download
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        document.getElementById('sidebar-toggle').addEventListener('click', function() {
            var sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('-translate-x-full');
        });
    </script>
</body>
</html>
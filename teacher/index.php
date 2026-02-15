<?php
// FIX: Uncommented this block to fetch real data from the database.
include 'db.php'; 

// Query for total students
$sql_students = "SELECT COUNT(student_id) as total_students FROM student";
$result_students = $conn->query($sql_students);

if ($result_students && $result_students->num_rows > 0) {
  $row = $result_students->fetch_assoc();
  $total_students = $row['total_students'];
} else {
  $total_students = 0; // Default to 0 if query fails
}

// Query for total teachers
$sql_teachers = "SELECT COUNT(teacher_id) as total_teachers FROM teacher";
$result_teachers = $conn->query($sql_teachers);

if ($result_teachers && $result_teachers->num_rows > 0) {
  $row = $result_teachers->fetch_assoc();
  $total_teachers = $row['total_teachers'];
} else {
  $total_teachers = 0; // Default to 0 if query fails
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    
    <style>
        /* Custom font */
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        body {
            font-family: 'Inter', sans-serif;
        }
        /* Style for the announcement link */
        .announcement-link {
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .announcement-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }
    </style>
</head>
<body class="bg-gray-100">

    <div class="flex h-screen">
        <div id="sidebar" class="fixed inset-y-0 left-0 z-30 w-64 transform -translate-x-full overflow-y-auto bg-gray-900 p-4 transition-transform duration-300 ease-in-out md:translate-x-0">
            
            <div class="mb-8 flex flex-col items-center">
                <img src="avatar.png" alt="Avatar" class="mb-3 h-24 w-24 rounded-full border-4 border-gray-700">
                <h3 class="text-xl font-semibold text-white">Teacher</h3>
                <span class="text-sm text-gray-400">Admin</span>
            </div>

            <nav class="flex flex-col space-y-2">
                <a href="#" class="group flex items-center rounded-lg bg-indigo-600 px-4 py-3 text-white transition-colors hover:bg-indigo-700">
                    <i class="fas fa-tachometer-alt mr-3 w-5 text-center"></i>
                    <span class="font-medium">Dashboard</span>
                </a>
                <a href="class.php" class="group flex items-center rounded-lg px-4 py-3 text-gray-300 transition-colors hover:bg-gray-800 hover:text-white">
                    <i class="fas fa-users mr-3 w-5 text-center"></i>
                    <span class="font-medium">Class</span>
                </a>
                <a href="student.html" class="group flex items-center rounded-lg px-4 py-3 text-gray-300 transition-colors hover:bg-gray-800 hover:text-white">
                    <i class="fas fa-user-graduate mr-3 w-5 text-center"></i>
                    <span class="font-medium">Student</span>
                </a>

                <a href="issue_certificate.php" class="group flex items-center rounded-lg px-4 py-3 text-gray-300 transition-colors hover:bg-gray-800 hover:text-white">
                    <i class="fas fa-certificate mr-3 w-5 text-center"></i>
                    <span class="font-medium">Issue Certificate</span>
                </a>
            </nav>

            <div class="absolute bottom-0 left-0 w-full p-4">
                 <a href="../login.php" class="group flex items-center rounded-lg px-4 py-3 text-gray-300 transition-colors hover:bg-red-700 hover:text-white">
                    <i class="fas fa-sign-out-alt mr-3 w-5 text-center"></i>
                    <span class="font-medium">Logout</span>
                </a>
            </div>
        </div>

        <div class="flex-1 overflow-y-auto md:ml-64">
            
            <header class="sticky top-0 z-20 flex items-center justify-between border-b border-gray-200 bg-white p-4 shadow-sm">
                <div class="flex items-center">
                    <button id="sidebar-toggle" class="mr-4 rounded-lg p-2 text-gray-600 hover:bg-gray-100 md:hidden">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <h1 class="text-2xl font-bold text-gray-800">Dashboard</h1>
                </div>
                
                <a href="../login.php" class="hidden rounded-lg bg-red-600 px-5 py-2.5 text-sm font-medium text-white shadow-md transition-colors hover:bg-red-700 md:block">
                    Logout
                </a>
            </header>

            <main class="p-6 md:p-8">
                <h2 class="mb-6 text-3xl font-semibold text-gray-700">Welcome to the Dashboard</h2>

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-2">
                    
                    <div class="flex items-center rounded-xl bg-white p-6 shadow-lg">
                        <div class="mr-6 flex h-16 w-16 flex-shrink-0 items-center justify-center rounded-full bg-blue-100 text-blue-600">
                            <i class="fas fa-user-graduate fa-2x"></i>
                        </div>
                        <div>
                            <span class="block text-sm font-medium text-gray-500">Total Students</span>
                            <span class="block text-4xl font-bold text-gray-800">
                                <?php echo $total_students; ?>
                            </span>
                        </div>
                    </div>

                    <div class="flex items-center rounded-xl bg-white p-6 shadow-lg">
                        <div class="mr-6 flex h-16 w-16 flex-shrink-0 items-center justify-center rounded-full bg-green-100 text-green-600">
                            <i class="fas fa-chalkboard-teacher fa-2x"></i>
                        </div>
                        <div>
                            <span class="block text-sm font-medium text-gray-500">Total Teachers</span>
                            <span class="block text-4xl font-bold text-gray-800">
                                <?php echo $total_teachers; ?>
                            </span>
                        </div>
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
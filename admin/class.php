<?php
include 'db.php';

$total_students_by_year = array();

// We can just loop through the Roman numerals directly
$roman_numerals = array('I', 'II', 'III', 'IV', 'V');

foreach ($roman_numerals as $roman) {
    // This now correctly queries the `year` column for the Roman numeral
    // Use a prepared statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT COUNT(student_id) as total_students FROM student WHERE `year` = ?");
    $stmt->bind_param("s", $roman);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $total_students_by_year[$roman] = $row['total_students'];
    } else {
        $total_students_by_year[$roman] = 0;
    }
    $stmt->close();
}
$conn->close();

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Classes</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    
    <style>
        /* Custom font */
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        body {
            font-family: 'Inter', sans-serif;
        }
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
                <a href="index.php" class="group flex items-center rounded-lg px-4 py-3 text-gray-300 transition-colors hover:bg-gray-800 hover:text-white">
                    <i class="fas fa-tachometer-alt mr-3 w-5 text-center"></i>
                    <span class="font-medium">Dashboard</span>
                </a>
                <a href="class.php" class="group flex items-center rounded-lg bg-indigo-600 px-4 py-3 text-white transition-colors hover:bg-indigo-700"> <i class="fas fa-users mr-3 w-5 text-center"></i>
                    <span class="font-medium">Class</span>
                </a>
                <a href="student.html" class="group flex items-center rounded-lg px-4 py-3 text-gray-300 transition-colors hover:bg-gray-800 hover:text-white">
                    <i class="fas fa-user-graduate mr-3 w-5 text-center"></i>
                    <span class="font-medium">Student</span>
                </a>
                <a href="teacher.html" class="group flex items-center rounded-lg px-4 py-3 text-gray-300 transition-colors hover:bg-gray-800 hover:text-white">
                    <i class="fas fa-chalkboard-teacher mr-3 w-5 text-center"></i>
                    <span class="font-medium">Teacher</span>
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
                    <h1 class="text-2xl font-bold text-gray-800">Classes</h1>
                </div>
                
                <a href="../login.php" class="hidden rounded-lg bg-red-600 px-5 py-2.5 text-sm font-medium text-white shadow-md transition-colors hover:bg-red-700 md:block">
                    Logout
                </a>
            </header>

            <main class="p-6 md:p-8">
                <h2 class="mb-6 text-3xl font-semibold text-gray-700">View Students by Year</h2>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    
                    <a href="1st_year_students.php" class="block rounded-xl p-6 text-white shadow-lg transition-transform hover:scale-105" style="background: linear-gradient(to right, #f4611d, #fc8d59f4);">
                        <div class="flex flex-col items-center text-center">
                            <i class="fas fa-graduation-cap fa-3x mb-4"></i>
                            <h3 class="text-2xl font-bold">1st Year</h3>
                            <p class="mt-2 text-lg font-medium">Total Students: <?php echo $total_students_by_year['I']; ?></p>
                        </div>
                    </a>

                    <a href="2nd_year_students.php" class="block rounded-xl p-6 text-white shadow-lg transition-transform hover:scale-105" style="background: linear-gradient(to right, #8338ec, #a36cf1);">
                        <div class="flex flex-col items-center text-center">
                            <i class="fas fa-graduation-cap fa-3x mb-4"></i>
                            <h3 class="text-2xl font-bold">2nd Year</h3>
                            <p class="mt-2 text-lg font-medium">Total Students: <?php echo $total_students_by_year['II']; ?></p>
                        </div>
                    </a>

                    <a href="3rd_year_students.php" class="block rounded-xl p-6 text-white shadow-lg transition-transform hover:scale-105" style="background: linear-gradient(to right, #3a86ff, #699ff5);">
                        <div class="flex flex-col items-center text-center">
                            <i class="fas fa-graduation-cap fa-3x mb-4"></i>
                            <h3 class="text-2xl font-bold">3rd Year</h3>
                            <p class="mt-2 text-lg font-medium">Total Students: <?php echo $total_students_by_year['III']; ?></p>
                        </div>
                    </a>

                    <a href="4th_year_students.php" class="block rounded-xl p-6 text-white shadow-lg transition-transform hover:scale-105" style="background: linear-gradient(to right, #0be881, #4ff3a7);">
                        <div class="flex flex-col items-center text-center">
                            <i class="fas fa-graduation-cap fa-3x mb-4"></i>
                            <h3 class="text-2xl font-bold">4th Year</h3>
                            <p class="mt-2 text-lg font-medium">Total Students: <?php echo $total_students_by_year['IV']; ?></p>
                        </div>
                    </a>

                    <a href="5th_year_students.php" class="block rounded-xl p-6 text-white shadow-lg transition-transform hover:scale-105" style="background: linear-gradient(to right, #ff006e, #f05b9c);">
                        <div class="flex flex-col items-center text-center">
                            <i class="fas fa-graduation-cap fa-3x mb-4"></i>
                            <h3 class="text-2xl font-bold">5th Year</h3>
                            <p class="mt-2 text-lg font-medium">Total Students: <?php echo $total_students_by_year['V']; ?></p>
                        </div>
                    </a>

                </div> </main>
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
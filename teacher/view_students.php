<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - View Students</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    
    <style>
        /* Custom font */
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        body {
            font-family: 'Inter', sans-serif;
        }
        
        /* Styles for the dynamically loaded modal content */
        .student-details-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .student-details-table th, .student-details-table td {
            padding: 8px 12px;
            border: 1px solid #ddd;
            text-align: left;
        }
        .student-details-table th {
            background-color: #f2f2f2;
        }
        .student-details-table td img {
            max-width: 100px;
            max-height: 100px;
            display: block;
            margin: 0 auto;
        }
        /* Style for the close button on the details modal */
        .close {
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 24px;
            font-weight: bold;
            cursor: pointer;
            color: #888;
        }
        .close:hover {
            color: #000;
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
                <a href="index.php" class="group flex items-center rounded-lg px-4 py-3 text-gray-300 transition-colors hover:bg-gray-800 hover:text-white">
                    <i class="fas fa-tachometer-alt mr-3 w-5 text-center"></i>
                    <span class="font-medium">Dashboard</span>
                </a>
                <a href="class.php" class="group flex items-center rounded-lg px-4 py-3 text-gray-300 transition-colors hover:bg-gray-800 hover:text-white">
                    <i class="fas fa-users mr-3 w-5 text-center"></i>
                    <span class="font-medium">Class</span>
                </a>
                <a href="student.html" class="group flex items-center rounded-lg bg-indigo-600 px-4 py-3 text-white transition-colors hover:bg-indigo-700"> <i class="fas fa-user-graduate mr-3 w-5 text-center"></i>
                    <span class="font-medium">Student</span>
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
                    <h1 class="text-2xl font-bold text-gray-800">All Students</h1>
                </div>
                
                <a href="../login.php" class="hidden rounded-lg bg-red-600 px-5 py-2.5 text-sm font-medium text-white shadow-md transition-colors hover:bg-red-700 md:block">
                    Logout
                </a>
            </header>

            <div id="studentDetailsContent" class="p-6 md:p-8">
                
                <div id="detailsModal" class="relative overflow-x-auto bg-white p-6 shadow-lg rounded-xl">
                    
                    <div class="flex flex-col md:flex-row justify-between items-center mb-4">
                        <form onsubmit="searchStudents(); return false;" class="flex-grow md:mr-4 mb-2 md:mb-0">
                            <div class="relative">
                                <input type="text" id="searchInput" placeholder="Search for names..." class="w-full rounded-lg border border-gray-300 p-2.5 pl-10 text-sm text-gray-900 focus:border-indigo-500 focus:ring-indigo-500">
                                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            </div>
                        </form>
                        
                        <button onclick="exportData()" class="w-full md:w-auto rounded-lg bg-green-600 px-5 py-2.5 text-sm font-medium text-white shadow-md transition-colors hover:bg-green-700">
                            <i class="fas fa-file-excel mr-2"></i>Export Data
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full min-w-max text-left text-sm text-gray-700">
                            <thead class="bg-gray-100 text-xs uppercase text-gray-700">
                                <tr>
                                    <th scope="col" class="px-6 py-3">S.No</th>
                                    <th scope="col" class="px-6 py-3">Name</th>
                                    <th scope="col" class="px-6 py-3">Register No</th>
                                    <th scope="col" class="px-6 py-3">Roll No</th>
                                    <th scope="col" class="px-6 py-3">Mail ID</th>
                                    <th scope="col" class="px-6 py-3">Year</th>
                                    <th scope="col" class="px-6 py-3 text-center">Details</th>
                                    <th scope="col" class="px-6 py-3 text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    include 'db.php';
                                    
                                    // Array to convert Roman numerals to numbers
                                    $roman_to_arabic = array(
                                        'I' => 1, 'II' => 2, 'III' => 3, 'IV' => 4, 'V' => 5
                                    );
                                    
                                    // SQL Query for ALL students
                                    $sql = "SELECT * FROM student";
                                    
                                    $result = $conn->query($sql);
                        
                                    if ($result && $result->num_rows > 0) {
                                        $count = 1;
                                        while($row = $result->fetch_assoc()) {
                                            
                                            // Convert Roman year to number
                                            $roman_year = $row['year'];
                                            $arabic_year = $roman_to_arabic[$roman_year] ?? $roman_year;
                                            
                                            echo "<tr class='border-b bg-white hover:bg-gray-50'>";
                                            echo "<td class='px-6 py-4 font-medium text-gray-900'>".$count."</td>";
                                            echo "<td class='px-6 py-4'>".$row['first_name']." ".$row['last_name']."</td>";
                                            echo "<td class='px-6 py-4'>".$row['register_no']."</td>";
                                            echo "<td class='px-6 py-4'>".$row['roll_no']."</td>";
                                            echo "<td class='px-6 py-4'>".$row['email']."</td>";
                                            echo "<td class='px-6 py-4'>". $arabic_year ."</td>"; // Display the number
                                            
                                            // Action Buttons
                                            echo "<td class='px-6 py-4 text-center'>
                                                    <button onclick='viewDetails(".$row['student_id'].")' class='text-indigo-600 hover:text-indigo-900' title='View Details'>
                                                        <i class='fas fa-eye fa-lg'></i>
                                                    </button>
                                                  </td>";
                                            echo "<td class='px-6 py-4 text-center'>
                                                    <button onclick='deleteStudent(".$row['student_id'].")' class='text-red-600 hover:text-red-900' title='Delete Student'>
                                                        <i class='fas fa-trash fa-lg'></i>
                                                    </button>
                                                  </td>";
                                            echo "</tr>";
                                            $count++;
                                        }
                                    } else {
                                        echo "<tr class='border-b bg-white'><td colspan='8' class='px-6 py-4 text-center text-gray-500'>No students found</td></tr>";
                                    }
                        
                                    $conn->close();
                                ?>
                            </tbody>
                        </table>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>

    <script>
        // Sidebar Toggle
        document.getElementById('sidebar-toggle').addEventListener('click', function() {
            var sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('-translate-x-full');
        });

        // Function to view student details (loads new content via AJAX)
        function viewDetails(student_id) {
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function () {
                if (this.readyState == 4 && this.status == 200) {
                    var modalContent = document.getElementById("studentDetailsContent");
                    // We wrap the response in the Tailwind UI structure
                    modalContent.innerHTML = `
                        <div id="detailsModal" class="relative bg-white p-6 shadow-lg rounded-xl">
                            ${this.responseText}
                        </div>
                    `;
                }
            };
            // This MUST point to your existing PHP file
            xhttp.open("GET", "get_student_details.php?student_id=" + student_id, true);
            xhttp.send();
        }

        // Function to close the details view and reload the table
        function closeDetailsModal() {
            location.reload();
        }

        // Function to delete a student
        function deleteStudent(student_id) {
            var confirmation = confirm("Are you sure you want to delete this student?");
            
            if (confirmation) {
                var xhttp = new XMLHttpRequest();
                xhttp.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        if (this.responseText.trim() === 'success') {
                            alert('Student deleted successfully.');
                            location.reload();
                        } else {
                            alert('Error deleting student.');
                        }
                    }
                };
                xhttp.open("GET", "delete_student.php?student_id=" + student_id, true);
                xhttp.send();
            }
        }

        // Function to search the table
        function searchStudents() {
            var input, filter, table, tr, td, i, txtValue;
            input = document.getElementById("searchInput");
            filter = input.value.toUpperCase();
            table = document.querySelector("table"); // Find the first table on the page
            tr = table.getElementsByTagName("tr");

            // Loop through all table rows, and hide those who don't match the search query
            for (i = 1; i < tr.length; i++) { // Start from 1 to skip the header row
                td = tr[i].getElementsByTagName("td")[1]; // Column 1 is the 'Name' column
                if (td) {
                    txtValue = td.textContent || td.innerText;
                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                        tr[i].style.display = "";
                    } else {
                        tr[i].style.display = "none";
                    }
                }
            }
        }

        // Function to export data
        function exportData() {
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    var blob = new Blob([this.responseText], {type: 'application/vnd.ms-excel'});
                    var url = URL.createObjectURL(blob);
                    var a = document.createElement('a');
                    a.href = url;
                    a.download = 'student_data.xls';
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                }
            };
            xhttp.open("GET", "export_data.php", true);
            xhttp.send();
        }
    </script>

</body>
</html>
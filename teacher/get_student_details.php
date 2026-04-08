<?php
session_start();

// SECURITY CHECK: Prevent direct unauthorized access via the URL
if (!isset($_SESSION['user_id']) || strtolower($_SESSION['role']) !== 'teacher') {
    echo '<h2 class="text-2xl font-bold text-red-600 mb-4">Unauthorized Access</h2>';
    exit();
}

include 'db.php';

// Use prepared statements to prevent SQL injection
$student_id = $_GET['student_id'];
$stmt = $conn->prepare("SELECT * FROM student WHERE student_id = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();

// Add the "close" button
echo '<button class="close" onclick="closeDetailsModal()">&times;</button>';

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    
    // XSS PROTECTION: Sanitize all outputs before echoing them into HTML
    $firstName = htmlspecialchars($row['first_name'] ?? '');
    $lastName  = htmlspecialchars($row['last_name'] ?? '');
    $regNo     = htmlspecialchars($row['register_no'] ?? '');
    $rollNo    = htmlspecialchars($row['roll_no'] ?? '');
    $email     = htmlspecialchars($row['email'] ?? '');
    $phone     = htmlspecialchars($row['phone'] ?? '');
    $address   = htmlspecialchars($row['address'] ?? '');
    $father    = htmlspecialchars($row['father'] ?? '');
    $mother    = htmlspecialchars($row['mother'] ?? '');
    $aadhar    = htmlspecialchars($row['aadhar'] ?? '');
    $dob       = htmlspecialchars($row['dob'] ?? '');
    $gender    = htmlspecialchars($row['gender'] ?? '');
    $dist      = htmlspecialchars($row['dist'] ?? '');
    $pincode   = htmlspecialchars($row['pincode'] ?? '');
    $uploaded  = htmlspecialchars($row['uploaded'] ?? '');
    
    $html = '<h2 class="text-2xl font-bold text-gray-800 mb-4">Student Details</h2>';
    
    $html .= '<table class="student-details-table">';
    
    // --- MOVED IMAGE PATH LOGIC TO THE TOP ---
    // The path in the database is already '../uploads/filename.png', which is correct relative to this file.
    $imagePath = $row['image'] ?? '';
    
    // We must replace the spaces with %20 so the browser reads the full URL!
    $imageUrl = str_replace(' ', '%20', $imagePath);
    
    if (!empty($imagePath) && file_exists($imagePath)) {
        // We use $imageUrl here so the HTML tag doesn't break on spaces, and changed label to "Photo"
        $html .= '<tr><td class="font-semibold align-middle">Photo:</td><td><img src="' . htmlspecialchars($imageUrl) . '" alt="Student Photo" class="w-32 h-32 object-cover rounded-lg border border-gray-300 shadow-sm"></td></tr>';
    } else {
        // Shows the exact path it tried to find so you can debug if the physical file is missing
        $html .= '<tr><td class="font-semibold text-gray-500">Photo:</td><td class="text-red-500 italic">Photo not found at: ' . htmlspecialchars($imagePath) . '</td></tr>';
    }
    
    // Continue with the rest of the student details
    $html .= '<tr><td class="font-semibold">Name:</td><td>' . $firstName . " " . $lastName . '</td></tr>';
    $html .= '<tr><td class="font-semibold">Register No:</td><td>' . $regNo . '</td></tr>';
    $html .= '<tr><td class="font-semibold">Roll No:</td><td>' . $rollNo . '</td></tr>';
    $html .= '<tr><td class="font-semibold">Email:</td><td>' . $email . '</td></tr>';
    $html .= '<tr><td class="font-semibold">Phone:</td><td>' . $phone . '</td></tr>';
    $html .= '<tr><td class="font-semibold">Address:</td><td>' . $address . '</td></tr>';
    $html .= '<tr><td class="font-semibold">Father\'s Name:</td><td>' . $father . '</td></tr>';
    $html .= '<tr><td class="font-semibold">Mother\'s Name:</td><td>' . $mother . '</td></tr>';
    $html .= '<tr><td class="font-semibold">Aadhar:</td><td>' . $aadhar . '</td></tr>';
    $html .= '<tr><td class="font-semibold">Date of Birth:</td><td>' . $dob . '</td></tr>';
    $html .= '<tr><td class="font-semibold">Gender:</td><td>' . $gender . '</td></tr>';
    $html .= '<tr><td class="font-semibold">District:</td><td>' . $dist . '</td></tr>';
    $html .= '<tr><td class="font-semibold">Pincode:</td><td>' . $pincode . '</td></tr>';
    $html .= '<tr><td class="font-semibold">Uploaded:</td><td>' . $uploaded . '</td></tr>';
    $html .= '</table>';

    // --- ADDED ACTION BUTTONS ---
    $html .= '<div class="mt-6 flex justify-end space-x-4">';
    
    // DELETE BUTTON
    $html .= '<button onclick="deleteStudent(' . (int)$student_id . ')" class="rounded-lg bg-red-600 px-5 py-2.5 text-sm font-medium text-white shadow-md transition-colors hover:bg-red-700">
                <i class="fas fa-trash mr-2"></i>Delete Student
              </button>';
               
    $html .= '</div>';
    // --- END OF BUTTONS ---

    echo $html;
} else {
    echo '<h2 class="text-2xl font-bold text-gray-800 mb-4">Error</h2>';
    echo 'No student found with that ID.';
}

$stmt->close();
$conn->close();
?>
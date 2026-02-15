<?php
include 'db.php';

// Use prepared statements to prevent SQL injection
$student_id = $_GET['student_id'];
$stmt = $conn->prepare("SELECT * FROM student WHERE student_id = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();

// Add the "close" button
// The styling for ".close" is already in the parent page (view_students.php, etc.)
echo '<button class="close" onclick="closeDetailsModal()">&times;</button>';

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    
    // Add Tailwind classes to the title
    $html = '<h2 class="text-2xl font-bold text-gray-800 mb-4">Student Details</h2>';
    
    // The styling for ".student-details-table" is in the parent page
    $html .= '<table class="student-details-table">';
    
    // Use null coalescing (?? '') to prevent errors if a field is empty in the database
    $html .= '<tr><td class="font-semibold">Name:</td><td>' . ($row['first_name'] ?? '') . " " . ($row['last_name'] ?? '') . '</td></tr>';
    $html .= '<tr><td class="font-semibold">Register No:</td><td>' . ($row['register_no'] ?? '') . '</td></tr>';
    $html .= '<tr><td class="font-semibold">Roll No:</td><td>' . ($row['roll_no'] ?? '') . '</td></tr>';
    $html .= '<tr><td class="font-semibold">Email:</td><td>' . ($row['email'] ?? '') . '</td></tr>';
    $html .= '<tr><td class="font-semibold">Phone:</td><td>' . ($row['phone'] ?? '') . '</td></tr>';
    $html .= '<tr><td class="font-semibold">Address:</td><td>' . ($row['address'] ?? '') . '</td></tr>';
    $html .= '<tr><td class="font-semibold">Father\'s Name:</td><td>' . ($row['father'] ?? '') . '</td></tr>';
    $html .= '<tr><td class="font-semibold">Mother\'s Name:</td><td>' . ($row['mother'] ?? '') . '</td></tr>';
    $html .= '<tr><td class="font-semibold">Aadhar:</td><td>' . ($row['aadhar'] ?? '') . '</td></tr>';
    $html .= '<tr><td class="font-semibold">Date of Birth:</td><td>' . ($row['dob'] ?? '') . '</td></tr>';
    $html .= '<tr><td class="font-semibold">Gender:</td><td>' . ($row['gender'] ?? '') . '</td></tr>';
    $html .= '<tr><td class="font-semibold">District:</td><td>' . ($row['dist'] ?? '') . '</td></tr>';
    $html .= '<tr><td class="font-semibold">Pincode:</td><td>' . ($row['pincode'] ?? '') . '</td></tr>';
    
    // Check if image path exists and build the correct relative path
    $imagePath = $row['image'] ?? '';
    // The image path is stored like 'uploads/image.png'
    // This file is in 'teacher/', so we must go 'up' one level
    $correctImagePath = "../uploads" . $imagePath; 
    
    if (!empty($imagePath) && file_exists($correctImagePath)) {
        $html .= '<tr><td class="font-semibold">Image:</td><td><img src="' . htmlspecialchars($correctImagePath) . '" alt="Student Image"></td></tr>';
    } else {
        $html .= '<tr><td class="font-semibold">Image:</td><td>Image not found</td></tr>';
    }
    
    $html .= '<tr><td class="font-semibold">Uploaded:</td><td>' . ($row['uploaded'] ?? '') . '</td></tr>';
    $html .= '</table>';

    // --- NEWLY ADDED BUTTONS ---
    $html .= '<div class="mt-6 flex justify-end space-x-4">';
    
    // DELETE BUTTON
    $html .= '<button onclick="deleteStudent(' . $student_id . ')" class="rounded-lg bg-red-600 px-5 py-2.5 text-sm font-medium text-white shadow-md transition-colors hover:bg-red-700">
                <i class="fas fa-trash mr-2"></i>Delete Student
              </button>';
              
    // (Optional) You can add an "Edit" button here later if you create an edit_student.php page
    // $html .= '<button onclick="editStudent(' . $student_id . ')" class="rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-medium text-white shadow-md transition-colors hover:bg-indigo-700">
    //             <i class="fas fa-pen mr-2"></i>Edit
    //           </button>';
               
    $html .= '</div>';
    // --- END OF NEW BUTTONS ---

    echo $html;
} else {
    echo '<h2 class="text-2xl font-bold text-gray-800 mb-4">Error</h2>';
    echo 'No student found with that ID.';
}

$stmt->close();
$conn->close();
?>
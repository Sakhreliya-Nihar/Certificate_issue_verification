<?php
include 'db.php';

// Use prepared statements to prevent SQL injection
$teacher_id = $_GET['teacher_id'];
$stmt = $conn->prepare("SELECT * FROM teacher WHERE teacher_id = ?");
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$result = $stmt->get_result();

// Add the "close" button
// The styling for ".close" is already in the parent page (view_teachers.php)
echo '<button class="close" onclick="closeDetailsModal()">&times;</button>';

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    
    // Add Tailwind classes to the title
    $html = '<h2 class="text-2xl font-bold text-gray-800 mb-4">Teacher Details</h2>';
    
    // The styling for ".student-details-table" is in the parent page
    $html .= '<table class="student-details-table">';
    
    // Use null coalescing (?? '') to prevent errors if a field is empty
    $html .= '<tr><td class="font-semibold">Name:</td><td>' . ($row['first_name'] ?? '') . " " . ($row['last_name'] ?? '') . '</td></tr>';
    $html .= '<tr><td class="font-semibold">Register No:</td><td>' . ($row['reg_no'] ?? '') . '</td></tr>';
    $html .= '<tr><td class="font-semibold">Email:</td><td>' . ($row['email'] ?? '') . '</td></tr>';
    $html .= '<tr><td class="font-semibold">Phone:</td><td>' . ($row['phone_number'] ?? '') . '</td></tr>';
    $html .= '<tr><td class="font-semibold">Address:</td><td>' . ($row['address'] ?? '') . '</td></tr>';                            
    $html .= '</table>';

    // --- ADDED ACTION BUTTONS ---
    $html .= '<div class="mt-6 flex justify-end space-x-4">';
    
    // DELETE BUTTON
    $html .= '<button onclick="deleteTeacher(' . $teacher_id . ')" class="rounded-lg bg-red-600 px-5 py-2.5 text-sm font-medium text-white shadow-md transition-colors hover:bg-red-700">
                <i class="fas fa-trash mr-2"></i>Delete Teacher
              </button>';
               
    $html .= '</div>';
    // --- END OF BUTTONS ---

    echo $html;
} else {
    echo '<h2 class="text-2xl font-bold text-gray-800 mb-4">Error</h2>';
    echo 'No teacher found with that ID.';
}

$stmt->close();
$conn->close();
?>
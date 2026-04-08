<?php
session_start();

// SECURITY CHECK
if (!isset($_SESSION['user_id']) || strtolower($_SESSION['role']) !== 'admin') {
    echo '<h2 class="text-2xl font-bold text-red-600 mb-4">Unauthorized Access</h2>';
    exit();
}

include 'db.php';

$teacher_id = $_GET['teacher_id'];
$stmt = $conn->prepare("SELECT * FROM teacher WHERE teacher_id = ?");
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$result = $stmt->get_result();

echo '<button class="close" onclick="closeDetailsModal()">&times;</button>';

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    
    // XSS PROTECTION
    $firstName = htmlspecialchars($row['first_name'] ?? '');
    $lastName  = htmlspecialchars($row['last_name'] ?? '');
    $regNo     = htmlspecialchars($row['reg_no'] ?? '');
    $email     = htmlspecialchars($row['email'] ?? '');
    $phone     = htmlspecialchars($row['phone_number'] ?? '');
    $address   = htmlspecialchars($row['address'] ?? '');
    
    $html = '<h2 class="text-2xl font-bold text-gray-800 mb-4">Teacher Details</h2>';
    $html .= '<table class="student-details-table">';
    
    // --- IMAGE PATH LOGIC ---
    $imagePath = $row['image'] ?? '';
    $imageUrl = str_replace(' ', '%20', $imagePath);
    
    if (!empty($imagePath) && file_exists($imagePath)) {
        $html .= '<tr><td class="font-semibold align-middle">Photo:</td><td><img src="' . htmlspecialchars($imageUrl) . '" alt="Teacher Photo" class="w-32 h-32 object-cover rounded-lg border border-gray-300 shadow-sm"></td></tr>';
    } else {
        $html .= '<tr><td class="font-semibold text-gray-500">Photo:</td><td class="text-red-500 italic">No photo found</td></tr>';
    }
    
    $html .= '<tr><td class="font-semibold">Name:</td><td>' . $firstName . " " . $lastName . '</td></tr>';
    $html .= '<tr><td class="font-semibold">Register No:</td><td>' . $regNo . '</td></tr>';
    $html .= '<tr><td class="font-semibold">Email:</td><td>' . $email . '</td></tr>';
    $html .= '<tr><td class="font-semibold">Phone:</td><td>' . $phone . '</td></tr>';
    $html .= '<tr><td class="font-semibold">Address:</td><td>' . $address . '</td></tr>';                            
    $html .= '</table>';

    // ACTION BUTTONS
    $html .= '<div class="mt-6 flex justify-end space-x-4">';
    $html .= '<button onclick="deleteTeacher(' . (int)$teacher_id . ')" class="rounded-lg bg-red-600 px-5 py-2.5 text-sm font-medium text-white shadow-md transition-colors hover:bg-red-700">
                <i class="fas fa-trash mr-2"></i>Delete Teacher
              </button>';
    $html .= '</div>';

    echo $html;
} else {
    echo '<h2 class="text-2xl font-bold text-gray-800 mb-4">Error</h2>';
    echo 'No teacher found with that ID.';
}

$stmt->close();
$conn->close();
?>
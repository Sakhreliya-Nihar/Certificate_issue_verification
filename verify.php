<?php
include 'db.php';
$certificate = null;
$error_message = '';

// Check if a form was submitted or if an ID is passed in the URL
if ($_SERVER["REQUEST_METHOD"] == "POST" || isset($_GET['id'])) {
    $search_code = '';
    if (isset($_POST['certificate_code'])) {
        $search_code = $_POST['certificate_code'];
    } elseif (isset($_GET['id'])) {
        $search_code = $_GET['id'];
    }

    if (!empty($search_code)) {
        $stmt = $conn->prepare("
            SELECT s.first_name, s.last_name, s.year, c.status, c.issue_date, c.code
            FROM certificates c
            JOIN student s ON c.student_id = s.student_id
            WHERE c.code = ?
        ");
        $stmt->bind_param("s", $search_code);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $certificate = $result->fetch_assoc();
        } else {
            $error_message = "Certificate not found. Please check the ID and try again.";
        }
        $stmt->close();
    } else {
        $error_message = "Please enter a Certificate ID.";
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Certificate</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-image: url('bg_cert.jpg');
            background-size: cover;
            background-position: center;
            color: #333;
        }
        .overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
        }
        .container {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 600px;
            padding: 2rem;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .logo {
            max-width: 150px;
            margin-bottom: 1rem;
        }
        h1 {
            color: #2c3e50;
            margin-bottom: 2rem;
        }
        .search-form {
            margin-bottom: 2rem;
        }
        .search-form input[type="text"] {
            width: 70%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }
        .search-form input[type="text"]:focus {
            outline: none;
            border-color: #3498db;
        }
        .search-form button {
            padding: 12px 20px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .search-form button:hover {
            background-color: #2980b9;
        }
        .result-container {
            text-align: left;
            padding: 2rem;
            border: 1px solid #e0e0e0;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        .result-container h2 {
            color: #27ae60;
            border-bottom: 2px solid #27ae60;
            padding-bottom: 0.5rem;
            margin-bottom: 1.5rem;
        }
        .result-container p {
            font-size: 1.1rem;
            line-height: 1.8;
            margin: 0.5rem 0;
        }
        .result-container strong {
            color: #555;
        }
        .error {
            color: #c0392b;
            font-weight: bold;
            margin-top: 1rem;
        }
    </style>
</head>
<body>
    <div class="overlay"></div>
    <div class="container">
        <img src="logo.png" alt="Logo" class="logo">
        <h1>Certificate Verification</h1>

        <form action="verify.php" method="post" class="search-form">
            <input type="text" name="certificate_code" placeholder="Enter Certificate ID" value="<?= htmlspecialchars($search_code ?? '') ?>">
            <button type="submit">Search</button>
        </form>

        <?php if ($certificate): ?>
            <div class="result-container">
                <h2>Certificate Details</h2>
                <p><strong>Student Name:</strong> <?= htmlspecialchars($certificate['first_name'] . ' ' . $certificate['last_name']) ?></p>
                <p><strong>Course/Year:</strong> <?= htmlspecialchars($certificate['year']) ?></p>
                <p><strong>Certificate Code:</strong> <?= htmlspecialchars($certificate['code']) ?></p>
                <p><strong>Status:</strong> <span style="color: <?= $certificate['status'] == 'issued' ? '#27ae60' : '#c0392b'; ?>; font-weight: bold;"><?= htmlspecialchars(ucfirst($certificate['status'])) ?></span></p>
                <p><strong>Date of Issue:</strong> <?= date('F j, Y', strtotime($certificate['issue_date'])) ?></p>
            </div>
        <?php elseif ($error_message): ?>
            <p class="error"><?= $error_message ?></p>
        <?php endif; ?>

    </div>
</body>
</html>

<?php
// Set the content type to HTML and disable output buffering for real-time feedback
header('Content-Type: text/html; charset=utf-8');
ob_implicit_flush(true);
ob_end_flush();

echo "<!DOCTYPE html><html lang='en'><head><title>Database Installer</title><style>
body { font-family: monospace; background-color: #111; color: #eee; padding: 20px; }
h1 { color: #4caf50; }
h3 { color: #03a9f4; margin: 5px 0; }
.error { color: #f44336; }
</style></head><body>";

echo "<h1>Starting Full Database Installation...</h1>";

// Include the database connection file
include 'db.php';

// Read the SQL file content
$sql_file = 'student.sql';
if (!file_exists($sql_file)) {
    die("<p class='error'>Error: SQL file '{$sql_file}' not found.</p></body></html>");
}
$sql_content = file_get_contents($sql_file);

// Split the content into individual queries
$queries = explode(';', $sql_content);

// Loop through each query and execute it
foreach ($queries as $query) {
    // Trim whitespace from the query
    $trimmed_query = trim($query);

    // Ignore empty queries or comment lines
    if (empty($trimmed_query) || strpos($trimmed_query, '--') === 0) {
        continue;
    }

    // Execute the query
    if ($conn->query($trimmed_query)) {
        echo "<h3>✅ Query Executed Successfully</h3>";
    } else {
        // If an error occurs, print it but continue
        echo "<p class='error'>Error executing query: " . $conn->error . "</p>";
        echo "<pre class='error'>Query: " . htmlspecialchars($trimmed_query) . "</pre>";
    }
}

echo "<h1>🎉 Full Database Installation Complete!</h1>";
echo "</body></html>";

// Close the connection
$conn->close();
?>

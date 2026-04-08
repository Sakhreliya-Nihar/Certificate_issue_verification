<?php
// Always start the session before trying to destroy it
session_start();

// Unset all of the session variables to clear the data
$_SESSION = array();

// Destroy the actual session on the server
session_destroy();

// Redirect the user back to the main login page (using the clean URL format!)
header("Location: index.html");
exit();
?>

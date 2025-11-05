<?php
require_once 'includes/config.php';

// Destroy all session data
session_unset();
session_destroy();

// Redirect to home page with message
session_start();
setMessage("You have been logged out successfully.", "success");
redirect('index.php');
?>
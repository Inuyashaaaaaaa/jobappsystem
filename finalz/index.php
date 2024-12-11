<?php
session_start();
require_once 'core/models.php';

// Redirect to login if the user is not logged in
if (!isset($_SESSION['role'])) {
    header('Location: login.php');
    exit();
}

$role = $_SESSION['role'];

// Ensure the username is set in session before displaying it
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'USERNAME ERROR';  // Fallback to 'Guest' if username is not set

// Handle logout functionality
if (isset($_POST['logout'])) {
    session_unset();  // Unset all session variables
    session_destroy();  // Destroy the session
    header('Location: login.php');  // Redirect to login page
    exit();
}

// Display the username and role at the top
echo "<h2>Welcome, " . htmlspecialchars($username) . "!</h2>";

?>

<!-- Logout Button -->
<form method="POST" action="">
    <button type="submit" name="logout">Logout</button>
</form>

<?php
if ($role == 'hr') {
    // HR homepage functionality
    $job_posts = getJobPosts();
    // Display job posts and other HR-specific content
    echo "<h1>HR Dashboard</h1>";
    echo "<p>Welcome, HR!</p>";

    foreach ($job_posts as $job) {
        echo "<p>Job Title: " . htmlspecialchars($job['title']) . "</p>";
    }
} else {
    // Applicant homepage functionality
    echo "<h1>Applicant Dashboard</h1>";
    echo "<p>Welcome, Applicant!</p>";
}
?>

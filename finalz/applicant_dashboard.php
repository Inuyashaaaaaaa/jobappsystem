<?php
session_start();
require_once 'core/models.php';
require_once 'core/handleForms.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'applicant') {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];  // Get the logged-in user
$username = $_SESSION['username'];  // Get the logged-in username
$job_posts = getJobPosts();  // Get all open job posts

// Handle job application
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['apply'])) {
    $job_id = $_POST['job_id'];
    applyForJob($user_id, $job_id);  // Function to apply for the selected job
    echo "<script>alert('Application submitted successfully!');</script>";
}

// Delete job post
if (isset($_GET['delete'])) {
    $job_id = intval($_GET['delete']);

    try {
        deleteJobPost($job_id);
        echo "<script>alert('Job post deleted successfully!');</script>";
    } catch (Exception $e) {
        echo "<script>alert('Failed to delete job post: " . $e->getMessage() . "');</script>";
    }

    // Redirect to avoid duplicate delete requests on refresh
    header('Location: hr_dashboard.php');
    exit();
}

// Handle logout
if (isset($_POST['logout'])) {
    session_destroy();
    header('Location: login.php');
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Jobs</title>
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
        // JavaScript function to confirm deletion
        function confirmDelete(jobId) {
            if (confirm("Are you sure you want to delete this job post? This action cannot be undone.")) {
                // Redirect to the same page with a delete request
                window.location.href = `?delete=${jobId}`;
            }
        }
    </script>
</head>
<body>

<div class="container mt-4">
    <!-- Include the Navbar -->
    <?php include 'applicant_navbar.php'; ?>

    <h3 class="mt-4">Available Job Posts</h3>
    <?php if ($job_posts): ?>
        <form method="POST">
            <h4>Select a job to apply for:</h4>
            <select name="job_id" class="form-select mb-3" required>
                <?php foreach ($job_posts as $job): ?>
                    <option value="<?= $job['id']; ?>"><?= htmlspecialchars($job['title']); ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" name="apply" class="btn btn-primary">Apply for Job</button>
        </form>
        
    <?php else: ?>
        <p>No open job posts available at the moment.</p>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>

</body>
</html>

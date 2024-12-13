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

    // Check if a file was uploaded
    if (isset($_FILES['resume']) && $_FILES['resume']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['resume']['tmp_name'];
        $file_name = $_FILES['resume']['name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        // Validate file extension
        if ($file_ext !== 'pdf') {
            echo "<script>alert('Only PDF files are allowed for resumes.');</script>";
        } else {
            // Create a unique name and save the file
            $upload_dir = 'uploads/resumes/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);  // Ensure the directory exists
            }
            $new_file_name = uniqid() . '-' . basename($file_name);
            $file_path = $upload_dir . $new_file_name;

            if (move_uploaded_file($file_tmp, $file_path)) {
                // Apply for the job and save the resume path
                applyForJob($user_id, $job_id, $file_path);
                echo "<script>alert('Application submitted successfully!');</script>";
            } else {
                echo "<script>alert('Error uploading the file. Please try again.');</script>";
            }
        }
    } else {
        echo "<script>alert('Please upload a resume (PDF file) to apply for a job.');</script>";
    }
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
</head>
<body>

<div class="container mt-4">
    <!-- Include the Navbar -->
    <?php include 'applicant_navbar.php'; ?>

    <h3 class="mt-4">Available Job Posts</h3>
    <?php if ($job_posts): ?>
        <form method="POST" enctype="multipart/form-data">
            <h4>Select a job to apply for:</h4>
            <select name="job_id" class="form-select mb-3" required>
                <?php foreach ($job_posts as $job): ?>
                    <option value="<?= $job['id']; ?>"><?= htmlspecialchars($job['title']); ?></option>
                <?php endforeach; ?>
            </select>
            
            <h4>Upload your resume (PDF):</h4>
            <div class="mb-3">
                <input type="file" name="resume" class="form-control" accept=".pdf" required>
            </div>
            
            <button type="submit" name="apply" class="btn btn-primary">Apply for Job</button>
        </form>
    <?php else: ?>
        <p>No open job posts available at the moment.</p>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>

</body>
</html>

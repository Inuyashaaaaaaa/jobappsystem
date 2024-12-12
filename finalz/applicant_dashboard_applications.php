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
$all_applications = getApplicationsByStatus($user_id);

// Separate accepted, rejected, and applied applications
$accepted_applications = [];
$rejected_applications = [];
$applied_applications = [];

foreach ($all_applications as $application) {
    if ($application['status'] == 1) {
        $accepted_applications[] = $application;
    } elseif ($application['status'] == 2) {
        $rejected_applications[] = $application;
    } else {
        $applied_applications[] = $application;
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
    <title>Your Applications</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-4">
     <!-- Include the Navbar -->
     <?php include 'applicant_navbar.php'; ?>
    <h1 class="mb-4">Your Applications</h1>

    <h4>Accepted Applications</h4>
    <?php if ($accepted_applications): ?>
        <ul class="list-group">
            <?php foreach ($accepted_applications as $application): ?>
                <li class="list-group-item"><?= htmlspecialchars($application['title']); ?> - <span class="text-success">Accepted</span></li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>You have no accepted job applications.</p>
    <?php endif; ?>

    <h4>Rejected Applications</h4>
    <?php if ($rejected_applications): ?>
        <ul class="list-group">
            <?php foreach ($rejected_applications as $application): ?>
                <li class="list-group-item"><?= htmlspecialchars($application['title']); ?> - <span class="text-danger">Rejected</span></li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>You have no rejected job applications.</p>
    <?php endif; ?>

    <h4>Pending Applications</h4>
    <?php if ($applied_applications): ?>
        <ul class="list-group">
            <?php foreach ($applied_applications as $application): ?>
                <li class="list-group-item"><?= htmlspecialchars($application['title']); ?> - <span class="text-warning">Applied</span></li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>You have no pending job applications.</p>
    <?php endif; ?>

    <a href="applicant_dashboard.php" class="btn btn-secondary mt-4">Go Back to Dashboard</a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

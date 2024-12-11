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
$messages = getMessages($user_id);  // Get all messages for the applicant

// Fetch HR representatives for the message form
$hr_representatives = getUsersByRole('hr');  // Fetch HR representatives

// Fetch all applications for the applicant
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

// Handle job application
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['apply'])) {
    $job_id = $_POST['job_id'];
    applyForJob($user_id, $job_id);  // Function to apply for the selected job
    echo "Application submitted successfully!";
}

// Handle sending message to HR
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_message'])) {
    $receiver_id = $_POST['hr_id'];  // The HR representative's ID
    $message = $_POST['message'];
    sendMessage($user_id, $receiver_id, $message);  // Function to send a message to HR
    $message_status = "Message sent successfully!";
}

// Handle logout
if (isset($_POST['logout'])) {
    session_destroy();  // Destroy session
    header('Location: login.php');  // Redirect to login page
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Applicant Dashboard</title>
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-4">
    <h1 class="mb-4">Welcome, <?= htmlspecialchars($username); ?> (Applicant)</h1>
    
    <!-- Logout Form -->
    <form method="POST">
        <button type="submit" name="logout" class="btn btn-danger mb-4">Logout</button>
    </form>

    <!-- Applicant Dashboard Tabs -->
    <ul class="nav nav-tabs" id="dashboardTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link active" id="job-posts-tab" data-bs-toggle="tab" href="#job-posts" role="tab" aria-controls="job-posts" aria-selected="true">Available Jobs</a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" id="applications-tab" data-bs-toggle="tab" href="#applications" role="tab" aria-controls="applications" aria-selected="false">Your Applications</a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" id="messages-tab" data-bs-toggle="tab" href="#messages" role="tab" aria-controls="messages" aria-selected="false">Messages</a>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content mt-4" id="dashboardTabsContent">

        <!-- Available Jobs Tab -->
        <div class="tab-pane fade show active" id="job-posts" role="tabpanel" aria-labelledby="job-posts-tab">
            <h3>Available Job Posts</h3>
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

        <!-- Your Applications Tab -->
        <div class="tab-pane fade" id="applications" role="tabpanel" aria-labelledby="applications-tab">
            <h3>Your Applications</h3>

            <!-- Accepted Applications -->
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

            <!-- Rejected Applications -->
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

            <!-- Pending Applications -->
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
        </div>

        <!-- Messages Tab -->
        <div class="tab-pane fade" id="messages" role="tabpanel" aria-labelledby="messages-tab">
            <h3>Your Messages</h3>
            <?php if ($messages): ?>
                <div class="list-group">
                    <?php foreach ($messages as $message): ?>
                        <a href="#conversation-<?= $message['id']; ?>" class="list-group-item list-group-item-action" data-bs-toggle="collapse">
                            <strong><?= htmlspecialchars($message['sender_username']); ?>:</strong> <?= htmlspecialchars($message['message']); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p>You have no messages.</p>
            <?php endif; ?>

            <!-- Send Message Form -->
            <h4 class="mt-4">Send a Message to HR</h4>
            <form method="POST" action="">
                <div class="mb-3">
                    <textarea name="message" class="form-control" placeholder="Write your message" required></textarea>
                </div>
                <div class="mb-3">
                    <select name="hr_id" class="form-select" required>
                        <option value="" disabled selected>Select HR</option>
                        <?php foreach ($hr_representatives as $hr): ?>
                            <option value="<?= $hr['id']; ?>"><?= htmlspecialchars($hr['username']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" name="send_message" class="btn btn-primary">Send Message</button>
            </form>

            <?php if (isset($message_status)): ?>
                <p class="text-success mt-3"><?= $message_status; ?></p>
            <?php endif; ?>
        </div>

    </div>
</div>

<!-- Bootstrap JS and dependencies -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>

</body>
</html>

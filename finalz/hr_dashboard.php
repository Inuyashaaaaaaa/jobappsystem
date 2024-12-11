<?php
session_start();
require_once 'core/models.php';
require_once 'core/handleForms.php';

// Check if the user is logged in and has the hr role
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'hr') {
    header('Location: login.php');
    exit();
}

$hr_id = $_SESSION['user_id'];  // Get the logged-in user (HR)
$hr_username = getUserById($hr_id)['username']; // Get HR's username
$job_posts = getJobPosts();  // Get all job posts
$messages = getMessages($hr_id);  // Get all messages for HR
$applicants = getApplicants();  // Fetch applicants for the HR to message



if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accept'])) {
    $application_id = $_POST['application_id'];
    acceptApplication($application_id);  // Function to accept the application
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reject'])) {
    $application_id = $_POST['application_id'];
    rejectApplication($application_id);  // Function to reject the application
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['post_job'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    postJob($title, $description, $hr_id);
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_job'])) {
    $job_id = $_POST['job_id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    updateJobPost($job_id, $title, $description);  // Update job post
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_job'])) {
    $job_id = $_POST['job_id'];
    deleteJobPost($job_id);  // Delete job post
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_message'])) {
    $receiver_id = $_POST['receiver_id'];
    $message = $_POST['message'];
    sendMessage($hr_id, $receiver_id, $message);  // Send message
    header('Location: ' . $_SERVER['PHP_SELF']);
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
    <title>HR Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>

<div class="container mt-4">
    <!-- HR Dashboard Header -->
    <h1>Welcome, <?= htmlspecialchars($hr_username); ?> (HR)</h1>
    <form method="POST">
        <button type="submit" name="logout" class="btn btn-danger">Logout</button>
    </form>

    <!-- HR Dashboard Tabs -->
    <ul class="nav nav-tabs" id="dashboardTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link active" id="job-posts-tab" data-bs-toggle="tab" href="#job-posts" role="tab" aria-controls="job-posts" aria-selected="true">Job Posts</a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" id="job-applications-tab" data-bs-toggle="tab" href="#job-applications" role="tab" aria-controls="job-applications" aria-selected="false">Job Applications</a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" id="messages-tab" data-bs-toggle="tab" href="#messages" role="tab" aria-controls="messages" aria-selected="false">Messages</a>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content mt-4" id="dashboardTabsContent">
        
        <!-- Job Posts Tab -->
        <div class="tab-pane fade show active" id="job-posts" role="tabpanel" aria-labelledby="job-posts-tab">
            <h3>Create a New Job Post</h3>
            <form method="POST" action="">
                <input type="text" name="title" class="form-control mb-2" placeholder="Job Title" required><br>
                <textarea name="description" class="form-control mb-3" placeholder="Job Description" required></textarea><br>
                <button type="submit" name="post_job" class="btn btn-primary">Post Job</button>
            </form>

            <h2 class="mt-4">Job Posts</h2>
            <?php if ($job_posts): ?>
                <?php foreach ($job_posts as $job): ?>
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($job['title']); ?></h5>
                            <p class="card-text"><?= htmlspecialchars($job['description']); ?></p>

                            <h6>Applications for this Job:</h6>
                            <?php
                            // Fetch applications for the current job post
                            $applications = getApplications($job['id']);
                            if ($applications): ?>
                                <ul>
                                    <?php foreach ($applications as $application): ?>
                                        <li>
                                            <strong><?= htmlspecialchars($application['applicant_name']); ?></strong> - 
                                            Status: 
                                            <?php
                                            if ($application['status'] == 1) {
                                                echo 'Hired';
                                            } elseif ($application['status'] == 2) {
                                                echo 'Rejected';
                                            } else {
                                                echo 'Applied';
                                            }
                                            ?>

                                            <?php if ($application['status'] == 0): ?>
                                                <!-- Only allow accepting or rejecting applications that are still 'applied' (status = 0) -->
                                                <form method="POST" class="mt-2">
                                                    <input type="hidden" name="application_id" value="<?= $application['id']; ?>">
                                                    <button type="submit" name="accept" class="btn btn-success btn-sm">Accept Application</button>
                                                    <button type="submit" name="reject" class="btn btn-danger btn-sm">Reject Application</button>
                                                </form>
                                            <?php endif; ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                <p>No applications yet for this job.</p>
                            <?php endif; ?>
                    
                            <form method="POST" class="d-inline">
                                <input type="hidden" name="job_id" value="<?= $job['id']; ?>">
                                <button type="button" class="btn btn-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?= $job['id']; ?>">Edit</button>
                            </form>
                            
                            <form method="POST" class="d-inline">
                                <input type="hidden" name="job_id" value="<?= $job['id']; ?>">
                                <button type="submit" name="delete_job" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                        </div>
                    </div>

                    <!-- Edit Job Modal -->
                    <div class="modal fade" id="editModal<?= $job['id']; ?>" tabindex="-1" aria-labelledby="editModalLabel<?= $job['id']; ?>" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editModalLabel<?= $job['id']; ?>">Edit Job Post</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form method="POST" action="">
                                    <div class="modal-body">
                                        <input type="hidden" name="job_id" value="<?= $job['id']; ?>">
                                        <div class="mb-3">
                                            <label for="title" class="form-label">Job Title</label>
                                            <input type="text" class="form-control" name="title" value="<?= htmlspecialchars($job['title']); ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="description" class="form-label">Job Description</label>
                                            <textarea class="form-control" name="description" required><?= htmlspecialchars($job['description']); ?></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" name="update_job" class="btn btn-primary">Save Changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No job posts available.</p>
            <?php endif; ?>
        </div>

        <!-- Job Applications Tab -->
        <div class="tab-pane fade" id="job-applications" role="tabpanel" aria-labelledby="job-applications-tab">
            <h2>Job Applications</h2>
            <!-- Job applications display logic can go here -->
        </div>

        <!-- Messages Tab -->
        <div class="tab-pane fade" id="messages" role="tabpanel" aria-labelledby="messages-tab">
            <h3>Your Messages</h3>
            <div class="list-group">
                <?php if ($messages): ?>
                    <?php foreach ($messages as $message): ?>
                        <a href="#conversation-<?= $message['id']; ?>" class="list-group-item list-group-item-action" data-bs-toggle="collapse">
                            <strong><?= htmlspecialchars($message['sender_username']); ?>:</strong> <?= htmlspecialchars($message['message']); ?>
                        </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>You have no messages.</p>
                <?php endif; ?>
            </div>

            <h4 class="mt-3">Reply to Conversation</h4>
            <form method="POST" action="">
                <textarea name="message" class="form-control" placeholder="Write your message" required></textarea><br>
                <select name="receiver_id" class="form-control mb-2" required>
                    <option value="" disabled selected>Select Applicant</option>
                    <?php foreach ($applicants as $applicant) {
                        echo "<option value='" . $applicant['id'] . "'>" . htmlspecialchars($applicant['username']) . "</option>";
                    } ?>
                </select><br>
                <button type="submit" name="send_message" class="btn btn-primary">Send Message</button>
            </form>
        </div>
    </div>
</div>

<!-- Bootstrap JS and dependencies -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>

<?php
session_start();
require_once 'core/models.php';
require_once 'core/handleForms.php';

// Check if the user is logged in and has the applicant role
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'applicant') {
    header('Location: login.php');
    exit();
}


$username = $_SESSION['username'];  // Get the logged-in username

$applicant_id = $_SESSION['user_id'];  // Logged-in Applicant ID
$applicant_username = getUserById($applicant_id)['username']; // Get Applicant's username
$hr_representatives = getUsersByRole('hr');  // Fetch all HR representatives

// Get conversation history with a specific HR representative if selected
$current_conversation = null;
$selected_hr_id = isset($_GET['hr_id']) ? $_GET['hr_id'] : null;
if ($selected_hr_id) {
    $current_conversation = getMessagesByReceiver($applicant_id, $selected_hr_id);  // Fetch conversation with specific HR representative
}

// Handle sending a message
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_message'])) {
    $receiver_id = $_POST['hr_id'];  // The selected HR representative's ID
    $message = $_POST['message'];
    sendMessage($applicant_id, $receiver_id, $message);  // Function to send a message to the HR representative

    // Redirect back to the same conversation to prevent form resubmission
    header("Location: applicant_dashboard_messages.php?hr_id=" . $receiver_id);
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
    <title>Applicant Messenger</title>
    <!-- Bootstrap 5 CDN -->
    <link rel="stylesheet" href="styles.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php include 'applicant_navbar.php'; ?>
<div class="messenger-container">
    <!-- Contacts List -->
    <div class="contacts-list">
        <div class="conversation-header">
            <h4>HR Representatives</h4>
        </div>
        <?php foreach ($hr_representatives as $hr): ?>
            <div class="contact-item <?= ($selected_hr_id == $hr['id']) ? 'active' : ''; ?>">
                <div class="contact-avatar"></div>
                <a href="applicant_dashboard_messages.php?hr_id=<?= $hr['id']; ?>" 
                   class="text-decoration-none text-dark flex-grow-1">
                    <?= htmlspecialchars($hr['username']); ?>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="conversation-area">
    <?php if ($selected_hr_id): ?>
        <!-- Conversation Header -->
        <div class="conversation-header">
            <?php 
            // Get the selected HR representative's name
            $selected_hr = getUserById($selected_hr_id);
            if ($selected_hr) {
                echo '<h4>' . htmlspecialchars($selected_hr['username']) . '</h4>';
            } else {
                echo '<h4>Unknown HR Representative</h4>';
            }
            ?>
        </div>

        <!-- Messages List -->
        <div class="messages-list">
            <?php if ($current_conversation): ?>
                <?php foreach ($current_conversation as $message): ?>
                    <div class="message-wrapper">
                        <div class="message <?= ($message['sender_id'] == $applicant_id) ? 'sent' : 'received'; ?>">
                            <?= htmlspecialchars($message['message']); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="text-muted text-center mt-3">No messages yet. Start the conversation!</div>
            <?php endif; ?>
        </div>

        <!-- Message Input -->
        <div class="message-input">
            <form method="POST" class="d-flex">
                <textarea name="message" class="form-control me-2" placeholder="Type a message" required></textarea>
                <input type="hidden" name="hr_id" value="<?= $selected_hr_id; ?>">
                <button type="submit" name="send_message" class="btn btn-primary">Send</button>
            </form>
        </div>
    <?php else: ?>
        <div class="d-flex justify-content-center align-items-center h-100 text-muted">
            Select an HR representative to start a conversation
        </div>
    <?php endif; ?>
</div>

</div>

<!-- Bootstrap JS and dependencies -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
<script>
    // Optional: Auto-scroll to the bottom of messages
    function scrollToBottom() {
        const messagesList = document.querySelector('.messages-list');
        if (messagesList) {
            messagesList.scrollTop = messagesList.scrollHeight;
        }
    }
    scrollToBottom();
</script>
</body>
</html>

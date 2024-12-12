<?php
session_start();
require_once 'core/models.php';
require_once 'core/handleForms.php';

// Check if the user is logged in and has the HR role
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'hr') {
    header('Location: login.php');
    exit();
}

$hr_id = $_SESSION['user_id'];  // Logged-in HR ID
$hr_username = getUserById($hr_id)['username']; // Get HR's username
$applicants = getApplicants();  // Fetch all applicants

// Get conversation history with a specific applicant if selected
$current_conversation = null;
$applicant_id = isset($_GET['applicant_id']) ? $_GET['applicant_id'] : null;
if ($applicant_id) {
    $current_conversation = getMessagesByReceiver($hr_id, $applicant_id);  // Fetch conversation with specific applicant
}

// Handle sending a message
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_message'])) {
    $receiver_id = $_POST['applicant_id'];  // The selected applicant's ID
    $message = $_POST['message'];
    sendMessage($hr_id, $receiver_id, $message);  // Function to send a message to the applicant

    // Redirect back to the same conversation to prevent form resubmission
    header("Location: hr_messages.php?applicant_id=" . $receiver_id);
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
    <title>HR Messenger</title>
    <!-- Bootstrap 5 CDN -->
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php include 'hr_navbar.php'; ?>
<div class="messenger-container">
    <!-- Contacts List -->
    <div class="contacts-list">
        <div class="conversation-header">
            <h4>Applicants</h4>
        </div>
        <?php foreach ($applicants as $applicant): ?>
            <div class="contact-item <?= ($applicant_id == $applicant['id']) ? 'active' : ''; ?>">
                <div class="contact-avatar"></div>
                <a href="hr_messages.php?applicant_id=<?= $applicant['id']; ?>" 
                   class="text-decoration-none text-dark flex-grow-1">
                    <?= htmlspecialchars($applicant['username']); ?>
                </a>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Conversation Area -->
    <div class="conversation-area">
        <?php if ($applicant_id): ?>
            <!-- Conversation Header -->
            <div class="conversation-header">
                <?php 
                // Fetch the applicant's details based on the applicant_id
                $contact = getUserById($applicant_id);

                if ($contact) {
                    echo '<h4>' . htmlspecialchars($contact['username']) . '</h4>';
                } else {
                    echo '<h4>Unknown Applicant</h4>'; // In case the applicant does not exist or there's an issue fetching the user
                }
                ?>
            </div>

            <!-- Messages List -->
            <div class="messages-list">
                <?php if ($current_conversation): ?>
                    <?php foreach ($current_conversation as $message): ?>
                        <div class="message-wrapper">
                            <div class="message <?= ($message['sender_id'] == $hr_id) ? 'sent' : 'received'; ?>">
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
                    <input type="hidden" name="applicant_id" value="<?= $applicant_id; ?>">
                    <button type="submit" name="send_message" class="btn btn-primary">Send</button>
                </form>
            </div>
        <?php else: ?>
            <div class="d-flex justify-content-center align-items-center h-100 text-muted">
                Select an applicant to start a conversation
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

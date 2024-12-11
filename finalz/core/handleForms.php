<?php
require_once 'dbConfig.php';
require_once 'models.php';

// Handle user registration
function registerUser($username, $password, $role) {
    global $db;
    
    // Check if the username already exists
    $sql = "SELECT * FROM users WHERE username = :username";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existingUser) {
        return false;  // Username already exists
    }

    // Hash the password before saving
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert the new user into the database
    $sql = "INSERT INTO users (username, password, role) VALUES (:username, :password, :role)";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':password', $hashedPassword);
    $stmt->bindParam(':role', $role);
    $stmt->execute();

    // Return the newly created user
    return getUserByUsername($username);
}

// Fetch a user by username (to validate credentials)
function getUserByUsername($username) {
    global $db;
    $sql = "SELECT * FROM users WHERE username = :username";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

//login 

function validateLogin($username, $password, $role) {
    global $db;

    // Query to check if the user exists with the given username and role
    $sql = "SELECT * FROM users WHERE username = :username AND role = :role";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':role', $role);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // If user is found and the password matches
    if ($user && password_verify($password, $user['password'])) {
        return $user; // Return user data
    }

    return false; // If login fails
}

function postJob($title, $description, $hr_id) {
    global $db;
    $sql = "INSERT INTO job_posts (title, description, posted_by) VALUES (:title, :description, :posted_by)";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':posted_by', $hr_id);
    $stmt->execute();
}







function sendMessage($sender_id, $receiver_id, $message) {
    global $db;

    $sql = "INSERT INTO messages (sender_id, receiver_id, message) VALUES (:sender_id, :receiver_id, :message)";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':sender_id', $sender_id);
    $stmt->bindParam(':receiver_id', $receiver_id);
    $stmt->bindParam(':message', $message);
    $stmt->execute();
}

function getApplicants() {
    global $db;
    $sql = "SELECT * FROM users WHERE role = 'applicant'";
    $stmt = $db->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Function to apply for a job
function applyForJob($applicant_id, $job_id) {
    global $db;

    // Insert the application into the 'applications' table
    $sql = "INSERT INTO applications (user_id, job_id) VALUES (:user_id, :job_id)";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':user_id', $applicant_id);
    $stmt->bindParam(':job_id', $job_id);
    $stmt->execute();
}


?>





<?php
require_once 'dbConfig.php';

// Fetch all job posts
function getJobPosts() {
    global $db;
    $sql = "SELECT * FROM job_posts WHERE status = 'open'";
    $stmt = $db->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fetch messages for a specific user, including sender details
function getMessages($user_id) {
    global $db;
    $sql = "
        SELECT messages.*, users.username AS sender_username
        FROM messages
        JOIN users ON messages.sender_id = users.id
        WHERE messages.receiver_id = :user_id
        ORDER BY messages.created_at DESC
    ";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fetch applications by status for the applicant
function getApplicationsByStatus($user_id) {
    global $db;
    $sql = "
        SELECT applications.id, applications.job_id, applications.status, job_posts.title
        FROM applications
        JOIN job_posts ON applications.job_id = job_posts.id
        WHERE applications.user_id = :user_id
    ";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


// Fetch job applications for a specific job
function getApplications($job_id) {
    global $db;
    $sql = "
        SELECT applications.id, applications.user_id AS applicant_id, applications.job_id, 
               applications.status, applications.hired, users.username AS applicant_name
        FROM applications
        JOIN users ON applications.user_id = users.id
        WHERE applications.job_id = :job_id
    ";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':job_id', $job_id);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}



// Fetch a specific user by ID
function getUserById($user_id) {
    global $db;
    $sql = "SELECT * FROM users WHERE id = :user_id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function acceptApplication($application_id) {
    global $db;

    // Update the application status to hired (1)
    $sql = "UPDATE applications SET hired = 1 WHERE id = :application_id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':application_id', $application_id);
    $stmt->execute();
}


// Fetch all HR representatives
function getUsersByRole($role) {
    global $db;
    $sql = "SELECT * FROM users WHERE role = :role";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':role', $role);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getAcceptedApplications($user_id) {
    global $db;
    $sql = "SELECT job_posts.title, applications.status 
            FROM applications 
            JOIN job_posts ON job_posts.id = applications.job_id 
            WHERE applications.user_id = :user_id AND applications.status = 'hired'";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function rejectApplication($application_id) {
    global $db;

    // Update the application status to 'rejected' (2)
    $sql = "UPDATE applications SET status = 2 WHERE id = :application_id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':application_id', $application_id);
    $stmt->execute();
}


function updateJobPost($job_id, $title, $description) {
    global $db;
    $stmt = $db->prepare("UPDATE job_posts SET title = ?, description = ? WHERE id = ?");
    $stmt->execute([$title, $description, $job_id]);
}

function deleteJobPost($job_id) {
    global $db;
    $stmt = $db->prepare("DELETE FROM job_posts WHERE id = ?");
    $stmt->execute([$job_id]);
}


?>

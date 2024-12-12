<div class="container mt-4">
    <h1 class="mb-4">Welcome, <?= htmlspecialchars($username); ?> (Applicant)</h1>
    
    <!-- Logout Form -->
    <form method="POST">
        <button type="submit" name="logout" class="btn btn-danger mb-4">Logout</button>
    </form>
<!-- Applicant Dashboard Tabs -->
<ul class="nav nav-tabs" id="dashboardTabs" role="tablist">
    <!-- Available Jobs Tab -->
    <li class="nav-item" role="presentation">
        <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'applicant_dashboard.php' ? 'active' : ''; ?>" 
           href="applicant_dashboard.php">Available Jobs</a>
    </li>

    <!-- Your Applications Tab -->
    <li class="nav-item" role="presentation">
        <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'applicant_dashboard_applications.php' ? 'active' : ''; ?>" 
           href="applicant_dashboard_applications.php">Your Applications</a>
    </li>

    <!-- Messages Tab -->
    <li class="nav-item" role="presentation">
        <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'applicant_dashboard_messages.php' ? 'active' : ''; ?>" 
           href="applicant_dashboard_messages.php">Messages</a>
    </li>
</ul>

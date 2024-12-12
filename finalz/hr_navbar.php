<div class="container mt-4">
    <h1 class="mb-4">Welcome, <?= htmlspecialchars($hr_username); ?> (HR Department)</h1>
    
    <!-- Logout Form -->
    <form method="POST">
        <button type="submit" name="logout" class="btn btn-danger mb-4">Logout</button>
    </form>
<!-- Applicant Dashboard Tabs -->
<ul class="nav nav-tabs" id="dashboardTabs" role="tablist">
    <!-- Available Jobs Tab -->
    <li class="nav-item" role="presentation">
        <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'hr_dashboard.php' ? 'active' : ''; ?>" 
           href="hr_dashboard.php">Available Jobs</a>
    </li>


    <!-- Messages Tab -->
    <li class="nav-item" role="presentation">
        <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'hr_messages.php' ? 'active' : ''; ?>" 
           href="hr_messages.php">Messages</a>
    </li>
</ul>

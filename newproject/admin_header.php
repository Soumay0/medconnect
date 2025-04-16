<?php
// admin_header.php
?>
<header class="admin-header">
    <div class="logo">
        <a href="dashboard.php">Med<span>Connect</span> Admin</a>
    </div>
    <nav>
        <a href="dashboard.php" class="<?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : '' ?>">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>
        <a href="appointments.php" class="<?= basename($_SERVER['PHP_SELF']) == 'appointments.php' ? 'active' : '' ?>">
            <i class="fas fa-calendar-check"></i> Appointments
        </a>
        <a href="doctors.php" class="<?= basename($_SERVER['PHP_SELF']) == 'doctors.php' ? 'active' : '' ?>">
            <i class="fas fa-user-md"></i> Doctors
        </a>
    </nav>
    <div class="admin-actions">
        <a href="../index.php" class="btn btn-outline">
            <i class="fas fa-home"></i> User Site
        </a>
        <a href="logout.php" class="btn">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </div>
</header>
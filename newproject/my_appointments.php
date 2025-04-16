<?php
session_start();
require 'config.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check if admin
$is_admin = isset($_SESSION['admin_id']);

// Fetch appointments
$appointments = [];
if ($is_admin) {
    // Admin sees all appointments
    $query = "SELECT a.*, u.name as user_name, d.name as doctor_name 
              FROM appointments a
              LEFT JOIN users u ON a.user_id = u.id
              LEFT JOIN doctors d ON a.doctor_id = d.id
              ORDER BY a.appointment_date DESC, a.appointment_time DESC";
    $result = mysqli_query($conn, $query);
} else {
    // Regular users see only their appointments
    $user_id = $_SESSION['user_id'];
    $query = "SELECT a.*, d.name as doctor_name 
              FROM appointments a
              LEFT JOIN doctors d ON a.doctor_id = d.id
              WHERE a.user_id = ?
              ORDER BY a.appointment_date DESC, a.appointment_time DESC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
}

while ($row = mysqli_fetch_assoc($result)) {
    $appointments[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $is_admin ? 'All Appointments' : 'My Appointments' ?> | MedConnect</title>
    <style>
        :root {
            --primary: #6c63ff;
            --secondary: #4d44db;
            --accent: #ff6584;
            --light: #f8f9fa;
            --dark: #212529;
            --gray: #6c757d;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        
        body {
            background: #f5f7fa;
            color: var(--dark);
            min-height: 100vh;
        }
        
        /* Header Styles */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.5rem 5%;
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .logo a {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
            text-decoration: none;
        }
        
        .logo span {
            color: var(--accent);
        }
        
        .nav-links {
            display: flex;
            gap: 1.5rem;
        }
        
        .nav-links a {
            text-decoration: none;
            color: var(--dark);
            font-weight: 500;
        }
        
        /* Main Content */
        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        
        .page-title {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
        
        .btn {
            padding: 0.8rem 1.5rem;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn:hover {
            background: var(--secondary);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        /* Appointment Cards */
        .appointment-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
        }
        
        .appointment-info {
            flex: 1;
            min-width: 250px;
        }
        
        .appointment-meta {
            color: var(--gray);
            font-size: 0.9rem;
            margin-top: 0.5rem;
        }
        
        .meta-item {
            margin-right: 1rem;
            display: inline-block;
        }
        
        .status {
            display: inline-block;
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-top: 0.5rem;
        }
        
        .status-scheduled {
            background: #e3f2fd;
            color: #1976d2;
        }
        
        .status-completed {
            background: #e8f5e9;
            color: #388e3c;
        }
        
        .status-cancelled {
            background: #ffebee;
            color: #d32f2f;
        }
        
        .actions {
            display: flex;
            gap: 0.5rem;
            align-items: flex-start;
        }
        
        .action-btn {
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-size: 0.9rem;
            cursor: pointer;
            border: none;
            transition: all 0.2s ease;
        }
        
        .action-btn:hover {
            transform: translateY(-1px);
        }
        
        .edit-btn {
            background: #e3f2fd;
            color: #1976d2;
        }
        
        .cancel-btn {
            background: #ffebee;
            color: #d32f2f;
        }
        
        .no-appointments {
            text-align: center;
            padding: 3rem;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        /* Footer */
        .page-footer {
            text-align: center;
            padding: 1.5rem;
            margin-top: 3rem;
            background: white;
            border-top: 1px solid #eee;
        }
        
        @media (max-width: 768px) {
            .appointment-card {
                flex-direction: column;
            }
            
            .actions {
                margin-top: 1rem;
                justify-content: flex-end;
            }
        }
    </style>
</head>
<body>
    <header class="page-header">
        <div class="logo">
            <a href="index.php">Med<span>Connect</span></a>
        </div>
        <nav class="nav-links">
            <a href="index.php">Home</a>
            <a href="dashboard.php">Dashboard</a>
            <a href="profile.php">Profile</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>
    
    <main class="container">
        <div class="page-title">
            <h1><?= $is_admin ? 'All Appointments' : 'My Appointments' ?></h1>
            <a href="index.php" class="btn">+ New Appointment</a>
        </div>
        
        <?php if (empty($appointments)): ?>
            <div class="no-appointments">
                <h3>No appointments found</h3>
                <p>You haven't booked any appointments yet.</p>
                <a href="index.php" class="btn">Book Your First Appointment</a>
            </div>
        <?php else: ?>
            <?php foreach ($appointments as $appt): 
                $status_class = 'status-scheduled';
                if ($appt['completed'] == 1) {
                    $status_class = 'status-completed';
                } elseif ($appt['status'] == 'cancelled') {
                    $status_class = 'status-cancelled';
                }
            ?>
                <div class="appointment-card">
                    <div class="appointment-info">
                        <h3>
                            <?= htmlspecialchars($appt['service_type']) ?>
                            <?php if ($is_admin): ?>
                                <span> - <?= htmlspecialchars($appt['user_name']) ?></span>
                            <?php endif; ?>
                        </h3>
                        <div class="appointment-meta">
                            <span class="meta-item">
                                <strong>Date:</strong> <?= date('F j, Y', strtotime($appt['appointment_date'])) ?>
                            </span>
                            <span class="meta-item">
                                <strong>Time:</strong> <?= date('g:i A', strtotime($appt['appointment_time'])) ?>
                            </span>
                            <?php if (!empty($appt['doctor_name'])): ?>
                                <span class="meta-item">
                                    <strong>Doctor:</strong> <?= htmlspecialchars($appt['doctor_name']) ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        <div class="status <?= $status_class ?>">
                            <?= $appt['completed'] ? 'Completed' : ($appt['status'] == 'cancelled' ? 'Cancelled' : 'Scheduled') ?>
                        </div>
                    </div>
                    
                    <div class="actions">
                        <?php if (!$appt['completed'] && $appt['status'] != 'cancelled'): ?>
                            <button class="action-btn edit-btn">Reschedule</button>
                            <button class="action-btn cancel-btn">Cancel</button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </main>
    
    <footer class="page-footer">
        <p>&copy; <?= date('Y') ?> MedConnect. All rights reserved.</p>
    </footer>
</body>
</html>
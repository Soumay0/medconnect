<?php
session_start();
error_log("===== ADMIN DASHBOARD DEBUG =====");
error_log("SESSION Data: " . print_r($_SESSION, true));
require 'config.php';

// Verify admin access
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Handle form submissions
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Handle appointment actions
    if (isset($_POST['appointment_action'])) {
        $appointment_id = intval($_POST['appointment_id']);
        $action = $_POST['appointment_action'];
        
        if ($action == 'approve') {
            $status = 'confirmed';
            $completed = 0;
            $meeting_id = mysqli_real_escape_string($conn, $_POST['meeting_id']);
            
            $stmt = $conn->prepare("UPDATE appointments SET status = ?, completed = ?, meeting_id = ? WHERE id = ?");
            $stmt->bind_param("sisi", $status, $completed, $meeting_id, $appointment_id);
            
            if ($stmt->execute()) {
                $success = "Appointment approved successfully with Meeting ID: $meeting_id";
            } else {
                $error = "Error updating appointment: ".$conn->error;
            }
        } else {
            $status = 'cancelled';
            $completed = 1;
            
            $stmt = $conn->prepare("UPDATE appointments SET status = ?, completed = ? WHERE id = ?");
            $stmt->bind_param("sii", $status, $completed, $appointment_id);
            
            if ($stmt->execute()) {
                $success = "Appointment cancelled successfully!";
            } else {
                $error = "Error cancelling appointment: ".$conn->error;
            }
        }
    }
    
    // Handle doctor addition
    if (isset($_POST['add_doctor'])) {
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $specialization = mysqli_real_escape_string($conn, $_POST['specialization']);
        $bio = mysqli_real_escape_string($conn, $_POST['bio']);
        $age = intval($_POST['age']);
        
        $insert_query = "INSERT INTO doctors (name, specialization, bio, age, is_active) 
                        VALUES ('$name', '$specialization', '$bio', $age, 1)";
        if (mysqli_query($conn, $insert_query)) {
            $doctor_id = mysqli_insert_id($conn);
            $success = "Doctor added successfully!";
            
            // Add default availability
            $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
            foreach ($days as $day) {
                mysqli_query($conn, "INSERT INTO doctor_availability (doctor_id, day_of_week, start_time, end_time, is_available)
                                   VALUES ($doctor_id, '$day', '09:00:00', '17:00:00', 1)");
            }
        } else {
            $error = "Error adding doctor: " . mysqli_error($conn);
        }
    }
    
    // Handle doctor update
    if (isset($_POST['update_doctor'])) {
        $doctor_id = intval($_POST['doctor_id']);
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $specialization = mysqli_real_escape_string($conn, $_POST['specialization']);
        $bio = mysqli_real_escape_string($conn, $_POST['bio']);
        $age = intval($_POST['age']);
        
        $stmt = $conn->prepare("UPDATE doctors SET name = ?, specialization = ?, bio = ?, age = ? WHERE id = ?");
        $stmt->bind_param("sssii", $name, $specialization, $bio, $age, $doctor_id);
        
        if ($stmt->execute()) {
            $success = "Doctor updated successfully!";
        } else {
            $error = "Error updating doctor: " . $conn->error;
        }
    }
    
    // Handle doctor deletion
    if (isset($_POST['delete_doctor'])) {
        $doctor_id = intval($_POST['doctor_id']);
        
        // First check if doctor has any appointments
        $check_query = "SELECT COUNT(*) as count FROM appointments WHERE doctor_id = $doctor_id AND completed = 0";
        $result = mysqli_query($conn, $check_query);
        $row = mysqli_fetch_assoc($result);
        
        if ($row['count'] > 0) {
            $error = "Cannot delete doctor with active appointments!";
        } else {
            // Soft delete (set is_active to 0)
            $delete_query = "UPDATE doctors SET is_active = 0 WHERE id = $doctor_id";
            if (mysqli_query($conn, $delete_query)) {
                $success = "Doctor removed successfully!";
            } else {
                $error = "Error removing doctor: " . mysqli_error($conn);
            }
        }
    }
    
    // Handle admin creation
    if (isset($_POST['add_admin'])) {
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $password = $_POST['password'];
        $role = mysqli_real_escape_string($conn, $_POST['role']);
        
        // Validate email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Invalid email format";
        } else {
            // Check if email already exists
            $check_query = "SELECT id FROM admins WHERE email = '$email'";
            $result = mysqli_query($conn, $check_query);
            
            if (mysqli_num_rows($result) > 0) {
                $error = "Email already exists";
            } else {
                // Hash password
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                
                $stmt = $conn->prepare("INSERT INTO admins (name, email, password_hash, role) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("ssss", $name, $email, $password_hash, $role);
                
                if ($stmt->execute()) {
                    $success = "Admin created successfully!";
                } else {
                    $error = "Error creating admin: " . $conn->error;
                }
            }
        }
    }
}

// Fetch data
$appointments = mysqli_query($conn, "SELECT a.*, u.name as user_name, u.email as user_email, d.name as doctor_name 
                                   FROM appointments a
                                   LEFT JOIN users u ON a.user_id = u.id
                                   LEFT JOIN doctors d ON a.doctor_id = d.id
                                   WHERE a.completed = 0 AND a.status != 'cancelled'
                                   ORDER BY a.appointment_date, a.appointment_time");

$doctors = mysqli_query($conn, "SELECT * FROM doctors WHERE is_active = 1");

// Fetch admins (for display if you want to show them)
$admins = mysqli_query($conn, "SELECT id, name, email, role, created_at FROM admins ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | MedConnect</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
       :root {
            --primary: #6c63ff;
            --secondary: #4d44db;
            --light: #f8f9fa;
            --dark: #212529;
            --gray: #6c757d;
            --success: #28a745;
            --danger: #dc3545;
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
        }
        
        /* Header */
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 2rem;
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .logo {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
        }
        
        .logo span {
            color: var(--secondary);
        }
        
        .admin-actions {
            display: flex;
            gap: 1rem;
            align-items: center;
        }
        
        .admin-name {
            font-weight: 600;
        }
        
        /* Dashboard Layout */
        .dashboard-container {
            max-width: 1400px;
            margin: 2rem auto;
            padding: 0 2rem;
        }
        
        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
        
        /* Cards */
        .card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #eee;
        }
        
        .card-title {
            font-size: 1.2rem;
            color: var(--primary);
        }
        
        /* Tables */
        .table-responsive {
            overflow-x: auto;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th, td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        th {
            background: var(--light);
            color: var(--dark);
            font-weight: 600;
        }
        
        tr:hover {
            background: rgba(108, 99, 255, 0.03);
        }
        
        /* Buttons */
        .btn {
            padding: 0.6rem 1.2rem;
            border-radius: 6px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
            border: none;
            font-size: 0.9rem;
        }
        
        .btn-sm {
            padding: 0.4rem 0.8rem;
            font-size: 0.8rem;
        }
        
        .btn-primary {
            background: var(--primary);
            color: white;
        }
        
        .btn-primary:hover {
            background: var(--secondary);
        }
        
        .btn-success {
            background: var(--success);
            color: white;
        }
        
        .btn-danger {
            background: var(--danger);
            color: white;
        }
        
        .btn-outline {
            background: transparent;
            border: 1px solid var(--gray);
            color: var(--dark);
        }
        
        .btn-outline:hover {
            background: #f8f9fa;
        }
        
        /* Status Badges */
        .badge {
            display: inline-block;
            padding: 0.3rem 0.6rem;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .badge-primary {
            background: rgba(108, 99, 255, 0.1);
            color: var(--primary);
        }
        
        .badge-warning {
            background: rgba(255, 193, 7, 0.1);
            color: #d39e00;
        }
        
        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }
        
        /* Messages */
        .alert {
            padding: 1rem;
            border-radius: 6px;
            margin-bottom: 1rem;
        }
        
        .alert-success {
            background: #e8f5e9;
            color: var(--success);
            border-left: 4px solid var(--success);
        }
        
        .alert-danger {
            background: #ffebee;
            color: var(--danger);
            border-left: 4px solid var(--danger);
        }
        
        /* Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }
        
        .modal-content {
            background: white;
            border-radius: 10px;
            width: 90%;
            max-width: 500px;
            padding: 1.5rem;
            box-shadow: 0 5px 20px rgba(0,0,0,0.2);
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        .modal-title {
            font-size: 1.2rem;
            font-weight: 600;
        }
        
        .close-modal {
            font-size: 1.5rem;
            cursor: pointer;
            color: var(--gray);
        }
        
        .form-group {
            margin-bottom: 1rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }
        
        .form-control {
            width: 100%;
            padding: 0.6rem 0.8rem;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 0.9rem;
        }
        
        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 0.5rem;
            margin-top: 1.5rem;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            header {
                flex-direction: column;
                gap: 1rem;
                padding: 1rem;
            }
            
            .admin-actions {
                width: 100%;
                justify-content: space-between;
            }
            
            .dashboard-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }
            
            .action-buttons {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <div class="logo">
            <a href="index.php" style="text-decoration: none; color: inherit;">Med<span>Connect</span></a>
        </div>
        <div class="admin-actions">
            <span class="admin-name"><?= htmlspecialchars($_SESSION['admin_name']) ?> (<?= htmlspecialchars($_SESSION['admin_role']) ?>)</span>
            <a href="logout.php" class="btn btn-outline btn-sm">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </header>
    
    <!-- Dashboard Content -->
    <div class="dashboard-container">
        <!-- Success/Error Messages -->
        <?php if(isset($success)): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?= $success ?>
            </div>
        <?php endif; ?>
        
        <?php if(isset($error)): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> <?= $error ?>
            </div>
        <?php endif; ?>
        
        <div class="dashboard-header">
            <h1>Admin Dashboard</h1>
            <div>
                <?php if ($_SESSION['admin_role'] === 'superadmin'): ?>
                    <button onclick="document.getElementById('addAdminModal').style.display='flex'" 
                        class="btn btn-primary btn-sm" style="margin-right: 0.5rem;">
                        <i class="fas fa-user-plus"></i> Add Admin
                    </button>
                <?php endif; ?>
                <button onclick="document.getElementById('addDoctorModal').style.display='flex'" 
                    class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Add Doctor
                </button>
            </div>
        </div>
        
        <!-- Appointments Section -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Upcoming Appointments</h2>
            </div>
            
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Patient</th>
                            <th>Service</th>
                            <th>Doctor</th>
                            <th>Date & Time</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($appt = mysqli_fetch_assoc($appointments)): ?>
                            <tr>
                                <td>
                                    <strong><?= htmlspecialchars($appt['user_name']) ?></strong><br>
                                    <small><?= htmlspecialchars($appt['user_email']) ?></small>
                                </td>
                                <td><?= htmlspecialchars($appt['service_type']) ?></td>
                                <td><?= $appt['doctor_name'] ? htmlspecialchars($appt['doctor_name']) : 'Not assigned' ?></td>
                                <td>
                                    <?= date('M j, Y', strtotime($appt['appointment_date'])) ?><br>
                                    <?= date('g:i A', strtotime($appt['appointment_time'])) ?>
                                </td>
                                <td>
                                    <span class="badge <?= $appt['status'] == 'confirmed' ? 'badge-primary' : 'badge-warning' ?>">
                                        <?= ucfirst($appt['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <form method="POST" style="display: flex; align-items: center; gap: 0.5rem;">
                                            <input type="hidden" name="appointment_id" value="<?= $appt['id'] ?>">
                                            <input type="hidden" name="appointment_action" value="approve">
                                            <input type="text" name="meeting_id" placeholder="Meeting ID" required
                                                   style="padding: 0.5rem; border: 1px solid #ddd; border-radius: 4px; flex: 1;">
                                            <button type="submit" class="btn btn-success btn-sm">
                                                <i class="fas fa-check"></i> Approve
                                            </button>
                                        </form>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="appointment_id" value="<?= $appt['id'] ?>">
                                            <input type="hidden" name="appointment_action" value="cancel">
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="fas fa-times"></i> Cancel
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Doctors Section -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Active Doctors</h2>
            </div>
            
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Specialization</th>
                            <th>Age</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($doctor = mysqli_fetch_assoc($doctors)): ?>
                            <tr>
                                <td><?= htmlspecialchars($doctor['name']) ?></td>
                                <td><?= htmlspecialchars($doctor['specialization']) ?></td>
                                <td><?= $doctor['age'] ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <button onclick="openEditDoctorModal(
                                            <?= $doctor['id'] ?>, 
                                            '<?= addslashes(htmlspecialchars($doctor['name'])) ?>', 
                                            '<?= addslashes(htmlspecialchars($doctor['specialization'])) ?>', 
                                            '<?= addslashes(htmlspecialchars($doctor['bio'])) ?>', 
                                            <?= $doctor['age'] ?>
                                        )" class="btn btn-primary btn-sm">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="doctor_id" value="<?= $doctor['id'] ?>">
                                            <input type="hidden" name="delete_doctor" value="1">
                                            <button type="submit" class="btn btn-danger btn-sm" 
                                                onclick="return confirm('Are you sure you want to remove this doctor?')">
                                                <i class="fas fa-trash"></i> Remove
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Admins Section (only visible to superadmins) -->
        <?php if ($_SESSION['admin_role'] === 'superadmin'): ?>
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">System Admins</h2>
            </div>
            
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Created At</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($admin = mysqli_fetch_assoc($admins)): ?>
                            <tr>
                                <td><?= htmlspecialchars($admin['name']) ?></td>
                                <td><?= htmlspecialchars($admin['email']) ?></td>
                                <td><?= ucfirst($admin['role']) ?></td>
                                <td><?= date('M j, Y', strtotime($admin['created_at'])) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>
    </div>
    
    <!-- Add Doctor Modal -->
    <div id="addDoctorModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Add New Doctor</h3>
                <span class="close-modal" onclick="document.getElementById('addDoctorModal').style.display='none'">&times;</span>
            </div>
            <form method="POST">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Specialization</label>
                    <input type="text" name="specialization" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Bio</label>
                    <textarea name="bio" class="form-control" rows="3"></textarea>
                </div>
                <div class="form-group">
                    <label>Age</label>
                    <input type="number" name="age" min="25" max="80" class="form-control">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" onclick="document.getElementById('addDoctorModal').style.display='none'">Cancel</button>
                    <button type="submit" name="add_doctor" class="btn btn-primary">Add Doctor</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Edit Doctor Modal -->
    <div id="editDoctorModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Edit Doctor</h3>
                <span class="close-modal" onclick="document.getElementById('editDoctorModal').style.display='none'">&times;</span>
            </div>
            <form method="POST" id="editDoctorForm">
                <input type="hidden" name="doctor_id" id="editDoctorId">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="name" id="editDoctorName" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Specialization</label>
                    <input type="text" name="specialization" id="editDoctorSpecialization" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Bio</label>
                    <textarea name="bio" id="editDoctorBio" class="form-control" rows="3"></textarea>
                </div>
                <div class="form-group">
                    <label>Age</label>
                    <input type="number" name="age" id="editDoctorAge" min="25" max="80" class="form-control">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" onclick="document.getElementById('editDoctorModal').style.display='none'">Cancel</button>
                    <button type="submit" name="update_doctor" class="btn btn-primary">Update Doctor</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Add Admin Modal -->
    <div id="addAdminModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Create New Admin</h3>
                <span class="close-modal" onclick="document.getElementById('addAdminModal').style.display='none'">&times;</span>
            </div>
            <form method="POST">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" required minlength="8">
                </div>
                <div class="form-group">
                    <label>Role</label>
                    <select name="role" class="form-control" required>
                        <option value="admin">Admin</option>
                        <option value="superadmin">Super Admin</option>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" onclick="document.getElementById('addAdminModal').style.display='none'">Cancel</button>
                    <button type="submit" name="add_admin" class="btn btn-primary">Create Admin</button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        // Open edit doctor modal with prefilled data
        function openEditDoctorModal(id, name, specialization, bio, age) {
            document.getElementById('editDoctorId').value = id;
            document.getElementById('editDoctorName').value = name;
            document.getElementById('editDoctorSpecialization').value = specialization;
            document.getElementById('editDoctorBio').value = bio;
            document.getElementById('editDoctorAge').value = age;
            document.getElementById('editDoctorModal').style.display = 'flex';
        }
        
        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target == document.getElementById('addDoctorModal')) {
                document.getElementById('addDoctorModal').style.display = "none";
            }
            if (event.target == document.getElementById('editDoctorModal')) {
                document.getElementById('editDoctorModal').style.display = "none";
            }
            if (event.target == document.getElementById('addAdminModal')) {
                document.getElementById('addAdminModal').style.display = "none";
            }
        }
    </script>
</body>
</html>
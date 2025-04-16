<?php
session_start();
require 'config.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch user data
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Handle form submissions
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_profile'])) {
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $phone = mysqli_real_escape_string($conn, $_POST['phone']);
        
        $update_query = "UPDATE users SET name = ?, phone = ? WHERE id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("ssi", $name, $phone, $user_id);
        
        if ($stmt->execute()) {
            $_SESSION['user_name'] = $name;
            $success = "Profile updated successfully!";
        } else {
            $error = "Error updating profile: " . $conn->error;
        }
    } 
    elseif (isset($_POST['change_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        
        if (!password_verify($current_password, $user['password'])) {
            $error = "Current password is incorrect";
        } elseif ($new_password !== $confirm_password) {
            $error = "New passwords don't match";
        } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $new_password)) {
            $error = "Password must contain: 8+ chars, 1 uppercase, 1 number, 1 special character";
        } else {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->bind_param("si", $hashed_password, $user_id);
            
            if ($stmt->execute()) {
                $success = "Password changed successfully!";
            } else {
                $error = "Error changing password";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile | MedConnect</title>
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
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e8f0 100%);
            color: var(--dark);
            min-height: 100vh;
        }
        
        /* Header Styles */
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.5rem 5%;
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
        }
        
        .logo a {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--primary);
            text-decoration: none;
        }
        
        .logo span {
            color: var(--accent);
        }
        
        .nav-links {
            display: flex;
            gap: 2rem;
        }
        
        .nav-links a {
            text-decoration: none;
            color: var(--dark);
            font-weight: 500;
        }
        
        .auth-buttons .btn {
            margin-left: 1rem;
            padding: 0.5rem 1.5rem;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
        }
        
        .btn-primary {
            background: var(--primary);
            color: white;
        }
        
        .btn-outline {
            background: transparent;
            color: var(--primary);
            border: 2px solid var(--primary);
        }
        
        /* Profile Container */
        .profile-container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }
        
        .profile-header {
            display: flex;
            align-items: center;
            margin-bottom: 2rem;
        }
        
        .profile-pic {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background-color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2.5rem;
            margin-right: 1.5rem;
        }
        
        .profile-section {
            margin-bottom: 2rem;
            padding: 1.5rem;
            border: 1px solid #eee;
            border-radius: 10px;
        }
        
        .form-group {
            margin-bottom: 1rem;
        }
        
        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }
        
        input[type="text"],
        input[type="email"],
        input[type="tel"],
        input[type="password"] {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
        }
        
        .btn {
            padding: 0.8rem 1.5rem;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
        }
        
        .error { color: var(--accent); }
        .success { color: green; }
        
        .password-rules {
            font-size: 0.9rem;
            color: var(--gray);
            margin-top: 0.5rem;
        }
        
        /* Footer */
        footer {
            text-align: center;
            padding: 1.5rem;
            background: white;
            margin-top: 2rem;
        }
    </style>
    
<style>
/* Improved Button Styles */
.btn {
    display: inline-block;
    padding: 0.8rem 1.5rem;
    border-radius: 8px;
    font-weight: 600;
    text-align: center;
    text-decoration: none;
    cursor: pointer;
    transition: all 0.3s ease;
    border: none;
    width: 100%;
    margin-bottom: 0.5rem;
}

.btn-primary {
    background: #6c63ff;
    color: white;
}

.btn-primary:hover {
    background: #4d44db;
    transform: translateY(-2px);
}

.btn-secondary {
    background: #f0f0f0;
    color: #333;
    border: 1px solid #ddd;
}

.btn-secondary:hover {
    background: #e0e0e0;
    transform: translateY(-2px);
}

/* Form Improvements */
.form-control {
    width: 100%;
    padding: 0.8rem;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 1rem;
    margin-bottom: 0.5rem;
}

.password-rules {
    color: #666;
    font-size: 0.85rem;
    margin-top: -0.5rem;
    margin-bottom: 1rem;
}

.profile-section {
    margin-bottom: 2rem;
    padding: 1.5rem;
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

@media (min-width: 768px) {
    .btn {
        width: auto;
        margin-right: 1rem;
        margin-bottom: 0;
    }
    
    .appointment-actions {
        display: flex;
        gap: 1rem;
    }
}
</style>
</head>
<body>
    <!-- Header -->
    <header>
        <div class="logo">
            <a href="index.php">Med<span>Connect</span></a>
        </div>
        <nav class="nav-links">
            <a href="index.php">Home</a>
            <a href="services.php">Services</a>
            <a href="doctors.php">Doctors</a>
            <a href="profile.php">Profile</a>
        </nav>
        <div class="auth-buttons">
            <a href="logout.php" class="btn btn-outline">Logout</a>
        </div>
    </header>
    
    <!-- Main Content -->
    <div class="profile-container">
        <!-- Profile Header -->
        <div class="profile-header">
            <div class="profile-pic">
                <?= strtoupper(substr($user['name'], 0, 1)) ?>
            </div>
            <div>
                <h1><?= htmlspecialchars($user['name']) ?></h1>
                <p><?= htmlspecialchars($user['email']) ?></p>
            </div>
        </div>
        
        <!-- Messages -->
        <?php if($error): ?>
            <div class="error"><?= $error ?></div>
        <?php endif; ?>
        
        <?php if($success): ?>
            <div class="success"><?= $success ?></div>
        <?php endif; ?>
        
        <!-- Personal Information -->
        <div class="profile-section">
    <h2>Password Settings</h2>
    <div class="password-notice">
        <p><strong>Important:</strong> We don't offer password recovery. Choose your password carefully.</p>
    </div>
    
    <form method="POST">
        <div class="form-group">
            <label>Current Password</label>
            <input type="password" name="current_password" required class="form-control">
        </div>
        
        <div class="form-group">
            <label>New Password</label>
            <input type="password" name="new_password" required class="form-control"
                   pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[@$!%*?&]).{8,}">
            <div class="password-rules">
                <small>Must contain: 8+ characters, 1 uppercase, 1 number, 1 special character (@$!%*?&)</small>
            </div>
        </div>
        
        <div class="form-group">
            <label>Confirm New Password</label>
            <input type="password" name="confirm_password" required class="form-control">
        </div>
        
        <button type="submit" name="change_password" class="btn btn-primary">Update Password</button>
    </form>
</div>

<div class="profile-section">
    <h2>Appointment Management</h2>
    <div class="appointment-actions">
        <a href="index.php" class="btn btn-secondary">Book New Appointment</a>
        <a href="dashboard.php" class="btn btn-secondary">View My Appointments</a>
    </div>
</div>
            
            
    
    <!-- Footer -->
    <footer>
        <p>&copy; <?= date('Y') ?> MedConnect. All rights reserved.</p>
    </footer>
</body>
</html>
<?php
session_start();
require 'config.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: " . ($_GET['from'] ? 'index.php' : 'dashboard.php'));
    exit();
}
if (isset($_SESSION['admin_id'])) {
    header("Location: admin_dashboard.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    
    // First try admin login
    $admin_result = mysqli_query($conn, "SELECT id, name, password_hash, role FROM admins WHERE email='$email'");
    
    if (mysqli_num_rows($admin_result) == 1) {
        $admin = mysqli_fetch_assoc($admin_result);
        if (password_verify($password, $admin['password_hash'])) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_name'] = $admin['name'];
            $_SESSION['admin_role'] = $admin['role'];
            header("Location: admin_dashboard.php");
            exit();
        }
    }
    
    // If not admin, try regular user login
    $user_result = mysqli_query($conn, "SELECT id, name, password FROM users WHERE email='$email'");
    
    if (mysqli_num_rows($user_result) == 1) {
        $user = mysqli_fetch_assoc($user_result);
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            header("Location: " . ($_GET['from'] ? 'index.php' : 'dashboard.php'));
            exit();
        }
    }
    
    $error = "Invalid email or password!";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login | MedConnect</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <style>
    /* Base Styles */
    :root {
      --primary: #6c63ff;
      --secondary: #4d44db;
      --accent: #ff6584;
      --light: #f8f9fa;
      --dark: #212529;
      --gray: #6c757d;
      --success: #28a745;
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
      overflow-x: hidden;
    }
    
    /* Header */
    header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 1.5rem 5%;
      position: fixed;
      width: 100%;
      top: 0;
      z-index: 1000;
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(10px);
      box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
      animation: slideDown 0.8s ease-out;
    }
    
    .logo {
      font-size: 1.8rem;
      font-weight: 700;
      color: var(--primary);
      display: flex;
      align-items: center;
    }
    
    .logo span {
      color: var(--accent);
    }
    .nav-links a {
  margin: 0 5px; /* This gives spacing between each link */
  text-decoration: none;
  color: #111;
  font-weight: 500;
}
.nav-links {
  display: flex;
  align-items: center;
  justify-content: center;
}
.nav-links {
  display: flex;
  gap: 20px;
  align-items: center;
  justify-content: center;
}

    
    .auth-buttons .btn {
      margin-left: 1rem;
    }
    
    /* Login Container */
    .login-container {
      max-width: 400px;
      margin: 100px auto;
      padding: 2.5rem;
      background: white;
      border-radius: 15px;
      box-shadow: 0 15px 40px rgba(0,0,0,0.1);
      transform: translateY(20px);
      opacity: 0;
      animation: fadeInUp 0.6s 0.3s forwards;
      position: relative;
      overflow: hidden;
    }
    
    .login-container::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 5px;
      background: linear-gradient(90deg, var(--primary), var(--accent));
    }
    
    .login-container h2 {
      text-align: center;
      margin-bottom: 1.5rem;
      color: var(--primary);
      font-size: 1.8rem;
    }
    
    .error { 
      color: var(--accent);
      text-align: center;
      margin-bottom: 1rem;
      animation: shake 0.5s;
    }
    
    .form-group {
      margin-bottom: 1.5rem;
      position: relative;
    }
    
    .form-group input {
      width: 100%;
      padding: 0.8rem 1rem 0.8rem 40px;
      border: 2px solid #e9ecef;
      border-radius: 10px;
      font-size: 1rem;
      transition: all 0.3s ease;
    }
    
    .form-group input:focus {
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(108, 99, 255, 0.2);
      outline: none;
    }
    
    .form-group::before {
      font-family: 'Font Awesome 5 Free';
      font-weight: 900;
      position: absolute;
      left: 15px;
      top: 50%;
      transform: translateY(-50%);
      color: var(--gray);
    }
    
    .form-group.email::before {
      content: '\f0e0';
    }
    
    .form-group.password::before {
      content: '\f023';
    }
    
    .btn {
      padding: 0.8rem 1.8rem;
      border-radius: 50px;
      font-weight: 600;
      text-decoration: none;
      transition: all 0.3s ease;
      display: inline-block;
      cursor: pointer;
      border: none;
      font-size: 1rem;
    }
    
    .btn-primary {
      background: var(--primary);
      color: white;
      box-shadow: 0 10px 20px rgba(108, 99, 255, 0.3);
    }
    
    .btn-primary:hover {
      background: var(--secondary);
      transform: translateY(-3px);
      box-shadow: 0 15px 25px rgba(108, 99, 255, 0.4);
    }
    
    .btn-outline {
      background: transparent;
      color: var(--primary);
      border: 2px solid var(--primary);
    }
    
    .btn-outline:hover {
      background: var(--primary);
      color: white;
      transform: translateY(-3px);
    }
    
    .submit-btn {
      width: 100%;
      padding: 1rem;
      background: var(--primary);
      color: white;
      border: none;
      border-radius: 10px;
      font-size: 1rem;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      margin-top: 0.5rem;
      position: relative;
      overflow: hidden;
    }
    
    .submit-btn:hover {
      background: var(--secondary);
      transform: translateY(-3px);
      box-shadow: 0 10px 20px rgba(108, 99, 255, 0.3);
    }
    
    .submit-btn:active {
      transform: translateY(0);
    }
    
    .submit-btn::after {
      content: '';
      position: absolute;
      top: 50%;
      left: 50%;
      width: 5px;
      height: 5px;
      background: rgba(255, 255, 255, 0.5);
      opacity: 0;
      border-radius: 100%;
      transform: scale(1, 1) translate(-50%);
      transform-origin: 50% 50%;
    }
    
    .submit-btn:focus:not(:active)::after {
      animation: ripple 1s ease-out;
    }
    
    .login-footer {
      text-align: center;
      margin-top: 1.5rem;
      color: var(--gray);
      font-size: 0.9rem;
    }
    
    .login-footer a {
      color: var(--primary);
      text-decoration: none;
      font-weight: 500;
      transition: all 0.3s ease;
    }
    
    .login-footer a:hover {
      color: var(--secondary);
      text-decoration: underline;
    }
    
    .admin-hint {
      font-size: 0.8rem;
      color: var(--gray);
      text-align: center;
      margin-top: 1.5rem;
      padding-top: 1rem;
      border-top: 1px solid #eee;
    }
    
    /* Animations */
    @keyframes fadeInUp {
      from { 
        opacity: 0;
        transform: translateY(20px);
      }
      to { 
        opacity: 1;
        transform: translateY(0);
      }
    }
    
    @keyframes shake {
      0%, 100% { transform: translateX(0); }
      10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
      20%, 40%, 60%, 80% { transform: translateX(5px); }
    }
    
    @keyframes ripple {
      0% {
        transform: scale(0, 0);
        opacity: 1;
      }
      20% {
        transform: scale(25, 25);
        opacity: 1;
      }
      100% {
        opacity: 0;
        transform: scale(40, 40);
      }
    }
    
    @keyframes pulse {
      0% { transform: scale(1); }
      50% { transform: scale(1.05); }
      100% { transform: scale(1); }
    }
    
    /* Responsive */
    @media (max-width: 768px) {
      .login-container {
        margin: 120px auto;
        width: 90%;
      }
      
      .nav-links {
        display: none;
      }
    }
    </style>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
</head>
<body>
    
<header>
  <div class="logo">
     <a href = "index.php"> Med<span>Connect</span></a>
    </div>
    <nav class="nav-links">
        <a href="index.php">Home</a>
        <a href="services.php">Services</a>
        <a href="doctors.php">Doctors</a>
        
        <a href="about.php">About</a>

    </nav>
    <div class="auth-buttons">
        <?php if(isset($_SESSION['user_id'])): ?>
            <a href="dashboard.php" class="btn btn-outline">Dashboard</a>
            <a href="logout.php" class="btn btn-primary">Logout</a>
        <?php else: ?>
            <a href="login.php" class="btn btn-outline">Login</a>
            <a href="signup.php" class="btn btn-primary">Sign Up</a>
        <?php endif; ?>
    </div>
</header> 
  
  <div class="login-container animate__animated animate__fadeIn">
    <h2 class="animate__animated animate__fadeInDown">Login to MedConnect</h2>
    <?php if(isset($error)) echo "<p class='error animate__animated animate__shakeX'>$error</p>"; ?>
    <form method="POST">
        <div class="form-group email">
            <input type="email" name="email" placeholder="Email" required class="form-control">
        </div>
        <div class="form-group password">
            <input type="password" name="password" placeholder="Password" required class="form-control">
        </div>
        <button type="submit" class="submit-btn animate__animated animate__pulse animate__delay-1s">Login</button>
    </form>
    <div class="login-footer">
        <p>New user? <a href="signup.php">Sign up here</a></p>
    </div>
    <p class="admin-hint">Admins: Use your admin credentials</p>
  </div>

  <script>
    // Add focus effects to form inputs
    document.querySelectorAll('.form-control').forEach(input => {
      input.addEventListener('focus', function() {
        this.parentElement.style.transform = 'scale(1.02)';
      });
      input.addEventListener('blur', function() {
        this.parentElement.style.transform = 'scale(1)';
      });
    });

    // Add ripple effect to submit button
    const submitBtn = document.querySelector('.submit-btn');
    if (submitBtn) {
      submitBtn.addEventListener('click', function(e) {
        e.preventDefault();
        this.classList.add('animate__animated', 'animate__pulse');
        setTimeout(() => {
          this.classList.remove('animate__animated', 'animate__pulse');
          this.form.submit();
        }, 500);
      });
    }
  </script>
</body>
</html>
<?php
session_start();
require 'config.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: " . ($_GET['from'] ? 'book.php' : 'dashboard.php'));
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
            header("Location: " . ($_GET['from'] ? 'book.php' : 'dashboard.php'));
            exit();
        }
    }
    
    $error = "Invalid email or password!";
}
?>
?>

<!-- COPY ALL YOUR EXISTING CSS FROM book.php -->
<!DOCTYPE html>
<html>
<head>
    <title>Login | MedConnect</title>
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
    
    .nav-links {
      display: flex;
      gap: 2rem;
    }
    
    .nav-links a {
      text-decoration: none;
      color: var(--dark);
      font-weight: 500;
      position: relative;
      transition: all 0.3s ease;
    }
    
    .nav-links a:hover {
      color: var(--primary);
    }
    
    .nav-links a::after {
      content: '';
      position: absolute;
      width: 0;
      height: 2px;
      bottom: -5px;
      left: 0;
      background-color: var(--primary);
      transition: width 0.3s ease;
    }
    
    .nav-links a:hover::after {
      width: 100%;
    }
    
    .auth-buttons .btn {
      margin-left: 1rem;
    }
    
    /* Hero Section */
    .hero {
      display: flex;
      align-items: center;
      min-height: 100vh;
      padding: 0 5%;
      padding-top: 6rem;
    }
    
    .hero-content {
      flex: 1;
      padding-right: 2rem;
      animation: fadeInLeft 0.8s ease-out;
    }
    
    .hero-image {
      flex: 1;
      position: relative;
      animation: fadeInRight 0.8s ease-out;
    }
    
    .hero h1 {
      font-size: 3.5rem;
      font-weight: 700;
      margin-bottom: 1.5rem;
      line-height: 1.2;
    }
    
    .hero h1 span {
      color: var(--primary);
    }
    
    .hero p {
      font-size: 1.2rem;
      color: var(--gray);
      margin-bottom: 2rem;
      max-width: 600px;
    }
    
    .hero-buttons {
      display: flex;
      gap: 1rem;
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
    
    .doctor-illustration {
      width: 100%;
      max-width: 600px;
      height: auto;
      border-radius: 20px;
      box-shadow: 0 25px 50px rgba(0, 0, 0, 0.1);
      transform: perspective(1000px) rotateY(-10deg);
      transition: transform 0.5s ease;
    }
    
    .doctor-illustration:hover {
      transform: perspective(1000px) rotateY(0deg);
    }
    
    /* Booking Form */
    .booking-form-container {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.5);
      display: flex;
      justify-content: center;
      align-items: center;
      z-index: 2000;
      opacity: 0;
      pointer-events: none;
      transition: all 0.3s ease;
    }
    
    .booking-form-container.active {
      opacity: 1;
      pointer-events: all;
    }
    
    .booking-form {
      background: white;
      border-radius: 20px;
      width: 90%;
      max-width: 500px;
      padding: 2.5rem;
      box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
      transform: translateY(20px);
      transition: all 0.3s ease;
      position: relative;
    }
    
    .booking-form-container.active .booking-form {
      transform: translateY(0);
    }
    
    .close-form {
      position: absolute;
      top: 20px;
      right: 20px;
      font-size: 1.5rem;
      cursor: pointer;
      color: var(--gray);
      transition: all 0.3s ease;
    }
    
    .close-form:hover {
      color: var(--dark);
      transform: rotate(90deg);
    }
    
    .form-title {
      font-size: 1.8rem;
      margin-bottom: 1.5rem;
      color: var(--primary);
    }
    
    .form-group {
      margin-bottom: 1.5rem;
    }
    
    .form-group label {
      display: block;
      margin-bottom: 0.5rem;
      font-weight: 500;
      color: var(--dark);
    }
    
    .form-control {
      width: 100%;
      padding: 0.8rem 1rem;
      border: 2px solid #e9ecef;
      border-radius: 10px;
      font-size: 1rem;
      transition: all 0.3s ease;
    }
    
    .form-control:focus {
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(108, 99, 255, 0.2);
      outline: none;
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
      margin-top: 1rem;
    }
    
    .submit-btn:hover {
      background: var(--secondary);
      transform: translateY(-3px);
      box-shadow: 0 10px 20px rgba(108, 99, 255, 0.3);
    }
    
    /* Chatbot */
    .chatbot-container {
      position: fixed;
      bottom: 30px;
      right: 30px;
      z-index: 1000;
    }
    
    .chatbot-btn {
      width: 60px;
      height: 60px;
      border-radius: 50%;
      background: var(--primary);
      color: white;
      display: flex;
      justify-content: center;
      align-items: center;
      cursor: pointer;
      box-shadow: 0 10px 25px rgba(108, 99, 255, 0.3);
      transition: all 0.3s ease;
      border: none;
    }
    
    .chatbot-btn:hover {
      transform: scale(1.1);
      background: var(--secondary);
    }
    
    .chatbot-btn i {
      font-size: 1.5rem;
    }
    
    .chatbot-box {
      position: absolute;
      right: 0;
      bottom: 80px;
      width: 350px;
      background: white;
      border-radius: 15px;
      box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
      transform: scale(0.5);
      opacity: 0;
      pointer-events: none;
      transition: all 0.3s ease;
      overflow: hidden;
    }
    
    .chatbot-box.active {
      transform: scale(1);
      opacity: 1;
      pointer-events: all;
    }
    
    .chatbot-header {
      background: var(--primary);
      color: white;
      padding: 1rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    
    .chatbot-header h3 {
      font-weight: 600;
    }
    
    .close-chatbot {
      background: none;
      border: none;
      color: white;
      font-size: 1.2rem;
      cursor: pointer;
    }
    
    .chatbot-messages {
      height: 300px;
      padding: 1rem;
      overflow-y: auto;
    }
    
    .chatbot-message {
      margin-bottom: 1rem;
      max-width: 80%;
      padding: 0.8rem 1rem;
      border-radius: 15px;
      font-size: 0.9rem;
      line-height: 1.4;
      animation: fadeIn 0.3s ease-out;
    }
    
    .bot-message {
      background: #f1f1f1;
      color: var(--dark);
      border-bottom-left-radius: 5px;
      align-self: flex-start;
    }
    
    .user-message {
      background: var(--primary);
      color: white;
      border-bottom-right-radius: 5px;
      align-self: flex-end;
      margin-left: auto;
    }
    
    .chatbot-input {
      display: flex;
      padding: 1rem;
      border-top: 1px solid #eee;
    }
    
    .chatbot-input input {
      flex: 1;
      padding: 0.8rem;
      border: 1px solid #ddd;
      border-radius: 30px;
      outline: none;
      font-size: 0.9rem;
    }
    
    .chatbot-input button {
      margin-left: 0.5rem;
      background: var(--primary);
      color: white;
      border: none;
      border-radius: 50%;
      width: 40px;
      height: 40px;
      display: flex;
      justify-content: center;
      align-items: center;
      cursor: pointer;
      transition: all 0.3s ease;
    }
    
    .chatbot-input button:hover {
      background: var(--secondary);
    }
    
    /* Animations */
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }
    
    @keyframes fadeInLeft {
      from { opacity: 0; transform: translateX(-50px); }
      to { opacity: 1; transform: translateX(0); }
    }
    
    @keyframes fadeInRight {
      from { opacity: 0; transform: translateX(50px); }
      to { opacity: 1; transform: translateX(0); }
    }
    
    @keyframes slideDown {
      from { opacity: 0; transform: translateY(-50px); }
      to { opacity: 1; transform: translateY(0); }
    }
    
    @keyframes pulse {
      0% { transform: scale(1); }
      50% { transform: scale(1.05); }
      100% { transform: scale(1); }
    }
    
    /* Responsive */
    @media (max-width: 768px) {
      .hero {
        flex-direction: column;
        padding-top: 8rem;
        text-align: center;
      }
      
      .hero-content {
        padding-right: 0;
        margin-bottom: 3rem;
      }
      
      .hero-buttons {
        justify-content: center;
      }
      
      .nav-links {
        display: none;
      }
      
      .hero h1 {
        font-size: 2.5rem;
      }
      
      .chatbot-box {
        width: 300px;
      }
    }
    .admin-hint {
    font-size: 0.8rem;
    color: var(--gray);
    text-align: center;
    margin-top: 1rem;
}
  </style>
    <style>
        .login-container {
            max-width: 400px;
            margin: 100px auto;
            padding: 2rem;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .error { color: var(--accent); }
    </style>
</head>
<body>
    <!-- Copy your header from book.php here (without auth buttons) -->
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
      <a href="login.php" class="btn btn-outline">Login</a>
      <a href="signup.php" class="btn btn-primary">Sign Up</a>
    </div>
  </header> 
  <div class="login-container">
    <h2>Login to MedConnect</h2>
    <?php if(isset($error)) echo "<p class='error'>$error</p>"; ?>
    <form method="POST">
        <div class="form-group">
            <input type="email" name="email" placeholder="Email" required class="form-control">
        </div>
        <div class="form-group">
            <input type="password" name="password" placeholder="Password" required class="form-control">
        </div>
        <button type="submit" class="submit-btn">Login</button>
    </form>
    <p>New user? <a href="signup.php">Sign up here</a></p>
    
    <p class="admin-hint">Admins: Use your admin credentials</p>
</div>
</html>
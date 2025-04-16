<?php
session_start();

// Database connection (same as book.php)
$db_host = "localhost";
$db_user = "root";
$db_pass = ""; // Default XAMPP password is empty
$db_name = "medconnect";

try {
    $conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
    if (!$conn) {
        throw new Exception("Connection failed: " . mysqli_connect_error());
    }
} catch (Exception $e) {
    die("We're experiencing technical difficulties. Please try again later.");
}

// Handle form submission
$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    // Check if email already exists
    $check = mysqli_query($conn, "SELECT email FROM users WHERE email='$email'");
    if (mysqli_num_rows($check) > 0) {
        $error = "Email already registered!";
    } else {
        // Insert new user
        $result = mysqli_query($conn, 
            "INSERT INTO users (name, email, password) 
             VALUES ('$name', '$email', '$password')");
        function isPasswordStrong($password) {
  return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password);
}

if (!isPasswordStrong($_POST['password'])) {
  $error = "Password must contain: 8+ chars, 1 uppercase, 1 number, 1 special character";
}
        if ($result) {
            // Auto-login after registration
            $user_id = mysqli_insert_id($conn);
            $_SESSION['user_id'] = $user_id;
            $_SESSION['user_name'] = $name;
            
            // Redirect to booking page
            header("Location: book.php");
            exit();
        } else {
            $error = "Registration failed. Please try again.";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign Up | MedConnect</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
  
    .signup-container {
      max-width: 500px;
      margin: 6rem auto 3rem;
      padding: 2.5rem;
      background: white;
      border-radius: 20px;
      box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
      animation: fadeInUp 0.5s ease-out;
    }
    
    .signup-title {
      font-size: 2rem;
      color: var(--primary);
      margin-bottom: 1.5rem;
      text-align: center;
    }
    
    .error-message {
      color: var(--accent);
      text-align: center;
      margin-bottom: 1.5rem;
      font-weight: 500;
    }
    
    .login-link {
      text-align: center;
      margin-top: 1.5rem;
      color: var(--gray);
    }
    
    .login-link a {
      color: var(--primary);
      font-weight: 600;
      text-decoration: none;
    }
    .security-notice {
    border: 1px solid #ff6584;
    background: #fff5f7;
    padding: 15px;
    border-radius: 8px;
    margin: 20px 0;
}
.security-notice ul {
    padding-left: 20px;
}

  </style>
</head>
<body>
  <!-- Same Header as book.php -->
  <header>
    <div class="logo">Med<span>Connect</span></div>
    <nav class="nav-links">
      <a href="index.php">Home</a>
      <a href="services.php">Services</a>
      <a href="doctors.php">Doctors</a>
      <a href="about.html">About</a>
    </nav>
    <div class="auth-buttons">
      <a href="login.php" class="btn btn-outline">Login</a>
      <a href="signup.php" class="btn btn-primary">Sign Up</a>
    </div>
  </header>
  <div class="security-notice">
   

  <!-- Signup Form -->
  <div class="signup-container">
    <h2 class="signup-title">Create Your Account</h2>
    
    <?php if($error): ?>
      <p class="error-message"><?php echo $error; ?></p>
    <?php endif; ?>
    
    <form method="POST">
      <div class="form-group">
        <label for="name">Full Name</label>
        <input type="text" id="name" name="name" class="form-control" placeholder="John Doe" required>
      </div>
      
      <div class="form-group">
        <label for="email">Email Address</label>
        <input type="email" id="email" name="email" class="form-control" placeholder="your@email.com" required>
      </div>
      
      <div class="form-group">
    <label>Password</label>
    <input type="password" name="password" required 
           pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[@$!%*?&]).{8,}" 
           title="Must contain: 8+ chars, 1 uppercase, 1 number, 1 special character">
</div>
<div class="form-group">
    <label>Confirm Password</label>
    <input type="password" name="confirm_password" required>
</div>
      <button type="submit" class="submit-btn">
        <i class="fas fa-user-plus"></i> Create Account
      </button>
    </form>
    
    <p class="login-link">
      Already have an account? <a href="login.php">Login here</a>
    </p>
  </div>

  <!-- Same Chatbot as book.php -->
  <div class="chatbot-container">
    <button class="chatbot-btn" id="chatbot-toggler">
      <i class="fas fa-comment-dots"></i>
    </button>
    <div class="chatbot-box" id="chatbot-box">
      <div class="chatbot-header">
        <h3>MedConnect Assistant</h3>
        <button class="close-chatbot" id="close-chatbot">&times;</button>
      </div>
      <div class="chatbot-messages" id="chatbot-messages">
        <div class="chatbot-message bot-message">
          Hello! I can help with account questions or booking information.
        </div>
      </div>
      <div class="chatbot-input">
        <input type="text" id="user-input" placeholder="Type your message...">
        <button id="send-message"><i class="fas fa-paper-plane"></i></button>
      </div>
    </div>
  </div>
 <h3>Password Security Policy</h3>
    <ul>
        <li>We do not offer password recovery for security reasons</li>
        <li>Choose your password carefully</li>
        <li>Consider using a password manager</li>
        <li>Your password must contain:
            <ul>
                <li>At least 8 characters</li>
                <li>1 uppercase letter</li>
                <li>1 number</li>
                <li>1 special character</li>
            </ul>
        </li>
    </ul>
</div>
  <script>
    // Same Chatbot JavaScript as book.php
    // ... [Copy all the JavaScript from your book.php] ...
    
    // Add specific signup page responses
    const signupBotResponses = {
      'password|strong': 'Your password should be at least 8 characters long for security.',
      'account|sign up': 'Just fill in your name, email and password to create an account.',
      'already have account': 'If you already have an account, click "Login here" below the form.'
    };
    
    // Modify getBotResponse to include signup-specific responses
    function getBotResponse(userMessage) {
      userMessage = userMessage.toLowerCase();
      
      // Check signup-specific responses first
      for (const pattern in signupBotResponses) {
        const regex = new RegExp(pattern);
        if (regex.test(userMessage)) {
          return signupBotResponses[pattern];
        }
      }
      
      // Then check general responses (copied from book.php)
      for (const pattern in botResponses) {
        const regex = new RegExp(pattern);
        if (regex.test(userMessage)) {
          return botResponses[pattern];
        }
      }
      
      return botResponses['default'];
    }
  </script>
</body>
</html>
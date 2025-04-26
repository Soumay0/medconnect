<?php
session_start();

$db_host = "localhost";
$db_user = "root";
$db_pass = ""; 
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
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Password validation
    function isPasswordStrong($password) {
        return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password);
    }
    
    if (!isPasswordStrong($password)) {
        $error = "Password must contain: 8+ chars, 1 uppercase, 1 number, 1 special character";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match!";
    } else {
        // Check if email already exists
        $check = mysqli_query($conn, "SELECT email FROM users WHERE email='$email'");
        if (mysqli_num_rows($check) > 0) {
            $error = "Email already registered!";
        } else {
            // Hash password
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert new user
            $result = mysqli_query($conn, 
                "INSERT INTO users (name, email, password) 
                 VALUES ('$name', '$email', '$password_hash')");
            
            if ($result) {
                // Auto-login after registration
                $user_id = mysqli_insert_id($conn);
                $_SESSION['user_id'] = $user_id;
                $_SESSION['user_name'] = $name;
                
                // Redirect to booking page
                header("Location: index.php");
                exit();
            } else {
                $error = "Registration failed. Please try again.";
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
  <title>Sign Up | MedConnect</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
    
    /* Signup Container */
    .signup-container {
      max-width: 500px;
      margin: 120px auto 3rem;
      padding: 2.5rem;
      background: white;
      border-radius: 20px;
      box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
      transform: translateY(20px);
      opacity: 0;
      animation: fadeInUp 0.6s 0.3s forwards;
      position: relative;
      overflow: hidden;
    }
    
    .signup-container::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 5px;
      background: linear-gradient(90deg, var(--primary), var(--accent));
    }
    
    .signup-title {
      font-size: 2rem;
      color: var(--primary);
      margin-bottom: 1.5rem;
      text-align: center;
      animation: fadeInDown 0.5s;
    }
    
    .error-message {
      color: var(--accent);
      text-align: center;
      margin-bottom: 1.5rem;
      font-weight: 500;
      animation: shake 0.5s;
    }
    
    /* Form Styles */
    /* Updated Form Group Styles */
.form-group {
  margin-bottom: 1.5rem;
  position: relative;
}

.form-control {
  width: 100%;
  padding: 0.8rem 1rem 0.8rem 40px; /* Adjusted padding to accommodate icon */
  border: 2px solid #e9ecef;
  border-radius: 10px;
  font-size: 1rem;
  transition: all 0.3s ease;
}

/* Fixed Icon Positioning */
.form-group::before {
  font-family: 'Font Awesome 6 Free';
  font-weight: 900;
  position: absolute;
  left: 15px;
  top: 38px; /* Adjusted to align with input field */
  color: var(--gray);
  z-index: 2;
}

/* Specific icon adjustments */
.form-group.name::before {
  content: '\f007';
}

.form-group.email::before {
  content: '\f0e0';
}

.form-group.password::before {
  content: '\f023';
}

.form-group.confirm-password::before {
  content: '\f084';
}

/* Label adjustments */
.form-group label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 500;
  color: var(--dark);
  margin-left: 40px; /* Added to prevent overlap with icon */
}
    /* Password Strength Meter */
    .password-strength {
      height: 5px;
      background: #e9ecef;
      border-radius: 5px;
      margin-top: 0.5rem;
      overflow: hidden;
      position: relative;
    }
    
    .password-strength::after {
      content: '';
      position: absolute;
      left: 0;
      top: 0;
      height: 100%;
      width: 0;
      background: var(--accent);
      transition: width 0.3s ease, background 0.3s ease;
    }
    
    .password-strength.weak::after {
      width: 30%;
      background: #ff4757;
    }
    
    .password-strength.medium::after {
      width: 60%;
      background: #ffa502;
    }
    
    .password-strength.strong::after {
      width: 100%;
      background: var(--success);
    }
    
    .password-hints {
      font-size: 0.8rem;
      color: var(--gray);
      margin-top: 0.5rem;
    }
    
    .password-hints ul {
      padding-left: 1.2rem;
    }
    
    .password-hints li.valid {
      color: var(--success);
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
    
    /* Submit Button */
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
    
    .submit-btn i {
      margin-right: 8px;
    }
    
    /* Security Notice */
    .security-notice {
      border: 1px solid rgba(255, 101, 132, 0.3);
      background: rgba(255, 101, 132, 0.05);
      padding: 1.2rem;
      border-radius: 10px;
      margin: 2rem 0 1rem;
      animation: fadeIn 0.8s;
    }
    
    .security-notice h3 {
      color: var(--accent);
      margin-bottom: 0.8rem;
      font-size: 1.1rem;
    }
    
    .security-notice ul {
      padding-left: 1.2rem;
      font-size: 0.9rem;
      color: var(--gray);
    }
    
    .security-notice li {
      margin-bottom: 0.3rem;
    }
    
    .security-notice li strong {
      color: var(--dark);
    }
    
    /* Login Link */
    .login-link {
      text-align: center;
      margin-top: 1.5rem;
      color: var(--gray);
      font-size: 0.95rem;
    }
    
    .login-link a {
      color: var(--primary);
      font-weight: 600;
      text-decoration: none;
      transition: all 0.3s ease;
    }
    
    .login-link a:hover {
      color: var(--secondary);
      text-decoration: underline;
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
    
    @keyframes fadeInDown {
      from { 
        opacity: 0;
        transform: translateY(-20px);
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
    
    @keyframes slideDown {
      from { opacity: 0; transform: translateY(-50px); }
      to { opacity: 1; transform: translateY(0); }
    }
    
    /* Responsive */
    @media (max-width: 768px) {
      .signup-container {
        margin: 120px auto 2rem;
        width: 90%;
        padding: 1.8rem;
      }
      
      .nav-links {
        display: none;
      }
      
      .signup-title {
        font-size: 1.8rem;
      }
    }
  </style>
</head>
<body>
  <header>
    <div class="logo">
      <a href="index.php">Med<span>Connect</span></a>
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
  
  <div class="signup-container animate__animated animate__fadeIn">
    <h2 class="signup-title animate__animated animate__fadeInDown">Create Your Account</h2>
    
    <?php if($error): ?>
      <p class="error-message animate__animated animate__shakeX"><?php echo $error; ?></p>
    <?php endif; ?>
    
    <form method="POST">
      <div class="form-group name">
        <label for="name">Full Name</label>
        <input type="text" id="name" name="name" class="form-control" placeholder="John Doe" required>
      </div>
      
      <div class="form-group email">
        <label for="email">Email Address</label>
        <input type="email" id="email" name="email" class="form-control" placeholder="your@email.com" required>
      </div>
      
      <div class="form-group password">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" class="form-control" 
               pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[@$!%*?&]).{8,}" 
               title="Must contain: 8+ chars, 1 uppercase, 1 number, 1 special character" required>
        <div class="password-strength" id="password-strength"></div>
        <div class="password-hints">
          <ul>
            <li id="length-hint">Minimum 8 characters</li>
            <li id="uppercase-hint">At least 1 uppercase letter</li>
            <li id="number-hint">At least 1 number</li>
            <li id="special-hint">At least 1 special character (@$!%*?&)</li>
          </ul>
        </div>
      </div>
      
      <div class="form-group confirm-password">
        <label for="confirm_password">Confirm Password</label>
        <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
      </div>
      
      <div class="security-notice">
        <h3>Password Security Policy</h3>
        <ul>
          <li><strong>We don't offer password recovery</strong> for security reasons</li>
          <li>Choose your password carefully</li>
          <li>Consider using a password manager</li>
          <li>Your password must meet all requirements shown above</li>
        </ul>
      </div>
      
      <button type="submit" class="submit-btn animate__animated animate__pulse animate__delay-1s">
        <i class="fas fa-user-plus"></i> Create Account
      </button>
    </form>
    
    <p class="login-link">
      Already have an account? <a href="login.php">Login here</a>
    </p>
  </div>

  <script>
    // Password strength indicator
    const passwordInput = document.getElementById('password');
    const passwordStrength = document.getElementById('password-strength');
    const lengthHint = document.getElementById('length-hint');
    const uppercaseHint = document.getElementById('uppercase-hint');
    const numberHint = document.getElementById('number-hint');
    const specialHint = document.getElementById('special-hint');
    
    passwordInput.addEventListener('input', function() {
      const password = this.value;
      let strength = 0;
      
      // Check length
      if (password.length >= 8) {
        strength += 1;
        lengthHint.classList.add('valid');
      } else {
        lengthHint.classList.remove('valid');
      }
      
      // Check uppercase
      if (/[A-Z]/.test(password)) {
        strength += 1;
        uppercaseHint.classList.add('valid');
      } else {
        uppercaseHint.classList.remove('valid');
      }
      
      // Check number
      if (/\d/.test(password)) {
        strength += 1;
        numberHint.classList.add('valid');
      } else {
        numberHint.classList.remove('valid');
      }
      
      // Check special char
      if (/[@$!%*?&]/.test(password)) {
        strength += 1;
        specialHint.classList.add('valid');
      } else {
        specialHint.classList.remove('valid');
      }
      
      // Update strength meter
      passwordStrength.className = 'password-strength';
      if (password.length > 0) {
        if (strength <= 1) {
          passwordStrength.classList.add('weak');
        } else if (strength <= 3) {
          passwordStrength.classList.add('medium');
        } else {
          passwordStrength.classList.add('strong');
        }
      }
    });
    
    // Form validation
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
      const password = document.getElementById('password').value;
      const confirmPassword = document.getElementById('confirm_password').value;
      
      if (password !== confirmPassword) {
        e.preventDefault();
        alert('Passwords do not match!');
        return false;
      }
      
      // Add loading animation
      const submitBtn = document.querySelector('.submit-btn');
      submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating Account...';
      submitBtn.disabled = true;
    });
    
    // Add focus effects to form inputs
    document.querySelectorAll('.form-control').forEach(input => {
      input.addEventListener('focus', function() {
        this.parentElement.style.transform = 'scale(1.02)';
      });
      input.addEventListener('blur', function() {
        this.parentElement.style.transform = 'scale(1)';
      });
    });
  </script>
</body>
</html>
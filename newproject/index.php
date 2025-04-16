<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check session
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// DB connection
$conn = mysqli_connect("localhost", "root", "", "medconnect");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Handle form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Required fields
    $required = ['doctor_id', 'date', 'time', 'phone'];
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            die("Missing required field: $field");
        }
    }

    // Sanitize and assign
    $user_id = (int)$_SESSION['user_id'];
    $doctor_id = (int)$_POST['doctor_id'];
    $service_type = trim($_POST['service_type']);
    $date = $_POST['date'];
    $time = $_POST['time'];
    $notes = trim($_POST['notes'] ?? '');
    $phone = trim($_POST['phone']);

    // Prepare insert
    $stmt = $conn->prepare("INSERT INTO appointments 
    (user_id, doctor_id, appointment_date, appointment_time, notes, phone) 
    VALUES (?, ?, ?, ?, ?, ?)");

    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("iissss", 
    $user_id, $doctor_id, $date, $time, $notes, $phone
);
if ($stmt->execute()) {
  header("Location: dashboard.php?success=Appointment+booked");
  exit();
} else {
  error_log("Database error: " . $stmt->error);
  header("Location: index.php?error=Failed+to+book+appointment");
  exit();
}
}

// Fetch doctors
$doctors = mysqli_query($conn, "SELECT id, name, specialization FROM doctors WHERE is_active = 1");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>MedConnect | Premium Healthcare Booking</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
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
      height: 100vh;
      background: rgba(0, 0, 0, 0.5);
      display: flex;
      justify-content: center;
      align-items: center;
      z-index: 1000;
      opacity: 0;
      pointer-events: none;
      transition: all 0.3s ease;
      position: fixed;
     
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
      height: 85vh;
      padding: 2.5rem;
      box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
      transform: translateY(20px);
      transition: all 0.3s ease;
      position: relative;
      margin-top: 40px;
      max-height: 90vh;
      overflow-y: auto;
      scroll-behavior: smooth;
    }

    .booking-form::-webkit-scrollbar {
  width: 8px;
}

.booking-form::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 10px;
}

.booking-form::-webkit-scrollbar-thumb {
  background: #888;
  border-radius: 10px;
}

.booking-form::-webkit-scrollbar-thumb:hover {
  background: #555;
}

    
    .booking-form-container.active .booking-form {
      transform: translateY(0);
    }
    
    .close-form {
    position: absolute;
    top: 15px;
    right: 15px;
    font-size: 24px;
    cursor: pointer;
    z-index: 1001; /* Above other elements */
    padding: 5px 10px;
}

.close-form:hover {
    color: #ff0000;
    transform: scale(1.2);
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
  transition: all 0.5s ease; /* Slowed down transition */
  margin-top: 1rem;
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

/* Loading animation */
.submit-btn.loading {
  pointer-events: none;
  opacity: 0.8;
}

.submit-btn.loading::after {
  content: "";
  position: absolute;
  width: 20px;
  height: 20px;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  margin: auto;
  border: 3px solid transparent;
  border-top-color: white;
  border-radius: 50%;
  animation: button-loading-spinner 1s linear infinite;
}

@keyframes button-loading-spinner {
  from {
    transform: rotate(0turn);
  }
  to {
    transform: rotate(1turn);
  }
}

/* Success animation */
.submit-btn.success {
  background: var(--success);
  animation: button-pulse 1.5s ease;
}

@keyframes button-pulse {
  0% {
    transform: scale(1);
    box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.7);
  }
  50% {
    transform: scale(1.05);
    box-shadow: 0 0 0 10px rgba(40, 167, 69, 0);
  }
  100% {
    transform: scale(1);
    box-shadow: 0 0 0 0 rgba(40, 167, 69, 0);
  }
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
  </style>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
<script>
document.getElementById('open-booking').addEventListener('click', function() {
    console.log('Button event listener triggered');
    document.getElementById('booking-form').classList.add('active');
});
</script>
  <!-- Header -->
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
            <a href="signup.html" class="btn btn-primary">Sign Up</a>
        <?php endif; ?>
    </div>
</header>

  <!-- Hero Section -->
  <section class="hero">
    <div class="hero-content">
      <h1>Premium Healthcare <span>Made Simple</span></h1>
      <p>Book appointments with top specialists in minutes. Our AI assistant will guide you to the perfect doctor for your needs.</p>
      <div class="hero-buttons">
    <?php if(isset($_SESSION['user_id'])): ?>
        <button id="open-booking" class="btn btn-primary">Book Now</button>
    <?php else: ?>
        <a href="login.php?from=book" class="btn btn-primary">Book Now</a>
    <?php endif; ?>
    <a href="about.php" class="btn btn-outline">Learn More</a>
</div>
    </div>
    <div class="hero-image">
    <img src="https://cdn-icons-png.flaticon.com/128/2382/2382533.png" alt="Doctor Illustration" class="doctor-illustration" style="width: 350px; height: auto;">

    </div>
  </section>

  <!-- Booking Form -->
  <div class="booking-form-container" id="booking-form">
  <div class="booking-form">
  <span class="close-form" id="close-form" aria-label="Close form">&times;</span>
    <h2 class="form-title">Quick Appointment</h2>
    
    <?php if (isset($_GET['error'])): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div>
    <?php endif; ?>
    
    <form method="POST" action="index.php" id="appointment-form" enctype="multipart/form-data" onsubmit="return validateForm()">
      <div class="form-row">
      <div class="form-group">
    <label for="doctor_id">Select Doctor *</label>
<select id="doctor_id" name="doctor_id" class="form-control" required>
    <option value="">-- Select Doctor --</option>
    <?php while($doctor = mysqli_fetch_assoc($doctors)): ?>
        <option value="<?= $doctor['id'] ?>">
            Dr. <?= $doctor['name'] ?> 
            <?php if(isset($doctor['specialization'])): ?>
                (<?= $doctor['specialization'] ?>)
            <?php endif; ?>
        </option>
    <?php endwhile; ?>
</select>
</div>
        <div class="form-group half-width">
          <label for="date">Date *</label>
          <input type="date" id="date" name="date" class="form-control" required 
                 min="<?= date('Y-m-d') ?>" value="<?= date('Y-m-d') ?>">
        </div>
      </div>
      
      <div class="form-row">
        <div class="form-group half-width">
          <label for="time">Time *</label>
          <select id="time" name="time" class="form-control" required>
            <option value="">Select time</option>
            <<option value="09:00:00">9:00 AM</option> <!-- HH:MM:SS -->
          </select>
        </div>
        <div class="form-group half-width">
          <label for="phone">Phone *</label>
          <input type="tel" id="phone" name="phone" class="form-control" 
                 placeholder="Your phone number" required>
        </div>
      </div>
      
      <div class="form-group">
        <label for="notes">Brief Notes (Optional)</label>
        <textarea id="notes" name="notes" class="form-control" rows="2" 
                  placeholder="Any specific concerns"></textarea>
      </div>
      
      <button type="submit" class="submit-btn">
        <i class="fas fa-calendar-check"></i> Confirm Booking
      </button>
    </form>
  </div>
</div>


  <!-- AI Chatbot -->
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
          Hello! I'm your MedConnect assistant. How can I help you today?
        </div>
      </div>
      <div class="chatbot-input">
        <input type="text" id="user-input" placeholder="Type your message...">
        <button id="send-message"><i class="fas fa-paper-plane"></i></button>
      </div>
    </div>
  </div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Close form functionality
    const closeBtn = document.getElementById('close-form');
    const bookingForm = document.getElementById('booking-form');
    
    if (closeBtn && bookingForm) {
        closeBtn.addEventListener('click', function(e) {
            e.stopPropagation(); // Prevent event bubbling
            console.log('Close button clicked'); // Debug log
            bookingForm.classList.remove('active');
        });
        
        // Close when clicking outside form
        bookingForm.addEventListener('click', function(e) {
            if (e.target === bookingForm) {
                console.log('Clicked outside - closing form');
                bookingForm.classList.remove('active');
            }
        });
    } else {
        console.error('Could not find close button or booking form');
    }
});
</script>
  <script>
 document.addEventListener('DOMContentLoaded', function() {
    // Simple button click handler
    
    const bookBtn = document.getElementById('open-booking');
    if (bookBtn) {
        bookBtn.addEventListener('click', function() {
            console.log('Book Now clicked - opening form');
            document.getElementById('booking-form').classList.add('active');
        });
    }

    // Simple form submission
    const bookForm = document.getElementById('appointment-form');
  
  if (bookForm) {
    bookForm.addEventListener('submit', function(e) {
      e.preventDefault();
      
      const submitBtn = this.querySelector('.submit-btn');
      submitBtn.classList.add('loading');
      
      // Simulate a slow transition (remove this timeout in production)
      setTimeout(() => {
        // Show success state
        submitBtn.classList.remove('loading');
        submitBtn.classList.add('success');
        submitBtn.innerHTML = '<i class="fas fa-check"></i> Booking Confirmed!';
        
        // Submit the form after animation
        setTimeout(() => {
          this.submit();
        }, 1500);
      }, 2000); // Adjust this timeout to match your actual API call time
    });
  }

  // ==================== AI Chatbot Functionality ====================
  const chatbotBtn = document.getElementById('chatbot-toggler');
  const chatbotBox = document.getElementById('chatbot-box');
  const closeBtn = document.getElementById('close-chatbot');
  const sendBtn = document.getElementById('send-message');
  const userInput = document.getElementById('user-input');
  const chatMessages = document.getElementById('chatbot-messages');

  // Chatbot responses database
  const botResponses = {
    greetings: ["Hello! 👋", "Hi there!", "Welcome to MedConnect! How can I help?"],
    appointment: "To book an appointment:\n1. Click 'Book Now'\n2. Select doctor & time\n3. Submit the form\nOr tell me your preferred date!",
    doctors: "We have specialists in:\n- Cardiology ❤️\n- Dermatology 🧴\n- Pediatrics 👶\nWhich department do you need?",
    emergency: "🚨 For emergencies, please call 108 or visit the nearest hospital immediately!",
    hours: "⌚ Our clinic hours:\nMon-Fri: 8AM-6PM\nSat: 9AM-2PM\nSun: Closed",
    contact: "📞 Call us at: (123) 456-7890\n✉️ Email: help@medconnect.com\n📍 Location: 123 Health St, Medical City",
    default: "I can help with:\n1. Booking appointments\n2. Doctor information\n3. Clinic hours\n4. Emergency contacts\nHow may I assist you?"
  };

  // Open/close chatbot
  if (chatbotBtn && chatbotBox) {
    chatbotBtn.addEventListener('click', () => {
      chatbotBox.classList.toggle('active');
      if (chatbotBox.classList.contains('active')) {
        // Auto-scroll to bottom when opening
        setTimeout(() => {
          chatMessages.scrollTop = chatMessages.scrollHeight;
        }, 100);
      }
    });
  }
  
  if (closeBtn && chatbotBox) {
    closeBtn.addEventListener('click', () => chatbotBox.classList.remove('active'));
  }

  // Send message functionality
  if (sendBtn && userInput) {
    sendBtn.addEventListener('click', sendMessage);
    userInput.addEventListener('keypress', (e) => {
      if (e.key === 'Enter') sendMessage();
    });
  }

  // Initial greeting
  setTimeout(() => {
    addBotMessage(botResponses.greetings[0]);
  }, 800);

  // Core chatbot functions
  function sendMessage() {
    if (!userInput || !chatMessages) return;
    
    const message = userInput.value.trim();
    if (!message) return;

    addUserMessage(message);
    userInput.value = '';
    showTypingIndicator();
    
    setTimeout(() => {
      removeTypingIndicator();
      const botReply = generateBotResponse(message);
      addBotMessage(botReply);
    }, 800 + Math.random() * 1200); // Random delay for "typing" effect
  }

  function generateBotResponse(userMessage) {
    userMessage = userMessage.toLowerCase();
    
    if (/(hi|hello|hey)/.test(userMessage)) {
      return randomResponse(botResponses.greetings);
    }
    if (/(book|appointment|schedule)/.test(userMessage)) {
      return botResponses.appointment;
    }
    if (/(doctor|specialist|physician)/.test(userMessage)) {
      return botResponses.doctors;
    }
    if (/(emergency|urgent|help)/.test(userMessage)) {
      return botResponses.emergency;
    }
    if (/(time|hour|when|open|timing)/.test(userMessage)) {
      return botResponses.hours;
    }
    if (/(contact|call|email|reach)/.test(userMessage)) {
      return botResponses.contact;
    }
    return botResponses.default;
  }

  // Helper functions
  function randomResponse(responses) {
    return responses[Math.floor(Math.random() * responses.length)];
  }

  function addUserMessage(text) {
    addMessage(text, 'user');
  }

  function addBotMessage(text) {
    addMessage(text, 'bot');
  }

  function addMessage(text, type) {
    if (!chatMessages) return;
    
    const msgDiv = document.createElement('div');
    msgDiv.className = `chatbot-message ${type}-message`;
    msgDiv.innerHTML = text.replace(/\n/g, '<br>');
    chatMessages.appendChild(msgDiv);
    chatMessages.scrollTop = chatMessages.scrollHeight;
  }

  function showTypingIndicator() {
    if (!chatMessages) return;
    
    const typingDiv = document.createElement('div');
    typingDiv.id = 'typing-indicator';
    typingDiv.className = 'chatbot-message bot-message typing';
    typingDiv.innerHTML = '<span></span><span></span><span></span>';
    chatMessages.appendChild(typingDiv);
    chatMessages.scrollTop = chatMessages.scrollHeight;
  }

  function removeTypingIndicator() {
    const typingEl = document.getElementById('typing-indicator');
    if (typingEl) typingEl.remove();
  }
});
  </script>
  <script>
function validateForm() {
    console.log("Form validation started");
    const required = ['doctor_id', 'service_type', 'date', 'time', 'phone'];
    let valid = true;
    
    required.forEach(field => {
        const el = document.querySelector(`[name="${field}"]`);
        if (!el || !el.value) {
            console.error(`Missing required field: ${field}`);
            el.style.border = "1px solid red";
            valid = false;
        }
    });
    
    if (!valid) {
        alert("Please fill all required fields (marked in red)");
        return false;
    }
    
    console.log("Form validation passed, submitting...");
    return true;
}
</script>
</body>
</html>
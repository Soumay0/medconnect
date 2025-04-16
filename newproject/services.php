<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Our Services | MedConnect</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    /* Base Styles - Matching Home Page */
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
    
    /* Header - Same as Home Page */
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
    
    /* Main Content */
    .services-container {
      padding: 6rem 5% 3rem;
      max-width: 1200px;
      margin: 0 auto;
    }
    
    .page-title {
      font-size: 2.5rem;
      font-weight: 700;
      margin-bottom: 1.5rem;
      color: var(--primary);
      text-align: center;
    }
    
    .page-subtitle {
      font-size: 1.2rem;
      color: var(--gray);
      margin-bottom: 3rem;
      text-align: center;
      max-width: 800px;
      margin-left: auto;
      margin-right: auto;
    }
    
    /* Services Grid */
    .services-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 2rem;
      margin-bottom: 4rem;
    }
    
    .service-card {
      background: white;
      border-radius: 15px;
      padding: 2rem;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
      transition: all 0.3s ease;
      text-align: center;
    }
    
    .service-card:hover {
      transform: translateY(-10px);
      box-shadow: 0 15px 40px rgba(108, 99, 255, 0.1);
    }
    
    .service-icon {
      width: 80px;
      height: 80px;
      background: var(--light);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 1.5rem;
      color: var(--primary);
      font-size: 2rem;
    }
    
    .service-title {
      font-size: 1.3rem;
      font-weight: 600;
      margin-bottom: 1rem;
      color: var(--dark);
    }
    
    .service-description {
      color: var(--gray);
      margin-bottom: 1.5rem;
    }
    
    /* Booking Section */
    .booking-section {
      background: white;
      border-radius: 15px;
      padding: 3rem;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
      margin-bottom: 4rem;
      text-align: center;
    }
    
    .section-title {
      font-size: 1.8rem;
      margin-bottom: 1.5rem;
      color: var(--primary);
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
    
    /* Chatbot - Same as Home Page */
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
    }
    
    .bot-message {
      background: #f1f1f1;
      color: var(--dark);
      border-bottom-left-radius: 5px;
    }
    
    .user-message {
      background: var(--primary);
      color: white;
      border-bottom-right-radius: 5px;
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
    
    /* Responsive */
    @media (max-width: 768px) {
      .services-container {
        padding: 6rem 2rem 3rem;
      }
      
      .nav-links {
        display: none;
      }
      
      .booking-section {
        padding: 2rem 1.5rem;
      }
      
      .chatbot-box {
        width: 300px;
      }
    }
  </style>
</head>
<body>
  <!-- Header -->
  <header>
  <div class="logo">
     <a href = "index.php"> Med<span>Connect</span></a>
    </div>
    <nav class="nav-links">
      <a href="index.php">Home</a>
      <a href="services.php" class="active">Services</a>
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

  <!-- Main Content -->
  <div class="services-container">
    <h1 class="page-title">Our Healthcare Services</h1>
    <p class="page-subtitle">Premium healthcare services tailored to your needs. Book appointments with top specialists in minutes.</p>
    
    <div class="services-grid">
      <div class="service-card">
        <div class="service-icon">
          <i class="fas fa-heartbeat"></i>
        </div>
        <h3 class="service-title">Cardiology</h3>
        <p class="service-description">Comprehensive heart care including diagnostics, treatment, and preventive cardiology services.</p>
        <a href="#book" class="btn btn-primary">Book Now</a>
      </div>
      
      <div class="service-card">
        <div class="service-icon">
          <i class="fas fa-allergies"></i>
        </div>
        <h3 class="service-title">Dermatology</h3>
        <p class="service-description">Skin care treatments for acne, eczema, psoriasis, and cosmetic dermatology procedures.</p>
        <a href="#book" class="btn btn-primary">Book Now</a>
      </div>
      
      <div class="service-card">
        <div class="service-icon">
          <i class="fas fa-baby"></i>
        </div>
        <h3 class="service-title">Pediatrics</h3>
        <p class="service-description">Specialized care for infants, children, and adolescents with child-friendly approaches.</p>
        <a href="#book" class="btn btn-primary">Book Now</a>
      </div>
      
      <div class="service-card">
        <div class="service-icon">
          <i class="fas fa-brain"></i>
        </div>
        <h3 class="service-title">Neurology</h3>
        <p class="service-description">Diagnosis and treatment of disorders affecting the brain, spine, and nervous system.</p>
        <a href="#book" class="btn btn-primary">Book Now</a>
      </div>
      
      <div class="service-card">
        <div class="service-icon">
          <i class="fas fa-x-ray"></i>
        </div>
        <h3 class="service-title">Diagnostics</h3>
        <p class="service-description">Advanced imaging and laboratory tests for accurate diagnosis and treatment planning.</p>
        <a href="#book" class="btn btn-primary">Book Now</a>
      </div>
      
      <div class="service-card">
        <div class="service-icon">
          <i class="fas fa-procedures"></i>
        </div>
        <h3 class="service-title">Surgery</h3>
        <p class="service-description">Minimally invasive surgical procedures with faster recovery times and better outcomes.</p>
        <a href="#book" class="btn btn-primary">Book Now</a>
      </div>
    </div>
    
    <div class="booking-section" id="book">
      <h2 class="section-title">Ready to Book Your Appointment?</h2>
      <p style="color: var(--gray); margin-bottom: 2rem;">Our AI assistant will guide you to the perfect doctor for your needs.</p>
      <a href="index.php" class="btn btn-primary" style="padding: 1rem 2.5rem; font-size: 1.1rem;">
        <i class="fas fa-calendar-check" style="margin-right: 8px;"></i> Book Appointment Now
      </a>
    </div>
  </div>

  <!-- Chatbot -->
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
    // Chatbot functionality
    const chatbotToggler = document.getElementById('chatbot-toggler');
    const chatbotBox = document.getElementById('chatbot-box');
    const closeChatbot = document.getElementById('close-chatbot');
    const userInput = document.getElementById('user-input');
    const sendMessageBtn = document.getElementById('send-message');
    const chatMessages = document.getElementById('chatbot-messages');

    // Toggle chatbot
    chatbotToggler.addEventListener('click', () => {
      chatbotBox.classList.toggle('active');
    });

    closeChatbot.addEventListener('click', () => {
      chatbotBox.classList.remove('active');
    });

    // Chatbot responses
    const botResponses = {
      greeting: ["Hello! How can I assist you today?", "Hi there! What can I help you with?"],
      services: "We offer Cardiology, Dermatology, Pediatrics, Neurology, Diagnostics, and Surgery services.",
      booking: "You can book appointments by clicking the 'Book Now' buttons or visiting our booking page.",
      doctors: "Our doctors are highly qualified specialists in their fields. You can view them on our Doctors page.",
      hours: "Our clinic is open Monday to Friday from 8AM to 6PM, and Saturday from 9AM to 2PM.",
      default: "I can help with information about our services, doctors, and booking appointments. What would you like to know?"
    };

    // Send message function
    function sendMessage() {
      const message = userInput.value.trim();
      if (!message) return;

      // Add user message
      addMessage(message, 'user');
      userInput.value = '';
      
      // Simulate bot typing
      setTimeout(() => {
        let botMessage = getBotResponse(message);
        addMessage(botMessage, 'bot');
      }, 500);
    }

    // Get bot response
    function getBotResponse(message) {
      message = message.toLowerCase();
      
      if (message.includes('hi') || message.includes('hello')) {
        return randomResponse(botResponses.greeting);
      } else if (message.includes('service') || message.includes('offer')) {
        return botResponses.services;
      } else if (message.includes('book') || message.includes('appointment')) {
        return botResponses.booking;
      } else if (message.includes('doctor') || message.includes('specialist')) {
        return botResponses.doctors;
      } else if (message.includes('hour') || message.includes('time') || message.includes('open')) {
        return botResponses.hours;
      } else {
        return botResponses.default;
      }
    }

    // Helper functions
    function randomResponse(responses) {
      return responses[Math.floor(Math.random() * responses.length)];
    }

    function addMessage(text, type) {
      const msgDiv = document.createElement('div');
      msgDiv.className = `chatbot-message ${type}-message`;
      msgDiv.textContent = text;
      chatMessages.appendChild(msgDiv);
      chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    // Event listeners
    sendMessageBtn.addEventListener('click', sendMessage);
    userInput.addEventListener('keypress', (e) => {
      if (e.key === 'Enter') sendMessage();
    });
  </script>
</body>
</html>
<?php
session_start();

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?redirect=dashboard.php");
    exit();
}

// Database connection
$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "medconnect";

try {
    $conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
    if (!$conn) {
        throw new Exception("Connection failed: " . mysqli_connect_error());
    }
    
    // Handle appointment cancellation
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_appointment'])) {
        $appointment_id = $_POST['appointment_id'];
        $user_id = $_SESSION['user_id'];
        
        // Delete the appointment
        $stmt = $conn->prepare("DELETE FROM appointments WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $appointment_id, $user_id);
        
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Appointment cancelled successfully!";
            header("Location: dashboard.php");
            exit();
        } else {
            $_SESSION['error_message'] = "Failed to cancel appointment.";
        }
    }
    
    // Get user data
    $user_id = $_SESSION['user_id'];
    $user_query = mysqli_query($conn, "SELECT name, email FROM users WHERE id = $user_id");
    $user_data = mysqli_fetch_assoc($user_query);
    
    // Get appointments
    $appointments = mysqli_query($conn, 
        "SELECT * FROM appointments 
         WHERE user_id = $user_id 
         ORDER BY appointment_date DESC 
         LIMIT 5");

} catch (Exception $e) {
    die("We're experiencing technical difficulties. Please try again later.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard | MedConnect</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <style>
    /* Base Styles (same as book.php) */
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
    }
    
    /* Header (same as book.php) */
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
      transition: width 0.3s;
    }
    
    .nav-links a:hover::after {
      width: 100%;
    }
    
    /* Dashboard Specific Styles */
    .dashboard {
      padding: 8rem 5% 3rem;
      max-width: 1200px;
      margin: 0 auto;
    }
    
    .welcome-card {
      background: white;
      border-radius: 20px;
      padding: 2rem;
      box-shadow: 0 10px 30px rgba(0,0,0,0.05);
      margin-bottom: 2rem;
      animation: fadeIn 0.6s ease-out;
    }
    
    .welcome-card h1 {
      font-size: 2.2rem;
      margin-bottom: 0.5rem;
    }
    
    .welcome-card h1 span {
      color: var(--primary);
    }
    
    .welcome-card p {
      color: var(--gray);
    }
    
    .dashboard-section {
      margin-bottom: 3rem;
    }
    
    .section-title {
      font-size: 1.5rem;
      margin-bottom: 1.5rem;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }
    
    .section-title i {
      color: var(--primary);
    }
    
    .appointments-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
      gap: 1.5rem;
    }
    
    .appointment-card {
      background: white;
      border-radius: 15px;
      padding: 1.5rem;
      box-shadow: 0 5px 15px rgba(0,0,0,0.05);
      transition: all 0.3s;
    }
    
    .appointment-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }
    
    .appointment-card h3 {
      color: var(--primary);
      margin-bottom: 0.5rem;
    }
    
    .appointment-card p {
      color: var(--gray);
      margin-bottom: 0.5rem;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }
    
    .appointment-card p i {
      color: var(--primary);
      width: 20px;
    }
    
    .no-appointments {
      background: white;
      border-radius: 15px;
      padding: 2rem;
      text-align: center;
      box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }
    
    .quick-actions {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
      gap: 1.5rem;
    }
    
    .action-card {
      background: white;
      border-radius: 15px;
      padding: 1.5rem;
      text-align: center;
      box-shadow: 0 5px 15px rgba(0,0,0,0.05);
      transition: all 0.3s;
      cursor: pointer;
    }
    
    .action-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }
    
    .action-card i {
      font-size: 2rem;
      color: var(--primary);
      margin-bottom: 1rem;
    }
    
    .action-card h3 {
      color: var(--dark);
      margin-bottom: 0.5rem;
    }
    
    /* Buttons (same as book.php) */
    .btn {
      padding: 0.8rem 1.8rem;
      border-radius: 50px;
      font-weight: 600;
      text-decoration: none;
      transition: all 0.3s;
      display: inline-block;
      cursor: pointer;
      border: none;
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
    
    /* Animations */
    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
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
      .dashboard {
        padding: 7rem 1.5rem 2rem;
      }
      
      .welcome-card {
        padding: 1.5rem;
      }
    }
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
    /* Typing indicator animation */
.typing span {
  display: inline-block;
  width: 8px;
  height: 8px;
  background: #6c63ff;
  border-radius: 50%;
  margin: 0 2px;
  opacity: 0.4;
  animation: typingAnimation 1s infinite ease-in-out;
}

.typing span:nth-child(2) {
  animation-delay: 0.2s;
}

.typing span:nth-child(3) {
  animation-delay: 0.4s;
}

@keyframes typingAnimation {
  0%, 100% { opacity: 0.4; transform: translateY(0); }
  50% { opacity: 1; transform: translateY(-3px); }
}
.confirmation-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        z-index: 2000;
        justify-content: center;
        align-items: center;
    }
    
    .modal-content {
        background: white;
        padding: 2rem;
        border-radius: 15px;
        max-width: 500px;
        width: 90%;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    }
    
    .modal-actions {
        display: flex;
        justify-content: flex-end;
        gap: 1rem;
        margin-top: 1.5rem;
    }
    
    /* Add status colors */
    .status-scheduled { color: var(--primary); }
    .status-completed { color: var(--success); }
    .status-cancelled { color: var(--danger); }
  </style>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
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
      <a href="dashboard.php" class="btn btn-outline">
        <i class="fas fa-user-circle"></i> My Account
      </a>
      <a href="logout.php" class="btn btn-primary">
        <i class="fas fa-sign-out-alt"></i> Logout
      </a>
    </div>
  </header>

  <!-- Dashboard Content -->

  <main class="dashboard">
    <!-- Welcome Card -->
    <div class="welcome-card">
      <h1>Welcome back, <span><?php echo htmlspecialchars($user_data['name']); ?></span></h1>
      <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success" style="color: var(--success); margin-top: 1rem;">
          <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
        </div>
      <?php endif; ?>
      <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger" style="color: var(--danger); margin-top: 1rem;">
          <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
        </div>
      <?php endif; ?>
      <p>Here's what's happening with your health journey today.</p>
    </div>

    <!-- Upcoming Appointments -->
    <div class="dashboard-section">
      <h2 class="section-title">
        <i class="fas fa-calendar-alt"></i> Upcoming Appointments
      </h2>
      
      <?php if(mysqli_num_rows($appointments) > 0): ?>
        <div class="appointments-grid">
          <?php while($apt = mysqli_fetch_assoc($appointments)): ?>
            <div class="appointment-card">
              <h3><?php echo htmlspecialchars($apt['service_type'] ?? 'General Checkup'); ?></h3>
              <p><i class="fas fa-calendar-day"></i> <?php echo date('F j, Y', strtotime($apt['appointment_date'])); ?></p>
              <p><i class="fas fa-clock"></i> <?php echo date('g:i A', strtotime($apt['appointment_time'])); ?></p>
              <p><i class="fas fa-sticky-note"></i> <?php echo !empty($apt['notes']) ? htmlspecialchars($apt['notes']) : 'No special notes'; ?></p>
              <p>
                <i class="fas fa-info-circle"></i> 
                Status: <span class="status-<?php echo htmlspecialchars($apt['status'] ?? 'scheduled'); ?>">
                  <?php echo ucfirst(htmlspecialchars($apt['status'] ?? 'scheduled')); ?>
                </span>
              </p>
              
              <button 
                type="button" 
                class="btn btn-danger cancel-btn" 
                style="width: 100%; margin-top: 1rem;"
                data-appointment-id="<?php echo $apt['id']; ?>"
              >
                <i class="fas fa-times"></i> Cancel Appointment
              </button>
            </div>
          <?php endwhile; ?>
        </div>
      <?php else: ?>
        <div class="no-appointments">
          <p>You don't have any upcoming appointments.</p>
          <a href="index.php" class="btn btn-primary">Book Your First Appointment</a>
        </div>
      <?php endif; ?>
    </div>

    <!-- Quick Actions -->
    <div class="dashboard-section">
      <h2 class="section-title">
        <i class="fas fa-bolt"></i> Quick Actions
      </h2>
      <div class="quick-actions">
        <div class="action-card" onclick="window.location.href='index.php'">
          <i class="fas fa-calendar-plus"></i>
          <h3>New Booking</h3>
          <p>Schedule a new appointment</p>
        </div>
        <div class="action-card" onclick="window.location.href='doctors.php'">
          <i class="fas fa-user-md"></i>
          <h3>Our Doctors</h3>
          <p>Meet our specialists</p>
        </div>
        <div class="action-card" onclick="window.location.href='profile.php'">
          <i class="fas fa-user-cog"></i>
          <h3>Profile</h3>
          <p>Update your information</p>
        </div>
        
      </div>
    </div>
    <!--cancellation boc-->
    <div class="confirmation-modal" id="confirmationModal">
      <div class="modal-content">
        <h3>Confirm Cancellation</h3>
        <p>Are you sure you want to cancel this appointment? This action cannot be undone.</p>
        <form method="POST" id="cancelForm">
          <input type="hidden" name="appointment_id" id="modalAppointmentId">
          <input type="hidden" name="cancel_appointment" value="1">
          <div class="modal-actions">
            <button type="button" class="btn btn-outline" id="cancelCancelBtn">
              No, Keep It
            </button>
            <button type="submit" class="btn btn-danger">
              Yes, Cancel Appointment
            </button>
          </div>
        </form>
      </div>
    </div>
  </main>

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
    // Cancellation modal handling
    document.addEventListener('DOMContentLoaded', function() {
      const cancelButtons = document.querySelectorAll('.cancel-btn');
      const modal = document.getElementById('confirmationModal');
      const cancelCancelBtn = document.getElementById('cancelCancelBtn');
      const modalAppointmentId = document.getElementById('modalAppointmentId');
      
      cancelButtons.forEach(btn => {
        btn.addEventListener('click', () => {
          modalAppointmentId.value = btn.dataset.appointmentId;
          modal.style.display = 'flex';
        });
      });
      
      cancelCancelBtn.addEventListener('click', () => {
        modal.style.display = 'none';
      });
      
      // Close modal when clicking outside
      modal.addEventListener('click', (e) => {
        if (e.target === modal) {
          modal.style.display = 'none';
        }
      });
    });
document.addEventListener('DOMContentLoaded', function() {
  // Chatbot elements
  const chatbotBtn = document.getElementById('chatbot-toggler');
  const chatbotBox = document.getElementById('chatbot-box');
  const closeBtn = document.getElementById('close-chatbot');
  const sendBtn = document.getElementById('send-message');
  const userInput = document.getElementById('user-input');
  const chatMessages = document.getElementById('chatbot-messages');

  // Open/close chatbot
  chatbotBtn.addEventListener('click', () => chatbotBox.classList.add('active'));
  closeBtn.addEventListener('click', () => chatbotBox.classList.remove('active'));

  // Send message on button click or Enter key
  sendBtn.addEventListener('click', sendMessage);
  userInput.addEventListener('keypress', (e) => e.key === 'Enter' && sendMessage());

  // Enhanced bot responses
  const botResponses = {
    greetings: ["Hello! 👋", "Hi there!", "Welcome to MedConnect! How can I help?"],
    appointment: "You can book appointments by visiting our booking page or telling me your preferred date and time.",
    doctors: "We have specialists in:\n- Cardiology ❤️\n- Dermatology 🧴\n- Pediatrics 👶\nWhich department do you need?",
    emergency: "🚨 For emergencies, please call 911 or visit the nearest hospital immediately!",
    hours: "⌚ Our clinic hours:\nMon-Fri: 8AM-6PM\nSat: 9AM-2PM\nSun: Closed",
    contact: "📞 Call us at: (123) 456-7890\n✉️ Email: help@medconnect.com\n📍 Location: 123 Health St, Medical City",
    default: "I'm a medical assistant bot. I can help with:\n1. Booking appointments\n2. Doctor information\n3. Clinic hours\n4. Emergency contacts\nHow may I assist you?"
  };

  // Initial greeting
  setTimeout(() => addBotMessage(botResponses.greetings[0]), 500);

  function sendMessage() {
    const message = userInput.value.trim();
    if (!message) return;

    addUserMessage(message);
    userInput.value = '';
    showTypingIndicator();
    
    setTimeout(() => {
      removeTypingIndicator();
      const botReply = generateBotResponse(message);
      addBotMessage(botReply);
    }, 1000 + Math.random() * 1500); // Random delay for natural feel
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
    if (/(emergency|urgent)/.test(userMessage)) {
      return botResponses.emergency;
    }
    if (/(time|hour|when|open)/.test(userMessage)) {
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
    const msgDiv = document.createElement('div');
    msgDiv.className = `chatbot-message ${type}-message`;
    msgDiv.innerHTML = text.replace(/\n/g, '<br>'); // Preserve line breaks
    chatMessages.appendChild(msgDiv);
    chatMessages.scrollTop = chatMessages.scrollHeight;
  }

  function showTypingIndicator() {
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
</body>
</html>
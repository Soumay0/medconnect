<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us | MedConnect</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #6c63ff;
            --secondary: #4d44db;
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
            line-height: 1.6;
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
        
        .nav-links {
            display: flex;
            gap: 2rem;
        }
        
        .nav-links a {
            text-decoration: none;
            color: var(--dark);
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .nav-links a:hover {
            color: var(--primary);
        }
        
        /* Main Content */
        .container {
            max-width: 1200px;
            margin: 3rem auto;
            padding: 0 2rem;
        }
        
        .page-header {
            text-align: center;
            margin-bottom: 3rem;
        }
        
        .page-header h1 {
            font-size: 2.5rem;
            color: var(--primary);
            margin-bottom: 1rem;
        }
        
        .page-header p {
            color: var(--gray);
            max-width: 700px;
            margin: 0 auto;
        }
        
        /* Mission Section */
        .mission-section {
            background: white;
            border-radius: 10px;
            padding: 2rem;
            margin-bottom: 3rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }
        
        .mission-section h2 {
            color: var(--primary);
            margin-bottom: 1rem;
            text-align: center;
        }
        
        .mission-content {
            display: flex;
            gap: 2rem;
            align-items: center;
        }
        
        .mission-text {
            flex: 1;
        }
        
        .mission-image {
            flex: 1;
            text-align: center;
        }
        
        .mission-image img {
            max-width: 100%;
            border-radius: 10px;
        }
        
        /* Team Section */
        .team-section {
            margin-bottom: 3rem;
        }
        
        .team-section h2 {
            color: var(--primary);
            margin-bottom: 2rem;
            text-align: center;
        }
        
        .team-members {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }
        
        .team-member {
            background: white;
            border-radius: 10px;
            padding: 2rem;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            transition: transform 0.3s ease;
        }
        
        .team-member:hover {
            transform: translateY(-10px);
        }
        
        .member-image {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            margin: 0 auto 1rem;
            border: 5px solid var(--light);
        }
        
        .member-name {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--primary);
        }
        
        .member-role {
            color: var(--gray);
            margin-bottom: 1rem;
            font-size: 0.9rem;
        }
        
        .member-contact {
            margin-top: 1rem;
        }
        
        .member-contact a {
            color: var(--primary);
            margin: 0 0.5rem;
            font-size: 1.2rem;
        }
        
        /* Motto Section */
        .motto-section {
            background: var(--primary);
            color: white;
            padding: 3rem 2rem;
            border-radius: 10px;
            text-align: center;
        }
        
        .motto-section h2 {
            margin-bottom: 1rem;
        }
        
        .motto-text {
            font-size: 1.2rem;
            font-style: italic;
            max-width: 700px;
            margin: 0 auto;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .mission-content {
                flex-direction: column;
            }
            
            .nav-links {
                display: none;
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
        <nav class="nav-links">
            <a href="index.php">Home</a>
            <a href="services.php">Services</a>
            <a href="doctors.php">Doctors</a>
            <a href="about.php">About</a>
            <?php if(isset($_SESSION['user_id'])): ?>
                <a href="dashboard.php">Dashboard</a>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="signup.php">Sign Up</a>
            <?php endif; ?>
        </nav>
    </header>
    
    <!-- Main Content -->
    <div class="container">
        <div class="page-header">
            <h1>About MedConnect</h1>
            <p>Connecting patients with healthcare professionals through innovative technology and compassionate care.</p>
        </div>
        
        <!-- Mission Section -->
        <div class="mission-section">
            <h2>Our Mission</h2>
            <div class="mission-content">
                <div class="mission-text">
                    <p>At MedConnect, we are dedicated to revolutionizing healthcare accessibility by bridging the gap between patients and medical professionals through our innovative online platform.</p>
                    <p>Our mission is to provide a seamless, secure, and user-friendly experience that empowers patients to take control of their healthcare journey while enabling doctors to deliver exceptional care beyond the confines of traditional clinic settings.</p>
                    <p>We believe in leveraging technology to make quality healthcare more accessible, affordable, and efficient for everyone, regardless of their location or circumstances.</p>
                </div>
                <div class="mission-image">
                    <img src="https://images.unsplash.com/photo-1579684385127-1ef15d508118?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80" alt="Healthcare Mission">
                </div>
            </div>
        </div>
        
        <!-- Team Section -->
        <div class="team-section">
            <h2>Meet Our Team</h2>
            <div class="team-members">
                <!-- Team Member 1 -->
                <div class="team-member">
                    <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="Soumay Pandey" class="member-image">
                    <h3 class="member-name">Soumay Pandey</h3>
                    <p class="member-role">Co-Founder & CEO</p>
                    <p>Visionary leader with expertise in healthcare technology and business strategy.</p>
                    <div class="member-contact">
                        <a href="mailto:soumaypandey@medconnect.com"><i class="fas fa-envelope"></i></a>
                        <a href="#"><i class="fab fa-linkedin"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                    </div>
                </div>
                
                <!-- Team Member 2 -->
                <div class="team-member">
                    <img src="https://randomuser.me/api/portraits/women/44.jpg" alt="Priya Sharma" class="member-image">
                    <h3 class="member-name">Priya Sharma</h3>
                    <p class="member-role">Chief Medical Officer</p>
                    <p>Experienced physician dedicated to improving patient care through technology.</p>
                    <div class="member-contact">
                        <a href="mailto:priyasharma@medconnect.com"><i class="fas fa-envelope"></i></a>
                        <a href="#"><i class="fab fa-linkedin"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                    </div>
                </div>
                
                <!-- Team Member 3 -->
                <div class="team-member">
                    <img src="https://randomuser.me/api/portraits/men/75.jpg" alt="Rahul Verma" class="member-image">
                    <h3 class="member-name">Rahul Verma</h3>
                    <p class="member-role">CTO</p>
                    <p>Technology expert focused on building secure and scalable healthcare solutions.</p>
                    <div class="member-contact">
                        <a href="mailto:rahulverma@medconnect.com"><i class="fas fa-envelope"></i></a>
                        <a href="#"><i class="fab fa-linkedin"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Motto Section -->
        <div class="motto-section">
            <h2>Our Motto</h2>
            <p class="motto-text">"Healthcare at your fingertips - Anytime, Anywhere"</p>
        </div>
    </div>
    
    <!-- Footer -->
    <footer style="background: var(--dark); color: white; text-align: center; padding: 2rem; margin-top: 3rem;">
        <p>&copy; <?= date('Y') ?> MedConnect. All rights reserved.</p>
        <div style="margin-top: 1rem;">
            <a href="#" style="color: white; margin: 0 0.5rem;"><i class="fab fa-facebook"></i></a>
            <a href="#" style="color: white; margin: 0 0.5rem;"><i class="fab fa-twitter"></i></a>
            <a href="#" style="color: white; margin: 0 0.5rem;"><i class="fab fa-instagram"></i></a>
            <a href="#" style="color: white; margin: 0 0.5rem;"><i class="fab fa-linkedin"></i></a>
        </div>
    </footer>
</body>
</html>
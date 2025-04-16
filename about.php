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
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
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
            width: 120px;
            height: 120px;
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
            
            .team-members {
                grid-template-columns: 1fr;
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
            <p>Connecting patients with healthcare professionals through innovative design and user-centered solutions.</p>
        </div>
        
        <!-- Mission Section -->
        <div class="mission-section">
            <h2>Our Design Philosophy</h2>
            <div class="mission-content">
                <div class="mission-text">
                    <p>At MedConnect, we believe that great healthcare experiences begin with thoughtful design. Our team of designers has crafted this platform with a focus on accessibility, usability, and aesthetic excellence.</p>
                    <p>We've approached every aspect of MedConnect with the user in mind - from the intuitive interface that makes navigation effortless, to the carefully chosen color palette that creates a calming environment for patients and healthcare providers alike.</p>
                    <p>Our design process is rooted in research, iteration, and testing to ensure we deliver a product that truly meets the needs of all users while maintaining the highest standards of visual appeal.</p>
                </div>
                <div class="mission-image">
                    <img src="https://images.unsplash.com/photo-1551288049-bebda4e38f71?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80" alt="Design Team Working">
                </div>
            </div>
        </div>
        
        <!-- Team Section -->
        <div class="team-section">
            <h2>Our Design Team</h2>
            <div class="team-members">
                <!-- Team Member 1 -->
                <div class="team-member">
                    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS1F8A6CFepGMz1hidqHr2avMIbSLvXhXfLZQ&s" alt="Soumay Pandey" class="member-image">
                    <h3 class="member-name">Soumay Pandey</h3>
                    <p class="member-role">Lead Full Stack Web Developer</p>
                    <p>User research and interaction design, ensuring seamless user experiences.</p>
                    <div class="member-contact">
                        <a href="soumaypandey1@gmail.com"><i class="fas fa-envelope"></i></a>
                        <a href="https://www.linkedin.com/in/soumay-pandey-296907285/"><i class="fab fa-linkedin"></i></a>
                        <a href="https://github.com/Soumay0"><i class="fab fa-dribbble"></i></a>
                    </div>
                </div>
                
                <!-- Team Member 2 -->
                <div class="team-member">
                    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS1F8A6CFepGMz1hidqHr2avMIbSLvXhXfLZQ&s" alt="Sukesh Muddangula" class="member-image">
                    <h3 class="member-name">Sukesh Muddangula</h3>
                    <p class="member-role">Frontend Developer</p>
                    <p>Brings designs to life with clean, efficient code and responsive implementations.</p>
                    <div class="member-contact">
                        <a href="m.sukesh6789@gmail.com"><i class="fas fa-envelope"></i></a>
                        <a href="https://www.linkedin.com/in/sukesh30?utm_source=share&utm_campaign=share_via&utm_content=profile&utm_medium=android_app "><i class="fab fa-linkedin"></i></a>
                       
                    </div>
                </div>
                
                <!-- Team Member 3 -->
                <div class="team-member">
                    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS1F8A6CFepGMz1hidqHr2avMIbSLvXhXfLZQ&s" alt="Sodhari Sanjeev" class="member-image">
                    <h3 class="member-name">Sodhari Sanjeev</h3>
                    <p class="member-role">Frontend Developer</p>
                    <p>Creates stunning visual elements and maintains brand consistency across platforms.</p>
                    <div class="member-contact">
                        <a href="sodharisanju1914@gmail.com"><i class="fas fa-envelope"></i></a>
                        <a href="https://linkedin.com/comm/mynetwork/discovery-see-all?usecase=PEOPLE_FOLLOWS&followMember=sanjeev-sodhari-6a385020b"><i class="fab fa-linkedin"></i></a>
                       
                    </div>
                </div>
                
                <!-- Team Member 4 -->
                <div class="team-member">
                    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS1F8A6CFepGMz1hidqHr2avMIbSLvXhXfLZQ&s" alt="Team Member 4" class="member-image">
                    <h3 class="member-name">Sudhanshu Kumar</h3>
                    <p class="member-role">Full Stack Web Developer</p>
                    <p>Conducts user testing and gathers insights to inform our design decisions.</p>
                    <div class="member-contact">
                        <a href="sk6264323@gmail.com"><i class="fas fa-envelope"></i></a>
                        <a href="https://www.linkedin.com/in/sudhanshukumar91?utm_source=share&utm_campaign=share_via&utm_content=profile&utm_medium=android_app"><i class="fab fa-linkedin"></i></a>
                       
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Design Approach Section -->
        <div class="mission-section">
            <h2>Our Design Approach</h2>
            <div class="mission-content">
                <div class="mission-image">
                    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS1F8A6CFepGMz1hidqHr2avMIbSLvXhXfLZQ&s" alt="Design Process">
                </div>
                <div class="mission-text">
                    <p><strong>Research:</strong> We begin by understanding the needs of both patients and healthcare providers through interviews and surveys.</p>
                    <p><strong>Wireframing:</strong> Low-fidelity prototypes help us test concepts before investing in full designs.</p>
                    <p><strong>Visual Design:</strong> We create high-fidelity mockups with careful attention to typography, color, and spacing.</p>
                    <p><strong>Implementation:</strong> Our frontend developers work closely with designers to ensure pixel-perfect execution.</p>
                    <p><strong>Testing:</strong> We continuously test our designs with real users to identify areas for improvement.</p>
                </div>
            </div>
        </div>
        
        <!-- Motto Section -->
        <div class="motto-section">
            <h2>Our Design Motto</h2>
            <p class="motto-text">"Beautiful, intuitive design that improves healthcare experiences"</p>
        </div>
    </div>
    
    <!-- Footer -->
    <footer style="background: var(--dark); color: white; text-align: center; padding: 2rem; margin-top: 3rem;">
        <p>&copy; <?= date('Y') ?> MedConnect. All rights reserved.</p>
        <div style="margin-top: 1rem;">
           
            <a href="https://www.linkedin.com/in/soumay-pandey-296907285/" style="color: white; margin: 0 0.5rem;"><i class="fab fa-linkedin"></i></a>
        </div>
            </footer>
</body>
</html>
<?php
session_start();
require_once 'db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch doctors from database
$doctors = [];
$sql = "SELECT * FROM doctors ORDER BY is_active DESC, name ASC"; // Order by status first (active doctors first)
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $doctors[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Doctors | MedConnect</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #6c63ff;
            --secondary: #4d44db;
            --accent: #ff6584;
            --light: #f8f9fa;
            --dark: #212529;
            --gray: #6c757d;
            --success: #28a745;
            --danger: #dc3545;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        
        body {
            background-color: #f5f7fa;
            color: var(--dark);
            line-height: 1.6;
        }
        
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
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem 1rem;
            padding-top: 6rem;
        }
        
        .page-title {
            text-align: center;
            margin-bottom: 2rem;
            color: var(--primary);
            font-size: 2.5rem;
            font-weight: 600;
        }
        
        .page-subtitle {
            text-align: center;
            color: var(--gray);
            margin-bottom: 3rem;
            font-size: 1.1rem;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .search-container {
            display: flex;
            justify-content: center;
            margin-bottom: 3rem;
        }
        
        .search-box {
            padding: 1rem 1.5rem;
            border: 2px solid #e9ecef;
            border-radius: 50px;
            width: 100%;
            max-width: 600px;
            font-size: 1rem;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }
        
        .search-box:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(108, 99, 255, 0.2);
            outline: none;
        }
        
        .availability-filter {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .availability-btn {
            padding: 0.8rem 2rem;
            border-radius: 50px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid #e9ecef;
            background: white;
            font-size: 0.9rem;
        }
        
        .availability-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .availability-btn.active {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
            box-shadow: 0 5px 15px rgba(108, 99, 255, 0.3);
        }
        
        .doctors-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }
        
        .doctor-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            position: relative;
            opacity: 0;
            transform: translateY(20px);
        }
        
        .doctor-card.active {
            opacity: 1;
            transform: translateY(0);
        }
        
        .doctor-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
        }
        
        .status-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            padding: 0.3rem 0.8rem;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            z-index: 2;
        }
        
        .status-active {
            background-color: var(--success);
            color: white;
        }
        
        .status-inactive {
            background-color: var(--danger);
            color: white;
        }
        
        .doctor-image-container {
            width: 100%;
            height: 250px;
            background: var(--light);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }
        
        .doctor-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .doctor-info {
            padding: 1.5rem;
            text-align: center;
        }
        
        .doctor-name {
            font-size: 1.4rem;
            margin-bottom: 0.5rem;
            color: var(--dark);
            font-weight: 600;
        }
        
        .doctor-specialty {
            color: var(--primary);
            font-weight: 600;
            margin-bottom: 1rem;
            display: block;
            font-size: 1rem;
        }
        
        .doctor-experience {
            color: var(--gray);
            font-size: 0.9rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        
        .doctor-bio {
            color: var(--gray);
            margin-bottom: 1.5rem;
            font-size: 0.95rem;
            line-height: 1.6;
        }
        
        .btn {
            display: inline-block;
            padding: 0.8rem 1.5rem;
            background-color: var(--primary);
            color: white;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            text-align: center;
            width: 100%;
            font-size: 1rem;
        }
        
        .btn:hover {
            background-color: var(--secondary);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(108, 99, 255, 0.3);
        }
        
        .no-doctors {
            grid-column: 1 / -1;
            text-align: center;
            padding: 3rem;
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .no-doctors h3 {
            color: var(--dark);
            margin-bottom: 1rem;
        }
        
        .no-doctors p {
            color: var(--gray);
            margin-bottom: 1.5rem;
        }
        
        @media (max-width: 768px) {
            .doctors-grid {
                grid-template-columns: 1fr;
            }
            
            .nav-links {
                display: none;
            }
            
            .page-title {
                font-size: 2rem;
            }
            
            .availability-filter {
                flex-direction: column;
                align-items: center;
            }
            
            .availability-btn {
                width: 100%;
                max-width: 250px;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">Med<span>Connect</span></div>
        <nav class="nav-links">
            <a href="index.php">Home</a>
            <a href="services.php">Services</a>
            <a href="doctors.php" class="active">Doctors</a>
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
    
    <div class="container">
        <h1 class="page-title">Meet Our Specialist Doctors</h1>
        <p class="page-subtitle">Book appointments with top specialists in minutes. Our AI assistant will guide you to the perfect doctor for your needs.</p>
        
        <div class="search-container">
            <input type="text" id="searchInput" class="search-box" placeholder="Search doctors by name..." oninput="filterDoctors()">
        </div>
        
        <div class="availability-filter">
            <button class="availability-btn active" onclick="filterByAvailability('all')">All Doctors</button>
            <button class="availability-btn" onclick="filterByAvailability('active')">Available Now</button>
        </div>
        
        <div class="doctors-grid" id="doctorsGrid">
            <?php if (count($doctors) > 0): ?>
                <?php foreach ($doctors as $doctor): ?>
                    <?php if ($doctor['is_active'] == 1): ?>
                        <div class="doctor-card" 
                            data-name="<?= htmlspecialchars(strtolower($doctor['name'])) ?>" 
                            data-status="active">
                            <div class="status-badge status-active">
                                Available
                            </div>
                            <div class="doctor-image-container">
                                <img src="data:image/png;base64,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" 
                                     alt="Doctor" class="doctor-image">
                            </div>
                            <div class="doctor-info">
                                <h3 class="doctor-name">Dr. <?= htmlspecialchars($doctor['name']) ?></h3>
                                <span class="doctor-specialty"><?= htmlspecialchars($doctor['specialty']) ?></span>
                                <div class="doctor-experience">
                                    <i class="fas fa-briefcase"></i>
                                    <span><?= htmlspecialchars($doctor['experience'] ?? '5') ?> years experience</span>
                                </div>
                                <p class="doctor-bio"><?= htmlspecialchars(substr($doctor['bio'] ?? 'Specialized in ' . $doctor['specialty'], 0, 100)) ?>...</p>
                                <a href="index.php?doctor=<?= $doctor['id'] ?>" class="btn">
                                    Book Appointment
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-doctors">
                    <h3>No Doctors Found</h3>
                    <p>We currently don't have any doctors available. Please check back later.</p>
                    <a href="index.php" class="btn">Return Home</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Filter doctors based on search input and availability
        function filterDoctors() {
            const searchInput = document.getElementById('searchInput').value.toLowerCase();
            const doctorCards = document.querySelectorAll('.doctor-card');
            
            doctorCards.forEach(card => {
                const name = card.dataset.name;
                const matchesSearch = name.includes(searchInput);
                
                if (matchesSearch) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }
        
        // Filter by availability status
        function filterByAvailability(status) {
            const doctorCards = document.querySelectorAll('.doctor-card');
            const availabilityBtns = document.querySelectorAll('.availability-btn');
            
            // Update active button
            availabilityBtns.forEach(btn => {
                if (btn.textContent.toLowerCase().includes(status)) {
                    btn.classList.add('active');
                } else {
                    btn.classList.remove('active');
                }
            });
            
            // Filter cards
            doctorCards.forEach(card => {
                const cardStatus = card.dataset.status;
                
                if (status === 'all' || cardStatus === status) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }
        
        // Animation for cards
        document.addEventListener('DOMContentLoaded', () => {
            const cards = document.querySelectorAll('.doctor-card');
            cards.forEach((card, index) => {
                setTimeout(() => {
                    card.classList.add('active');
                }, index * 100);
            });
        });
    </script>
</body>
</html>
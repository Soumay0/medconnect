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
$sql = "SELECT * FROM doctors ORDER BY name ASC";
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
    <style>
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
        }
        
        .search-filter {
            display: flex;
            justify-content: center;
            margin-bottom: 2rem;
            gap: 1rem;
            flex-wrap: wrap;
        }
        
        .search-box {
            padding: 0.8rem 1.5rem;
            border: 2px solid #e9ecef;
            border-radius: 50px;
            width: 100%;
            max-width: 500px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .search-box:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(108, 99, 255, 0.2);
            outline: none;
        }
        
        .filter-btn {
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 50px;
            background-color: #e9ecef;
            color: var(--dark);
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 500;
        }
        
        .filter-btn:hover, .filter-btn.active {
            background-color: var(--primary);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(108, 99, 255, 0.3);
        }
        
        .doctors-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }
        
        .doctor-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            position: relative;
        }
        
        .doctor-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
        }
        
        .doctor-image-container {
            width: 100%;
            height: 250px;
            background: var(--light);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .doctor-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .doctor-info {
            padding: 1.5rem;
        }
        
        .doctor-name {
            font-size: 1.3rem;
            margin-bottom: 0.5rem;
            color: var(--dark);
        }
        
        .doctor-specialty {
            color: var(--primary);
            font-weight: 600;
            margin-bottom: 0.5rem;
            display: block;
        }
        
        .doctor-age {
            color: var(--gray);
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }
        
        .doctor-bio {
            color: var(--gray);
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
        }
        
        .btn {
            display: inline-block;
            padding: 0.6rem 1.5rem;
            background-color: var(--primary);
            color: white;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            text-align: center;
        }
        
        .btn:hover {
            background-color: var(--secondary);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(108, 99, 255, 0.3);
        }
        
        .no-doctors {
            grid-column: 1 / -1;
            text-align: center;
            padding: 2rem;
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        @media (max-width: 768px) {
            .doctors-grid {
                grid-template-columns: 1fr;
            }
            
            .nav-links {
                display: none;
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
        
        <div class="search-filter">
            <input type="text" id="searchInput" class="search-box" placeholder="Search doctors..." oninput="filterDoctors()">
            <select id="specialtyFilter" class="filter-btn" onchange="filterDoctors()">
                <option value="all">All Specialties</option>
                <option value="Cardiology">Cardiology</option>
                <option value="Dermatology">Dermatology</option>
                <option value="Pediatrics">Pediatrics</option>
                <option value="Neurology">Neurology</option>
            </select>
        </div>
        
        <div class="doctors-grid" id="doctorsGrid">
            <?php if (count($doctors) > 0): ?>
                <?php foreach ($doctors as $doctor): ?>
                <div class="doctor-card" 
                     data-name="<?= htmlspecialchars(strtolower($doctor['name'])) ?>" 
                     data-specialty="<?= htmlspecialchars(strtolower($doctor['specialty'])) ?>">
                    <div class="doctor-image-container">
                        <img src="https://cdn-icons-png.flaticon.com/512/3304/3304567.png" 
                             alt="Doctor" class="doctor-image">
                    </div>
                    <div class="doctor-info">
                        <h3 class="doctor-name">Dr. <?= htmlspecialchars($doctor['name']) ?></h3>
                        <span class="doctor-specialty"><?= htmlspecialchars($doctor['specialty']) ?></span>
                        <span class="doctor-age">Age: <?= htmlspecialchars($doctor['age']) ?></span>
                        <p class="doctor-bio"><?= htmlspecialchars(substr($doctor['bio'] ?? 'Specialized in ' . $doctor['specialty'], 0, 100)) ?>...</p>
                        <a href="index.php?doctor=<?= $doctor['id'] ?>" class="btn">Book Appointment</a>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-doctors">
                    <p>No doctors available at the moment. Please check back later.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Filter doctors based on search input and specialty
        function filterDoctors() {
            const searchInput = document.getElementById('searchInput').value.toLowerCase();
            const specialtyFilter = document.getElementById('specialtyFilter').value.toLowerCase();
            const doctorCards = document.querySelectorAll('.doctor-card');
            
            doctorCards.forEach(card => {
                const name = card.dataset.name;
                const specialty = card.dataset.specialty;
                
                const matchesSearch = name.includes(searchInput);
                const matchesSpecialty = specialtyFilter === 'all' || specialty.includes(specialtyFilter);
                
                if (matchesSearch && matchesSpecialty) {
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
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });
        });
    </script>
</body>
</html>
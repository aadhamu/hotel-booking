<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>
<style>
    /* General Reset */
{
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

/* Body Background */
body {
    background: #f8f8f8;
    color: #333;
    line-height: 1.6;
}

/* Navbar */
.navbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: rgba(0, 0, 0, 0.7);
    color: #fff;
    padding: 20px 50px;
    position: fixed;
    width: 100%;
    z-index: 999;
}

.logo {
    font-size: 1.8rem;
    font-weight: bold;
    letter-spacing: 1px;
}

.nav-links {
    list-style: none;
    display: flex;
    gap: 25px;
}

.nav-links li a {
    color: #fff;
    text-decoration: none;
    transition: color 0.3s ease;
}

.nav-links li a:hover {
    color: #f9c74f;
}

.btn {
    padding: 10px 20px;
    background: #f9c74f;
    color: #000;
    border: none;
    border-radius: 30px;
    cursor: pointer;
    font-weight: bold;
    transition: background 0.3s ease;
    text-decoration: none;
}

.btn:hover {
    background: #f9844a;
    color: #fff;
}

/* Hero Section */
.hero {
    height: 100vh;
    background: url('https://images.unsplash.com/photo-1559599238-dca5eae0f5d1?auto=format&fit=crop&w=1950&q=80') no-repeat center center/cover;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    text-align: center;
    color: white;
    padding-top: 100px;
    position: relative;
}

.hero::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    height: 100%;
    width: 100%;
    background: rgba(0, 0, 0, 0.6);
    z-index: 1;
}

.hero-content {
    z-index: 2;
    max-width: 700px;
    animation: fadeInUp 1.5s ease-out;
}

.hero-content h2 {
    font-size: 3rem;
    margin-bottom: 20px;
}

.hero-content p {
    font-size: 1.2rem;
    margin-bottom: 30px;
}

.hero-btn {
    font-size: 1rem;
}

/* Animation */
@keyframes fadeInUp {
    0% {
        opacity: 0;
        transform: translateY(40px);
    }
    100% {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Responsive */
@media screen and (max-width: 768px) {
    .navbar {
        flex-direction: column;
    }

    .nav-links {
        flex-direction: column;
        gap: 10px;
        margin-top: 10px;
    }

    .hero-content h2 {
        font-size: 2rem;
    }

    .hero-content p {
        font-size: 1rem;
    }
}






</style>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Hotel Booking Landing Page</title> 
    <link rel="stylesheet" href="">
</head>
<body>
    <header class="hero"> 
        <nav class="navbar">
            <h1 class="logo">Ethad hotel</h1>
            <ul class="nav-links">
                <li><a href="#about">About</a></li>   
                <li><a href="explore_rooms.php">Rooms</a></li>
                <li><a href="#contact">Contact</a></li>
                <li><a href="user_dashboard.php" class="btn">My Dashboard</a></li> <!-- 👈 Link to dashboard -->
            </ul>
        </nav>

        <div class="hero-content">
            <h2>Welcome to Ethad hotel</h2>
            <p>Your comfort is our priority. Explore our premium rooms and services.</p>
            <a href="explore_rooms.php">
    <button style="padding: 15px 30px; background: #f9c74f; border: none; border-radius: 30px; font-weight: bold; cursor: pointer;">
        Explore Rooms
    </button>
</a>
        </div>
    </header>

     
</body>
</html>

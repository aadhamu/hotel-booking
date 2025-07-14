<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Etihad Hotel</title>
  <style>
    /* General Styles */
    :root {
      --primary-color: #2d3436;
      --secondary-color: #0984e3;
      --accent-color: #00cec9;
      --light-color: #f5f6fa;
      --dark-color: #2d3436;
      --text-color: #636e72;
      --white: #ffffff;
      --shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
      --transition: all 0.3s ease;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      line-height: 1.6;
      color: var(--text-color);
      background-color: var(--light-color);
    }

    a {
      text-decoration: none;
      color: inherit;
    }

    ul {
      list-style: none;
    }

    .section_container {
      max-width: 1200px;
      margin: 0 auto;
      padding: 0 20px;
    }

    /* Button Styles */
    .btn {
      display: inline-block;
      padding: 10px 20px;
      background-color: var(--secondary-color);
      color: var(--white);
      border: none;
      border-radius: 5px;
      cursor: pointer;
      transition: var(--transition);
      font-weight: 500;
    }

    .btn:hover {
      background-color: #0767b1;
      transform: translateY(-2px);
    }

    /* Navigation */
    nav {
      background-color: var(--white);
      box-shadow: var(--shadow);
      position: fixed;
      width: 100%;
      top: 0;
      z-index: 100;
    }

    .nav_logo {
      font-size: 1.8rem;
      font-weight: 700;
      color: var(--secondary-color);
    }

    .nav-links {
      display: flex;
      align-items: center;
      gap: 30px;
    }

    .nav-links li a {
      font-weight: 500;
      transition: var(--transition);
    }

    .nav-links li a:hover {
      color: var(--secondary-color);
    }

    nav {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 20px 40px;
    }

    /* Header */
    .header_container {
      margin-top: 80px;
    }

    .header_image_container {
      position: relative;
      height: 500px;
      background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), 
                  url('img/image.jpg') center/cover no-repeat;
      border-radius: 10px;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: flex-start;
      padding: 0 60px;
      color: var(--white);
    }

    .header_content {
      max-width: 600px;
    }

    .header_content h1 {
      font-size: 2.5rem;
      margin-bottom: 20px;
      line-height: 1.2;
    }

    .header_content p {
      font-size: 1.2rem;
      margin-bottom: 30px;
    }

    /* Popular Rooms */
    .popular_container {
      padding: 80px 0;
    }

    .section_header {
      text-align: center;
      margin-bottom: 50px;
      font-size: 2rem;
      color: var(--primary-color);
      position: relative;
    }

    .section_header::after {
      content: '';
      display: block;
      width: 80px;
      height: 4px;
      background-color: var(--accent-color);
      margin: 15px auto 0;
    }

    .popular_grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
      gap: 30px;
    }

    .popular_card {
      background-color: var(--white);
      border-radius: 10px;
      overflow: hidden;
      box-shadow: var(--shadow);
      transition: var(--transition);
    }

    .popular_card:hover {
      transform: translateY(-10px);
    }

    .popular_card img {
      width: 100%;
      height: 200px;
      object-fit: cover;
    }

    .popular_content {
      padding: 20px;
    }

    .popular_card_header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 10px;
    }

    .popular_card_header h4 {
      font-size: 1.2rem;
      color: var(--primary-color);
    }

    /* Footer */
    .footer {
      background-color: var(--primary-color);
      color: var(--white);
      padding: 60px 0 20px;
    }

    .footer_container {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 40px;
    }

    .footer_col h3 {
      font-size: 1.5rem;
      margin-bottom: 20px;
      color: var(--white);
    }

    .footer_col h4 {
      font-size: 1.2rem;
      margin-bottom: 20px;
      color: var(--accent-color);
    }

    .footer_col p {
      margin-bottom: 10px;
      cursor: pointer;
      transition: var(--transition);
    }

    .footer_col p:hover {
      color: var(--accent-color);
    }

    .footer_col:last-child {
      grid-column: 1 / -1;
      text-align: center;
      padding-top: 20px;
      border-top: 1px solid rgba(255, 255, 255, 0.1);
      font-size: 0.9rem;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
      nav {
        padding: 15px 20px;
      }

      .nav_logo {
        font-size: 1.5rem;
      }

      .nav-links {
        gap: 15px;
      }

      .header_image_container {
        height: 400px;
        padding: 0 30px;
      }

      .header_content h1 {
        font-size: 2rem;
      }

      .popular_grid {
        grid-template-columns: 1fr;
      }
    }
  </style>
</head>
<body>
  <nav>
    <div class="nav_logo">Etihad Hotel</div>
    <ul class="nav-links">                   
      <li><a href="explore_rooms.php">Rooms</a></li>
      <li><a href="user_dashboard.php" class="btn">My Dashboard</a></li>
      <li><a href="logout.php">Logout</a></li>
    </ul>
  </nav>
  
  <header class="section_container header_container">
    <div class="header_image_container">
      <div class="header_content">
        <h1>Unwind in style — your comfort is our priority.</h1>
        <p>Affordable stays, unforgettable comfort.</p>
      </div>
    </div>
  </header>
  
  <section class="section_container popular_container">
    <h2 class="section_header">Rooms</h2>
    <div class="popular_grid">
      <div class="popular_card">
        <img src="img/pic for hotel.jpg" alt="Deluxe Room">
        <div class="popular_content">
          <div class="popular_card_header">
            <h4>Deluxe Room</h4>
          </div>
          <p>Spacious room with city view</p>
        </div>
      </div>
      
      <div class="popular_card">
        <img src="img/hotel pic2.jpg" alt="Executive Suite">
        <div class="popular_content">
          <div class="popular_card_header">
            <h4>Executive Suite</h4>
          </div>
          <p>Luxury suite with separate living area</p>
        </div>
      </div>
      
      <div class="popular_card">
        <img src="img/hotel pic3.jpg" alt="Presidential Suite">
        <div class="popular_content">
          <div class="popular_card_header">
            <h4>Presidential Suite</h4>
          </div>
          <p>Ultimate luxury experience</p>
        </div>
      </div>
    </div>
  </section>
 
  <footer class="footer">
    <div class="section_container footer_container">
      <div class="footer_col">
        <h3>Etihad Hotel</h3>
        <p>Etihad Hotels is a premium hotel booking website that offers a seamless and convenient way to find and book accommodation.</p>
        <p>With a user friendly interface and excellent customer service.</p>
      </div>
      
      <div class="footer_col">
        Copyright @ 2025 Web Design, All Rights Reserved.
      </div>
    </div>
  </footer>
</body>
</html>
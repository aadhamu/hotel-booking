<?php 
include 'config.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$user_id = $_SESSION['user_id'];

$query = $conn->prepare("SELECT bookings.id, bookings.ticket, rooms.room_name, bookings.check_in, bookings.check_out 
                         FROM bookings 
                         JOIN rooms ON bookings.room_id = rooms.id 
                         WHERE bookings.user_id = ?");
$query->bind_param("i", $user_id);
$query->execute();
$result = $query->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Tickets</title>
    <link rel="stylesheet" href="your_dashboard_styles.css">
    <style>
        .ticket-card {
            background: #fff;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            animation: fadeInUp 0.5s ease-in-out;
        }
        .back-btn {
            display: inline-block;
            background: #f9c74f;
            color: #000;
            padding: 10px 20px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: bold;
            margin-bottom: 20px;
        }
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="user_dashboard.php" class="back-btn">← Back to Dashboard</a>
        <h2>Your Tickets</h2>
        <?php while($row = $result->fetch_assoc()): ?>
            <div class="ticket-card">
                <p><strong>Ticket ID:</strong> <?= $row['ticket'] ?></p>
                <p><strong>Room:</strong> <?= $row['room_name'] ?></p>
                <p><strong>Check-in:</strong> <?= $row['check_in'] ?></p>
                <p><strong>Check-out:</strong> <?= $row['check_out'] ?></p>
            </div>
        <?php endwhile; ?>
    </div>
</body>
</html>

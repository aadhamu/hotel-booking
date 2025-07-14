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
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: #f0f6ff;
            padding: 50px 20px;
            color: #333;
        }

        .container {
            max-width: 900px;
            margin: auto;
        }

        h2 {
            text-align: center;
            color: #007bff;
            margin-bottom: 40px;
            font-size: 2rem;
        }

        .back-btn {
            display: inline-block;
            background: #007bff;
            color: #fff;
            padding: 10px 25px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: bold;
            margin-bottom: 30px;
            transition: background 0.3s ease;
        }

        .back-btn:hover {
            background: #0056b3;
        }

        .ticket-card {
            background: #ffffff;
            border-left: 6px solid #007bff;
            padding: 25px;
            margin-bottom: 25px;
            border-radius: 12px;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.08);
            animation: fadeInUp 0.6s ease-in-out;
            transition: transform 0.3s ease;
        }

        .ticket-card:hover {
            transform: translateY(-5px);
        }

        .ticket-card p {
            font-size: 1.05rem;
            margin-bottom: 10px;
        }

        .ticket-card strong {
            color: #333;
        }

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

        @media screen and (max-width: 768px) {
            .ticket-card {
                padding: 20px;
            }

            .back-btn {
                padding: 8px 18px;
                font-size: 0.9rem;
            }

            h2 {
                font-size: 1.6rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="user_dashboard.php" class="back-btn">← Back to Dashboard</a>
        <h2>🎫 Your Booking Tickets</h2>

        <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <div class="ticket-card">
                    <p><strong>🎟️ Ticket ID:</strong> <?= $row['ticket'] ?></p>
                    <p><strong>🏨 Room:</strong> <?= $row['room_name'] ?></p>
                    <p><strong>📅 Check-in:</strong> <?= date("F j, Y", strtotime($row['check_in'])) ?></p>
                    <p><strong>📅 Check-out:</strong> <?= date("F j, Y", strtotime($row['check_out'])) ?></p>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p style="text-align: center; font-size: 1.1rem;">You have no bookings yet. Book a room to see your tickets here.</p>
        <?php endif; ?>
    </div>
</body>
</html>

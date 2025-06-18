<?php
include 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$rooms = $conn->query("SELECT * FROM rooms WHERE id NOT IN (SELECT room_id FROM bookings)");

$booking_query = $conn->prepare("SELECT bookings.ticket, rooms.room_name, bookings.check_in, bookings.check_out 
                                 FROM bookings 
                                 JOIN rooms ON bookings.room_id = rooms.id 
                                 WHERE bookings.user_id = ? 
                                 ORDER BY bookings.id DESC");
$booking_query->bind_param("i", $user_id);
$booking_query->execute();
$booking_result = $booking_query->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Dashboard</title>
    <script src="https://checkout.flutterwave.com/v3.js"></script>
    <style>
/* Reset & Font */
* {
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
    padding-top: 100px; /* to prevent content hiding behind navbar */
}

/* Container */
.container {
    max-width: 1200px;
    margin: auto;
    padding: 20px;
}

/* Heading */
h2, h3 {
    text-align: center;
    margin-bottom: 20px;
    color: #333;
    animation: fadeInUp 1s ease-out;
}

/* Table Styling */
table {
    width: 100%;
    margin-bottom: 40px;
    border-collapse: collapse;
    background: #fff;
    box-shadow: 0 4px 10px rgba(0,0,0,0.05);
    border-radius: 10px;
    overflow: hidden;
    animation: fadeInUp 1.2s ease-out;
}

th, td {
    padding: 15px 20px;
    text-align: left;
    border-bottom: 1px solid #eee;
}

th {
    background-color: #000; /* dark like navbar */
    color: white;
    font-size: 1rem;
}

tr:hover {
    background-color: #f9f9f9;
}

tr:last-child td {
    border-bottom: none;
}

/* Inputs (Date pickers) */
input[type="date"] {
    padding: 8px 10px;
    border: 1px solid #ccc;
    border-radius: 8px;
    outline: none;
    transition: border-color 0.3s ease;
}

input[type="date"]:focus {
    border-color: #f9c74f;
}

/* Buttons */
button {
    padding: 10px 20px;
    background: #f9c74f;
    color: #000;
    border: none;
    border-radius: 30px;
    cursor: pointer;
    font-weight: bold;
    transition: background 0.3s ease, color 0.3s ease;
}

button:hover {
    background: #f9844a;
    color: #fff;
}

/* Back Button (Link style) */
.back-btn {
    display: inline-block;
    background: #f9c74f;
    color: #000;
    padding: 10px 20px;
    border-radius: 30px;
    text-decoration: none;
    font-weight: bold;
    margin-bottom: 20px;
    transition: background 0.3s ease, color 0.3s ease;
}

.back-btn:hover {
    background: #f9844a;
    color: #fff;
}

/* Small text */
small {
    color: #666;
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
    th, td {
        padding: 10px 12px;
    }

    button, .back-btn {
        padding: 8px 16px;
    }
}
</style>

</head>
<body>

<div class="container">
    <!-- Back Button -->
    <div style="text-align: left; margin-bottom: 20px;">
    <a href="index.php" class="back-btn">← Back to Home</a>

    </div>

    <h2>Available Rooms</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Room Name</th>
            <th>Price</th>
            <th>Check-in</th>
            <th>Check-out</th>
            <th>Action</th>
        </tr>
        <?php while ($room = $rooms->fetch_assoc()): ?>
        <tr>
            <td><?= $room['id'] ?></td>
            <td><?= $room['room_name'] ?></td>
            <td>₦<?= number_format($room['price'], 2) ?></td>
            <td><input type="date" id="check_in_<?= $room['id'] ?>" required></td>
            <td><input type="date" id="check_out_<?= $room['id'] ?>" required></td>
            <td>
                <button onclick="payWithFlutterwave<?= $room['id'] ?>()">Pay & Book</button>
            </td>
        </tr>

        <script>
        function payWithFlutterwave<?= $room['id'] ?>() {
            const checkIn = document.getElementById("check_in_<?= $room['id'] ?>").value;
            const checkOut = document.getElementById("check_out_<?= $room['id'] ?>").value;
            const userEmail = "user@example.com"; // Replace with PHP session email if available

            if (!checkIn || !checkOut) {
                alert("Please select check-in and check-out dates.");
                return;
            }

            FlutterwaveCheckout({
                public_key: "FLWPUBK_TEST-d295a228d1e11a71aa6e1908c1f595a8-X",
                tx_ref: "TXREF_" + Math.floor(Math.random() * 1000000000),
                amount: <?= $room['price'] ?>,
                currency: "NGN", // Currency already set to Naira
                payment_options: "card, mobilemoney, ussd",
                customer: {
                    email: userEmail,
                    phonenumber: "N/A",
                    name: "Hotel User"
                },
                callback: function (data) {
                    window.location.href = `book_room.php?room_id=<?= $room['id'] ?>&check_in=${checkIn}&check_out=${checkOut}&tx_ref=${data.tx_ref}`;
                },
                customizations: {
                    title: "Hotel Booking",
                    description: "Booking for <?= $room['room_name'] ?>",
                    logo: "https://yourhotel.com/logo.png"
                }
            });
        }
        </script>

        <?php endwhile; ?>
    </table>
    <a href="my_tickets.php" class="back-btn">🎫 My Tickets</a>

    <!-- <h3>Your Bookings</h3>
    <table>
        <tr>
            <th>Room</th>
            <th>Check-in</th>
            <th>Check-out</th>
            <th>Ticket</th>
        </tr>
        <?php// while ($booking = $booking_result->fetch_assoc()): ?>
        <tr>
            <td><?//= $booking['room_name'] ?></td>
            <td><?//= $booking['check_in'] ?></td>
            <td><?//= $booking['check_out'] ?></td>
            <td><strong><?//= $booking['ticket'] ?></strong> <br><small>Keep safe!</small></td>
        </tr>
        <?php// endwhile; ?>
    </table> -->
</div>

</body>
</html>

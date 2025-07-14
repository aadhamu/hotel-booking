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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard | Etihad Hotel</title>
    <script src="https://checkout.flutterwave.com/v3.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #2c3e50;
            --secondary: #3498db;
            --accent: #f39c12;
            --light: #ecf0f1;
            --dark: #2c3e50;
            --success: #27ae60;
            --danger: #e74c3c;
            --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f5f7fa;
            color: #333;
            line-height: 1.6;
            padding-top: 80px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        /* Navigation */
        nav {
            background-color: var(--primary);
            color: white;
            padding: 15px 40px;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: var(--shadow);
        }

        .nav-logo {
            font-size: 1.8rem;
            font-weight: 700;
            color: white;
        }

        .nav-links {
            display: flex;
            gap: 25px;
        }

        .nav-links a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .nav-links a:hover {
            color: var(--accent);
        }

        .nav-links i {
            font-size: 1.1rem;
        }

        /* Dashboard Content */
        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .welcome-message {
            font-size: 1.8rem;
            color: var(--dark);
            margin-bottom: 5px;
        }

        .email-display {
            color: #7f8c8d;
            font-size: 0.9rem;
        }

        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background-color: var(--secondary);
            color: white;
            padding: 10px 20px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
            box-shadow: var(--shadow);
        }

        .back-btn:hover {
            background-color: #2980b9;
            transform: translateY(-2px);
        }

        /* Cards Section */
        .cards-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }

        .card {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: var(--shadow);
            transition: var(--transition);
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .card-title {
            font-size: 1.2rem;
            color: var(--dark);
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .card-title i {
            color: var(--accent);
        }

        .card-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 10px;
        }

        .card-desc {
            color: #7f8c8d;
            font-size: 0.9rem;
        }

        /* Tables */
        .section-title {
            font-size: 1.5rem;
            color: var(--dark);
            margin: 30px 0 20px;
            position: relative;
            padding-bottom: 10px;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 60px;
            height: 3px;
            background-color: var(--accent);
        }

        .table-container {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: var(--shadow);
            margin-bottom: 40px;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 15px 20px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        th {
            background-color: var(--primary);
            color: white;
            font-weight: 500;
        }

        tr:last-child td {
            border-bottom: none;
        }

        tr:hover {
            background-color: #f8f9fa;
        }

        /* Form Elements */
        input[type="date"] {
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 0.9rem;
            transition: var(--transition);
            width: 100%;
            max-width: 200px;
        }

        input[type="date"]:focus {
            border-color: var(--secondary);
            outline: none;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary {
            background-color: var(--accent);
            color: white;
        }

        .btn-primary:hover {
            background-color: #e67e22;
            transform: translateY(-2px);
        }

        .btn-secondary {
            background-color: var(--secondary);
            color: white;
        }

        .btn-secondary:hover {
            background-color: #2980b9;
            transform: translateY(-2px);
        }

        /* Ticket Badge */
        .ticket-badge {
            background-color: var(--light);
            padding: 5px 10px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.9rem;
            color: var(--dark);
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .dashboard-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }

            .cards-container {
                grid-template-columns: 1fr;
            }

            th, td {
                padding: 12px 15px;
            }

            .nav-links {
                gap: 15px;
            }
        }

        @media (max-width: 576px) {
            nav {
                padding: 15px 20px;
                flex-direction: column;
                gap: 15px;
            }

            .nav-links {
                width: 100%;
                justify-content: space-between;
            }

            .table-container {
                border-radius: 0;
            }
        }

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }
    </style>
</head>
<body>
    <nav>
        <div class="nav-logo">Etihad Hotel</div>
        <div class="nav-links">
            <a href="explore_rooms.php"><i class="fas fa-door-open"></i> Rooms</a>
            <a href="user_dashboard.php" class="active"><i class="fas fa-user"></i> Dashboard</a>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </nav>

    <div class="container">
        <div class="dashboard-header">
            <div>
                <h1 class="welcome-message">Welcome back</h1>
            </div>
            <a href="index.php" class="back-btn">
                <i class="fas fa-arrow-left"></i> Back to Home
            </a>
        </div>

        <div class="cards-container">
            <div class="card fade-in">
                <h3 class="card-title"><i class="fas fa-door-open"></i> Available Rooms</h3>
                <p class="card-value"><?= $rooms->num_rows ?></p>
                <p class="card-desc">Rooms ready for booking</p>
            </div>

            <div class="card fade-in">
                <h3 class="card-title"><i class="fas fa-ticket-alt"></i> Your Bookings</h3>
                <p class="card-value"><?= $booking_result->num_rows ?></p>
                <p class="card-desc">Your current reservations</p>
            </div>

            <div class="card fade-in">
                <h3 class="card-title"><i class="fas fa-star"></i> Member Since</h3>
                <p class="card-value"><?= date('Y', strtotime($_SESSION['created_at'] ?? 'now')) ?></p>
                <p class="card-desc">Thank you for being with us</p>
            </div>
        </div>

        <h2 class="section-title">Available Rooms</h2>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Room</th>
                        <th>Price</th>
                        <th>Check-in</th>
                        <th>Check-out</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($room = $rooms->fetch_assoc()): ?>
                    <tr class="fade-in">
                        <td>
                            <strong><?= $room['room_name'] ?></strong><br>
                            <small>Room ID: <?= $room['id'] ?></small>
                        </td>
                        <td>₦<?= number_format($room['price'], 2) ?></td>
                        <td><input type="date" id="check_in_<?= $room['id'] ?>" required></td>
                        <td><input type="date" id="check_out_<?= $room['id'] ?>" required></td>
                        <td>
                            <button class="btn btn-primary" onclick="payWithFlutterwave<?= $room['id'] ?>()">
                                <i class="fas fa-credit-card"></i> Book Now
                            </button>
                        </td>
                    </tr>
<script>
function payWithFlutterwave<?= $room['id'] ?>() {
    const checkIn = document.getElementById("check_in_<?= $room['id'] ?>").value;
    const checkOut = document.getElementById("check_out_<?= $room['id'] ?>").value;
    const price = <?= $room['price'] ?>;

    if (!checkIn || !checkOut) {
        alert("Please select both check-in and check-out dates.");
        return;
    }
    if (new Date(checkOut) <= new Date(checkIn)) {
        alert("Check-out date must be after check-in date.");
        return;
    }

    FlutterwaveCheckout({
        public_key: "FLWPUBK-8a0a8fc1a126ab8837395696e4d569ca-X", // your live Flutterwave public key
        tx_ref: "TKT-" + Date.now(),
        amount: price,
        currency: "NGN",
        customer: {
            email: "<?= $_SESSION['email'] ?? 'guest@example.com' ?>",
            name: "<?= $_SESSION['name'] ?? 'Guest' ?>"
        },
        customizations: {
            title: "Etihad Hotel Booking",
            description: "Payment for <?= $room['room_name'] ?>",
            logo: "https://your-logo.com/logo.png"
        },
      callback: function(response) {
    console.log("Payment callback response:", response);

    const txId = response.transaction_id || response.transactionId; // supports both formats
    const status = response.status?.toLowerCase(); // handle undefined/null safely

    if (status === "successful" || status === "success" || status === "completed") {
        if (txId) {
            window.location.href = `verify_payment.php?transaction_id=${txId}&room_id=<?= $room['id'] ?>&check_in=${checkIn}&check_out=${checkOut}`;
        } else {
            alert("❌ Payment succeeded but no transaction ID received.");
        }
    } else {
        alert("❌ Payment was not completed. Status: " + response.status);
    }
},

    });
}
</script>


                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <a href="my_tickets.php" class="btn btn-secondary">
            <i class="fas fa-ticket-alt"></i> View My Tickets
        </a>
    </div>
</body>
</html>





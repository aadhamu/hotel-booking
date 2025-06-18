<?php  
include 'config.php';
session_start();   

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$rooms = $conn->query("SELECT * FROM rooms WHERE id NOT IN (SELECT room_id FROM bookings)");

// Search functionality for ticket number
$search = isset($_GET['search_ticket']) ? $conn->real_escape_string($_GET['search_ticket']) : '';

$bookingsQuery = "SELECT bookings.id, users.name AS user_name, rooms.room_name, bookings.check_in, bookings.check_out, bookings.ticket
                  FROM bookings 
                  JOIN users ON bookings.user_id = users.id
                  JOIN rooms ON bookings.room_id = rooms.id";

if (!empty($search)) {
    $bookingsQuery .= " WHERE bookings.ticket LIKE '%$search%'";
}

$bookings = $conn->query($bookingsQuery);

$noTicketFound = ($search && $bookings->num_rows == 0); // Check if no results were found
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        /* General Reset & Font */
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
            padding-top: 100px; /* space for fixed navbar */
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
            background-color: #000; /* Dark header */
            color: white;
            font-size: 1rem;
        }

        tr:hover {
            background-color: #f9f9f9;
        }

        tr:last-child td {
            border-bottom: none;
        }

        /* Input fields (For adding/editing) */
        input[type="text"], input[type="number"], input[type="date"], textarea {
            padding: 10px 15px;
            border: 1px solid #ccc;
            border-radius: 8px;
            width: 100%;
            box-sizing: border-box;
            margin-bottom: 10px;
            outline: none;
            transition: border-color 0.3s ease;
        }

        input[type="text"]:focus, input[type="number"]:focus, input[type="date"]:focus, textarea:focus {
            border-color: #f9c74f;
        }

        /* Buttons */
        button {
            padding: 12px 25px;
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

        /* Edit/Delete Button Styling */
        .edit-btn, .delete-btn {
            padding: 6px 15px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: bold;
            display: inline-block;
            margin-right: 10px;
            transition: background 0.3s ease, color 0.3s ease;
        }

        .edit-btn {
            background-color: #4caf50;
            color: white;
        }

        .delete-btn {
            background-color: #f44336;
            color: white;
        }

        .edit-btn:hover {
            background-color: #45a049;
        }

        .delete-btn:hover {
            background-color: #e53935;
        }

        /* Form Wrapper */
        .form-wrapper {
            margin-top: 30px;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            animation: fadeInUp 1.2s ease-out;
        }

        /* Section Headers */
        .section-header {
            margin-bottom: 15px;
            font-size: 1.5rem;
            color: #333;
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

            .form-wrapper {
                padding: 15px;
            }

            .section-header {
                font-size: 1.2rem;
            }
        }

        /* Popup Message */
        .popup-message {
            display: none;
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: #f44336;
            color: white;
            padding: 10px 20px;
            border-radius: 10px;
            font-weight: bold;
            z-index: 9999;
        }

    </style>
</head>
<body>

<div class="container">

    <h2>Admin Dashboard</h2>

    <h3>Add New Room</h3>
    <form action="add_room.php" method="POST" enctype="multipart/form-data">
        <input type="text" name="room_name" placeholder="Room Name" required>
        <input type="number" name="price" placeholder="Price per Night (₦)" required>
        <input type="file" name="image" accept="image/*" required>
        

        <button type="submit">Add Room</button> 
    </form>

    <h3>Available Rooms</h3>
    <table>
        <tr>
            <th>ID</th>
            <th>Room Name</th>
            <th>Price (₦)</th>
            <th>Actions</th>
        </tr>
        <?php while ($room = $rooms->fetch_assoc()): ?>
        <tr>
            <td><?= $room['id'] ?></td>
            <td><?= htmlspecialchars($room['room_name']) ?></td>
            <td>₦<?= number_format($room['price']) ?></td>
            <td>
                <a href="edit_room.php?id=<?= $room['id'] ?>" class="edit-btn">Edit</a> | 
                <a href="delete_room.php?id=<?= $room['id'] ?>" onclick="return confirm('Are you sure?')" class="delete-btn">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

    <h3>Room Bookings</h3>

    <!-- Search Bar for Tickets -->
    <form method="GET" style="margin-bottom: 20px;">
        <input type="text" name="search_ticket" placeholder="Search by Ticket Number" value="<?= isset($_GET['search_ticket']) ? htmlspecialchars($_GET['search_ticket']) : '' ?>" style="padding:10px; border-radius:8px; width:250px;">
        <button type="submit">Search</button>
    </form>

    <?php if ($noTicketFound): ?>
        <div class="popup-message" id="popupMessage">No ticket found!</div>
    <?php endif; ?>

    <table>
        <tr>
            <th>ID</th>
            <th>Customer Name</th>
            <th>Room</th>
            <th>Check-in</th>
            <th>Check-out</th>
            <th>Ticket</th>
        </tr>
        <?php while ($booking = $bookings->fetch_assoc()): ?>
        <tr>
            <td><?= $booking['id'] ?></td>
            <td><?= htmlspecialchars($booking['user_name']) ?></td>
            <td><?= htmlspecialchars($booking['room_name']) ?></td>
            <td><?= $booking['check_in'] ?></td>
            <td><?= $booking['check_out'] ?></td>
            <td><?= $booking['ticket'] ?></td>
        </tr>
        <?php endwhile; ?>
    </table>

    <a href="admin_dashboard.php" class="back-btn">Back to Dashboard</a>

</div>

<script>
    // Display the popup for 6 seconds if no ticket is found
    <?php if ($noTicketFound): ?>
    setTimeout(function() {
        document.getElementById('popupMessage').style.display = 'none';
    }, 6000);
    document.getElementById('popupMessage').style.display = 'block';
    <?php endif; ?>
</script>

</body>
</html>  
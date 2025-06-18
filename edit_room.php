<?php 
include 'config.php';

$room = null; // Initialize $room first

if (isset($_GET['id'])) {
    $room_id = $_GET['id'];
    $result = $conn->query("SELECT * FROM rooms WHERE id = $room_id");

    if ($result->num_rows > 0) {
        $room = $result->fetch_assoc();
    } else {
        echo "❌ Room not found.";
        exit();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $room_id = $_POST['id'];
    $room_name = $_POST['room_name'];
    $price = $_POST['price'];

    $query = "UPDATE rooms SET room_name='$room_name', price='$price' WHERE id='$room_id'";
    if ($conn->query($query)) {
        echo "✅ Room updated successfully!";
        header("Location: admin_dashboard.php");
        exit();
    } else {
        echo "❌ Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Room</title>
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
            padding-bottom: 50px;
        }

        /* Container */
        .container {
            max-width: 600px;
            margin: auto;
            padding: 20px;
        }

        /* Heading */
        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
            animation: fadeInUp 1s ease-out;
        }

        /* Form Styling */
        form {
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            animation: fadeInUp 1.2s ease-out;
        }

        label {
            font-size: 1.2rem;
            color: #333;
        }

        input[type="text"], input[type="number"] {
            padding: 10px 15px;
            border: 1px solid #ccc;
            border-radius: 8px;
            width: 100%;
            box-sizing: border-box;
            margin-bottom: 15px;
            outline: none;
            transition: border-color 0.3s ease;
        }

        input[type="text"]:focus, input[type="number"]:focus {
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
            width: 100%;
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
            margin-top: 20px;
            transition: background 0.3s ease, color 0.3s ease;
            text-align: center;
        }

        .back-btn:hover {
            background: #f9844a;
            color: #fff;
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
            form {
                padding: 20px;
            }

            button {
                padding: 10px 20px;
            }
        }
    </style>
</head>
<body>

<div class="container">

    <h2>Edit Room</h2>

    <?php if ($room): ?>
        <form action="edit_room.php" method="POST">
            <input type="hidden" name="id" value="<?= $room['id'] ?>">

            <label>Room Name:</label><br>
            <input type="text" name="room_name" value="<?= htmlspecialchars($room['room_name']) ?>" required><br><br>

            <label>Current Price: ₦<?= number_format($room['price']) ?></label><br><br>
            <input type="number" name="price" placeholder="Enter New Price (₦)" required><br><br>

            <button type="submit">Update Room</button>
        </form>
    <?php else: ?>
        <p>❗ No room selected for editing.</p>
    <?php endif; ?>

    <a href="admin_dashboard.php" class="back-btn">Back to Dashboard</a>

</div>

</body>
</html>

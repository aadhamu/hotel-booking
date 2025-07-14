<?php
include 'config.php';

// ✅ Only show rooms that are NOT booked
$roomsQuery = "SELECT * FROM rooms WHERE status != 'booked'";
$roomsResult = $conn->query($roomsQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Explore Rooms</title>
    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
    }

    body {
        background: #f4f8fc;
        color: #333;
        line-height: 1.6;
        padding: 40px 20px;
    }

    .rooms-container {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 30px;
        max-width: 1200px;
        margin: auto;
    }

    .room {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 6px 12px rgba(0,0,0,0.1);
        width: 320px;
        overflow: hidden;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .room:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 18px rgba(0,0,0,0.15);
    }

    .room img {
        width: 100%;
        height: 220px;
        object-fit: cover;
    }

    .room h3 {
        font-size: 1.4rem;
        padding: 15px 20px 5px;
        color: #222;
    }

    .room p {
        padding: 0 20px 10px;
        color: #0056b3;
        font-size: 1rem;
        font-weight: bold;
    }

    .btn {
        display: block;
        margin: 15px 20px 20px;
        text-align: center;
        background: #007bff;
        color: #fff;
        padding: 12px 20px;
        border-radius: 30px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease-in-out;
    }

    .btn:hover {
        background: #0056b3;
    }

    .go-back-container {
        text-align: center;
        margin-bottom: 30px;
    }

    .go-back-btn {
        display: inline-block;
        background: #007bff;
        color: #fff;
        padding: 12px 30px;
        border-radius: 50px;
        font-size: 1rem;
        font-weight: bold;
        text-decoration: none;
        transition: all 0.3s ease-in-out;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.15);
    }

    .go-back-btn:hover {
        background: #0056b3;
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.25);
    }

    .go-back-btn:active {
        transform: translateY(0);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.15);
    }

    @media screen and (max-width: 768px) {
        .rooms-container {
            flex-direction: column;
            align-items: center;
        }

        .room {
            width: 90%;
        }
    }
    </style>
</head>
<body>

<div class="go-back-container">
    <a href="javascript:void(0);" class="go-back-btn" onclick="window.history.back();">← Go Back</a>
</div>

<?php
if ($roomsResult->num_rows > 0) {
    echo '<div class="rooms-container">';
    while ($room = $roomsResult->fetch_assoc()) {
        $room_name = htmlspecialchars($room['room_name']);
        $price = number_format($room['price']);
        $image_path = $room['image'];

        echo "<div class='room'>";
        echo "<img src='" . (!empty($image_path) ? $image_path : "default-room.jpg") . "' alt='" . $room_name . "'>";
        echo "<h3>" . $room_name . "</h3>";
        echo "<p>₦" . $price . "/night</p>";
        echo "<a href='user_dashboard.php' class='btn'>Book Now</a>";
        echo "</div>";
    }
    echo '</div>';
} else {
    echo "<p style='text-align: center;'>No available rooms at the moment.</p>";
}
?>

</body>
</html>

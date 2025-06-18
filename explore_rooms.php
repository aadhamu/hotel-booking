<a href="javascript:void(0);" class="go-back-btn" onclick="window.history.back();">Go Back</a>

<?php
include 'config.php';

$roomsQuery = "SELECT * FROM rooms";
$roomsResult = $conn->query($roomsQuery);

if ($roomsResult->num_rows > 0) {
    echo '<style>
    /* General Reset */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
    }

    /* Body Background */
    body {
        background: #f8f8f8;
        color: #333;
        line-height: 1.6;
    }

    /* Room Container */
    .rooms-container {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        justify-content: space-around;
        padding: 50px 0;
    }

    /* Room Item */
    .room {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        width: 300px;
        overflow: hidden;
        transition: transform 0.3s ease;
    }

    .room:hover {
        transform: scale(1.05);
    }

    .room img {
        width: 100%;
        height: auto;
        object-fit: cover;
    }

    .room h3 {
        font-size: 1.5rem;
        padding: 20px;
        color: #333;
    }

    .room p {
        padding: 0 20px 20px;
        color: #f9c74f;
        font-size: 1.1rem;
    }

    .room p.no-image {
        padding: 0 20px 20px;
        color: #999;
        font-size: 1.1rem;
    }

    /* Button Style */
    .btn {
        display: inline-block;
        background: #f9c74f;
        color: #000;
        padding: 10px 20px;
        border-radius: 30px;
        text-decoration: none;
        font-weight: bold;
        transition: background 0.3s ease;
    }

    .btn:hover {
        background: #f9844a;
        color: #fff;
    }
        /* Go Back Button Style */
.go-back-btn {
    display: inline-block;
    background: #f9c74f; /* Bright yellow background */
    color: #000; /* Black text */
    padding: 12px 30px; /* Larger padding for a better button appearance */
    border-radius: 50px; /* Rounded corners for a modern look */
    font-size: 1rem; /* Text size */
    font-weight: bold; /* Bold text for prominence */
    text-decoration: none; /* Remove underline */
    text-align: center; /* Center the text */
    margin-top: 30px; /* Add some space above the button */
    transition: all 0.3s ease-in-out; /* Smooth transition effect */
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2); /* Add a subtle shadow */
}

.go-back-btn:hover {
    background: #f9844a; /* Darken background on hover */
    color: #fff; /* Change text color to white on hover */
    transform: translateY(-2px); /* Slightly lift the button on hover */
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3); /* Add a stronger shadow on hover */
}

.go-back-btn:active {
    transform: translateY(0); /* Reset to normal position when clicked */
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2); /* Return to normal shadow */
}

    </style>';
   
   

   
    echo '<div class="rooms-container">';
    while ($room = $roomsResult->fetch_assoc()) {
        $room_name = htmlspecialchars($room['room_name']);
        $price = number_format($room['price']);
        $image_path = $room['image'];  // Get the image path from the database

        // Display room details
        echo "<div class='room'>";
        echo "<h3>" . $room_name . "</h3>";
        echo "<p>₦" . $price . "/night</p>";
        if (!empty($image_path)) {
            echo "<img src='" . $image_path . "' alt='" . $room_name . "'>";
        } else {
            echo "<p class='no-image'>No image available</p>";
        }
        echo "<a href='user_dashboard.php' class='btn'>Book Now</a>"; // Button for booking (you can link it to a booking page)
        echo "</div>";
    }
    echo '</div>';
} else {
    echo "<p>No rooms available.</p>";
}
?>

<?php
include 'config.php';

if (isset($_GET['id'])) {
    $room_id = $_GET['id'];
    $query = "DELETE FROM rooms WHERE id='$room_id'";

    if ($conn->query($query)) {
        echo "✅ Room deleted successfully!";
        header("Location: admin_dashboard.php"); 
    } else {
        echo "❌ Error: " . $conn->error;
    }
}
?>

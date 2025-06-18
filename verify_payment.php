<?php
include 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    die("User not logged in.");
}

// Validate required GET parameters
if (!isset($_GET['tx_ref']) || !isset($_GET['room_id']) || !isset($_GET['check_in']) || !isset($_GET['check_out'])) {
    die("Missing data for booking.");
}

$tx_ref = $_GET['tx_ref'];
$room_id = $_GET['room_id'];
$check_in = $_GET['check_in'];
$check_out = $_GET['check_out'];
$user_id = $_SESSION['user_id'];

// === VERIFY PAYMENT WITH FLUTTERWAVE API ===
$curl = curl_init();

curl_setopt_array($curl, array(
    CURLOPT_URL => "https://api.flutterwave.com/v3/transactions/verify_by_reference?tx_ref=$tx_ref",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "GET",
    CURLOPT_HTTPHEADER => array(
        "Authorization: Bearer FLWSECK_TEST-885e558d3fded9c4aca2342187914b28-X", // Replace with your actual Flutterwave Secret Key
        "Content-Type: application/json"
    ),
));

$response = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);

// === HANDLE FLUTTERWAVE RESPONSE ===
if ($err) {
    die("cURL Error #:" . $err);
} else {
    $result = json_decode($response, true);

    if ($result['status'] == "success" && $result['data']['status'] == "successful") {
        // Generate booking ticket
        $ticket = strtoupper(uniqid("TKT-"));
        
        // Insert the booking into the database
        $stmt = $conn->prepare("INSERT INTO bookings (user_id, room_id, check_in, check_out, ticket) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iisss", $user_id, $room_id, $check_in, $check_out, $ticket);

        if ($stmt->execute()) {
            // Mark room as booked
            $update_room_status = $conn->prepare("UPDATE rooms SET status = 'booked' WHERE id = ?");
            $update_room_status->bind_param("i", $room_id);
            $update_room_status->execute();

            echo "<h2>✅ Booking Successful</h2>";
            echo "<p>Your Booking Ticket: <strong>$ticket</strong></p>";
            echo "<p>Transaction Reference: <strong>$tx_ref</strong></p>";
            echo "<p>Redirecting to your dashboard...</p>";
            header("refresh:5;url=user_dashboard.php");
        } else {
            echo "❌ Booking failed: " . $conn->error;
        }
    } else {
        echo "<h3>❌ Payment verification failed or incomplete transaction.</h3>";
    }
}
?>

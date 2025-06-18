<?php 
include 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    die("User not logged in.");
}

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['room_id'], $_GET['check_in'], $_GET['check_out'], $_GET['tx_ref'])) {
    $user_id = $_SESSION['user_id'];
    $room_id = intval($_GET['room_id']);
    $check_in = $_GET['check_in'];
    $check_out = $_GET['check_out'];
    $tx_ref = $_GET['tx_ref']; // Flutterwave transaction reference

    // Step 1: Verify payment with Flutterwave API
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
            "Authorization: Bearer YOUR_FLUTTERWAVE_SECRET_KEY", // Replace with your actual secret key
            "Content-Type: application/json"
        ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    if ($err) {
        die("cURL Error #:" . $err);
    } else {
        $result = json_decode($response, true);

        // Step 2: Check if the payment was successful
        if ($result['status'] == 'success' && $result['data']['status'] == 'successful') {

            // Generate a unique booking ticket
            $ticket = strtoupper(uniqid("TKT-")); // Example: TKT-65DFG98JH2

            // Step 3: Insert booking into the database
            $stmt = $conn->prepare("INSERT INTO bookings (user_id, room_id, check_in, check_out, ticket) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("iisss", $user_id, $room_id, $check_in, $check_out, $ticket);

            if ($stmt->execute()) {
                // Step 4: Mark the room as booked
                $update_room_status = $conn->prepare("UPDATE rooms SET status = 'booked' WHERE id = ?");
                $update_room_status->bind_param("i", $room_id);
                $update_room_status->execute();

                echo "<h3>✅ Room booked successfully after payment!</h3>";
                echo "<p>Your Booking Ticket: <strong>$ticket</strong></p>";
                echo "<p>Transaction Reference: <strong>$tx_ref</strong></p>";
                echo "<p><b>⚠️ Keep this ticket safe!</b></p>";
                echo "<p>Redirecting to your dashboard in 5 seconds...</p>";
                header("refresh:5;url=user_dashboard.php");
            } else {
                echo "❌ Booking failed: " . $conn->error;
            }

            $stmt->close();
        } else {
            echo "<h3>❌ Payment verification failed or transaction was not successful.</h3>";
        }
    }
} else {
    echo "Invalid request. Missing booking or payment information.";
}
?>

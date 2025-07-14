<?php
include 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    die("<div style='color:red;'>❌ User not logged in.</div>");
}

if (!isset($_GET['transaction_id'], $_GET['room_id'], $_GET['check_in'], $_GET['check_out'])) {
    die("<div style='color:red;'>❌ Missing booking data.</div>");
}

$transaction_id = $_GET['transaction_id'];
$room_id = intval($_GET['room_id']);
$check_in = $_GET['check_in'];
$check_out = $_GET['check_out'];
$user_id = $_SESSION['user_id'];

// === CALL FLUTTERWAVE VERIFY API ===
$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => "https://api.flutterwave.com/v3/transactions/{$transaction_id}/verify",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        "Authorization: Bearer FLWSECK-c0f62702edc2d7524356294ee98fdb98-19808adcbf2vt-X", // LIVE SECRET KEY
        "Content-Type: application/json"
    ]
]);

$response = curl_exec($ch);
$err = curl_error($ch);
curl_close($ch);

echo "<!doctype html><html><head><meta charset='utf-8'><title>Payment Status</title>
<style>
body { font-family:sans-serif; background:#f5f5f5; padding:50px; text-align:center; }
.success { color:green; font-size:1.6rem; }
.error { color:red; font-size:1.3rem; }
.ticket-box { background:#fff; padding:25px; border-radius:10px; box-shadow:0 4px 10px rgba(0,0,0,0.1); margin-top:20px; display:inline-block; }
</style></head><body>";

if ($err) {
    echo "<div class='error'>❌ API Error: $err</div>";
    exit();
}

$result = json_decode($response, true);

// === LOG RESPONSE FOR DEBUGGING ===
file_put_contents("fw_verify_debug.log", print_r($result, true));

if (
    isset($result['status'], $result['data']['status']) &&
    $result['status'] === 'success' &&
    strtolower($result['data']['status']) === 'successful'
) {
    $ticket = strtoupper(uniqid("TKT-"));
    
    $stmt = $conn->prepare("INSERT INTO bookings (user_id, room_id, check_in, check_out, ticket) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iisss", $user_id, $room_id, $check_in, $check_out, $ticket);
    
    if ($stmt->execute()) {
        $conn->query("UPDATE rooms SET status='booked' WHERE id=$room_id");

        echo "<div class='ticket-box'>
            <div class='success'>✅ Payment Successful & Booking Confirmed</div>
            <p><strong>Ticket:</strong> $ticket</p>
            <p><strong>Transaction ID:</strong> $transaction_id</p>
            <p>Redirecting to your dashboard...</p>
        </div>";
        header("refresh:5;url=user_dashboard.php");
    } else {
        echo "<div class='error'>❌ Failed to save booking: " . $stmt->error . "</div>";
    }
} else {
    $fail_reason = $result['data']['status'] ?? 'unknown';
    echo "<div class='error'>❌ Payment not completed. Status: $fail_reason</div>
          <p>Please retry or contact support if debited.</p>";
}

echo "</body></html>";

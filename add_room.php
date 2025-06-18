<?php
include 'config.php';

$room_name = $_POST['room_name'];
$price = $_POST['price'];

$image_path = null; // Default to null if no image is uploaded

if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $targetDir = "uploads/";

    // Create the directory if it doesn't exist
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }

    $filename = basename($_FILES["image"]["name"]);
    $targetFile = $targetDir . $filename;

    // Optionally: rename to avoid duplicate file names
    $fileType = pathinfo($filename, PATHINFO_EXTENSION);
    $uniqueName = uniqid("room_", true) . "." . $fileType;
    $image_path = $targetDir . $uniqueName;

    if (move_uploaded_file($_FILES["image"]["tmp_name"], $image_path)) {
        // Image successfully uploaded
    } else {
        die("Error: Failed to move uploaded file.");
    }
} else {
    die("Error: No image uploaded or upload failed.");
}

// Insert into DB
$stmt = $conn->prepare("INSERT INTO rooms (room_name, price, image) VALUES (?, ?, ?)");
$stmt->bind_param("sis", $room_name, $price, $image_path);
$stmt->execute();

header("Location: admin_dashboard.php");
exit();

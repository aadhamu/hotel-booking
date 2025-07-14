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
    <title>Admin Dashboard | Etihad Hotel</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
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
            --warning: #f1c40f;
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

        /* Navigation */
        nav {
            background-color: var(--primary);
            color: white;
            padding: 15px 20px;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: var(--shadow);
            flex-wrap: wrap;
        }

        .nav-logo {
            font-size: 1.5rem;
            font-weight: 700;
            color: white;
            order: 1;
        }

        .mobile-menu-btn {
            display: none;
            background: none;
            border: none;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
            order: 2;
            padding: 5px;
        }

        .nav-links {
            display: flex;
            gap: 25px;
            order: 3;
        }

        .nav-links a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 5px;
            padding: 5px 0;
        }

        .nav-links a:hover {
            color: var(--accent);
        }

        .nav-links i {
            font-size: 1.1rem;
        }

        /* Container */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        /* Dashboard Header */
        .dashboard-header {
            text-align: center;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }

        .dashboard-header h1 {
            font-size: 2.2rem;
            color: var(--primary);
            margin-bottom: 10px;
        }

        .dashboard-header p {
            color: #7f8c8d;
            font-size: 1rem;
        }

        /* Cards */
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

        /* Section Titles */
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

        /* Tables */
        .table-container {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: var(--shadow);
            margin-bottom: 40px;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 600px;
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

        /* Forms */
        .form-container {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: var(--shadow);
            margin-bottom: 40px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--dark);
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
            transition: var(--transition);
        }

        .form-control:focus {
            border-color: var(--secondary);
            outline: none;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
        }

        /* Buttons */
        .btn {
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 8px;
            min-height: 44px;
        }

        .btn:active {
            transform: scale(0.98);
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

        .btn-danger {
            background-color: var(--danger);
            color: white;
        }

        .btn-danger:hover {
            background-color: #c0392b;
            transform: translateY(-2px);
        }

        .btn-success {
            background-color: var(--success);
            color: white;
        }

        .btn-success:hover {
            background-color: #219955;
            transform: translateY(-2px);
        }

        /* Action Buttons */
        .action-btns {
            display: flex;
            gap: 10px;
        }

        .action-btn {
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 0.9rem;
            text-decoration: none;
            transition: var(--transition);
            min-height: 44px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        /* Search Bar */
        .search-container {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        .search-input {
            flex: 1;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
            transition: var(--transition);
        }

        .search-input:focus {
            border-color: var(--secondary);
            outline: none;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
        }

        /* File Input */
        .file-input {
            display: none;
        }

        .file-label {
            display: inline-block;
            padding: 12px 25px;
            background-color: var(--light);
            color: var(--dark);
            border-radius: 8px;
            cursor: pointer;
            transition: var(--transition);
            margin-bottom: 15px;
            border: 1px dashed #ccc;
            text-align: center;
            width: 100%;
        }

        .file-label:hover {
            background-color: #e0e0e0;
        }

        /* Popup Message */
        .popup-message {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 25px;
            border-radius: 8px;
            font-weight: 500;
            z-index: 9999;
            box-shadow: var(--shadow);
            display: flex;
            align-items: center;
            gap: 10px;
            animation: slideIn 0.3s ease-out;
        }

        .popup-error {
            background-color: var(--danger);
            color: white;
        }

        .popup-success {
            background-color: var(--success);
            color: white;
        }

        .popup-warning {
            background-color: var(--warning);
            color: var(--dark);
        }

        /* Animations */
        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes fadeOut {
            to {
                opacity: 0;
            }
        }

        .fade-out {
            animation: fadeOut 0.5s ease-out forwards;
        }

        /* Responsive Design */
        @media (max-width: 992px) {
            .container {
                padding: 15px;
            }
            
            .card {
                padding: 20px;
            }
            
            th, td {
                padding: 12px 15px;
            }
        }

        @media (max-width: 768px) {
            .mobile-menu-btn {
                display: block;
            }

            .nav-links {
                display: none;
                width: 100%;
                flex-direction: column;
                gap: 10px;
                padding-top: 15px;
            }

            .nav-links.active {
                display: flex;
            }

            .nav-links a {
                padding: 10px 0;
                border-bottom: 1px solid rgba(255,255,255,0.1);
            }

            .cards-container {
                grid-template-columns: 1fr;
            }

            .section-title {
                font-size: 1.3rem;
            }

            .form-container {
                padding: 20px;
            }

            .search-container {
                flex-direction: column;
            }

            .search-container button {
                width: 100%;
            }
        }

        @media (max-width: 576px) {
            body {
                padding-top: 70px;
            }

            .dashboard-header h1 {
                font-size: 1.8rem;
            }

            .card-value {
                font-size: 1.8rem;
            }

            .btn, .action-btn {
                padding: 10px 15px;
                font-size: 0.9rem;
            }

            .form-group {
                margin-bottom: 15px;
            }

            .form-control {
                padding: 10px 12px;
            }
        }
    </style>
</head>
<body>
    <nav>
        <div class="nav-logo">Etihad Hotel</div>
        <button class="mobile-menu-btn" id="mobileMenuBtn">
            <i class="fas fa-bars"></i>
        </button>
        <div class="nav-links" id="navLinks">
            <a href="admin_dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="#booking"><i class="fas fa-calendar-check"></i> Bookings</a>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </nav>

    <div class="container">
        <div class="dashboard-header">
            <h1>Admin Dashboard</h1>
            <p>Manage rooms, bookings, and hotel operations</p>
        </div>

        <div class="cards-container">
            <div class="card">
                <h3 class="card-title"><i class="fas fa-hotel"></i> Available Rooms</h3>
                <p class="card-value"><?= $rooms->num_rows ?></p>
                <p class="card-desc">Rooms ready for booking</p>
            </div>

            <div class="card">
                <h3 class="card-title"><i class="fas fa-calendar-check"></i> Total Bookings</h3>
                <p class="card-value"><?= $bookings->num_rows ?></p>
                <p class="card-desc">Current reservations</p>
            </div>

            <div class="card">
                <h3 class="card-title"><i class="fas fa-users"></i> Active Admin</h3>
                <p class="card-value"><?= $_SESSION['name'] ?? 'Admin' ?></p>
                <p class="card-desc">You're logged in</p>
            </div>
        </div>

        <h2 class="section-title">Add New Room</h2>
        <div class="form-container">
            <form action="add_room.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="room_name">Room Name</label>
                    <input type="text" id="room_name" name="room_name" class="form-control" placeholder="Deluxe Suite" required>
                </div>

                <div class="form-group">
                    <label for="price">Price per Night (₦)</label>
                    <input type="number" id="price" name="price" class="form-control" placeholder="50000" required>
                </div>

                <div class="form-group">
                    <label for="image">Room Image</label>
                    <input type="file" id="image" name="image" class="file-input" accept="image/*" required>
                    <label for="image" class="file-label">
                        <i class="fas fa-cloud-upload-alt"></i> Choose an image...
                    </label>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-plus-circle"></i> Add Room
                </button>
            </form>
        </div>

        <h2 class="section-title">Available Rooms</h2>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Room Name</th>
                        <th>Price (₦)</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($room = $rooms->fetch_assoc()): ?>
                    <tr>
                        <td><?= $room['id'] ?></td>
                        <td><?= htmlspecialchars($room['room_name']) ?></td>
                        <td>₦<?= number_format($room['price'], 2) ?></td>
                        <td>
                            <div class="action-btns">
                                <a href="edit_room.php?id=<?= $room['id'] ?>" class="action-btn btn-secondary">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <a href="delete_room.php?id=<?= $room['id'] ?>" onclick="return confirm('Are you sure you want to delete this room?')" class="action-btn btn-danger">
                                    <i class="fas fa-trash-alt"></i> Delete
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <h2 class="section-title" id="booking">Room Bookings</h2>
        
        <div class="search-container">
            <form method="GET" style="flex: 1; display: flex; gap: 10px;">
                <input type="text" name="search_ticket" class="search-input" placeholder="Search by Ticket Number" value="<?= isset($_GET['search_ticket']) ? htmlspecialchars($_GET['search_ticket']) : '' ?>">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Search
                </button>
            </form>
        </div>

        <?php if ($noTicketFound): ?>
            <div class="popup-message popup-error" id="popupMessage">
                <i class="fas fa-exclamation-circle"></i> No ticket found!
            </div>
        <?php endif; ?>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Booking ID</th>
                        <th>Customer</th>
                        <th>Room</th>
                        <th>Check-in</th>
                        <th>Check-out</th>
                        <th>Ticket</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($booking = $bookings->fetch_assoc()): ?>
                    <tr>
                        <td><?= $booking['id'] ?></td>
                        <td><?= htmlspecialchars($booking['user_name']) ?></td>
                        <td><?= htmlspecialchars($booking['room_name']) ?></td>
                        <td><?= date('M j, Y', strtotime($booking['check_in'])) ?></td>
                        <td><?= date('M j, Y', strtotime($booking['check_out'])) ?></td>
                        <td>
                            <span class="ticket-badge"><?= $booking['ticket'] ?></span>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        // Mobile menu toggle
        document.getElementById('mobileMenuBtn').addEventListener('click', function() {
            document.getElementById('navLinks').classList.toggle('active');
        });

        // Close mobile menu when clicking a link
        document.querySelectorAll('.nav-links a').forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth <= 768) {
                    document.getElementById('navLinks').classList.remove('active');
                }
            });
        });

        // Close mobile menu when resizing to larger screen
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768) {
                document.getElementById('navLinks').classList.remove('active');
            }
        });

        // Display the popup for 6 seconds if no ticket is found
        <?php if ($noTicketFound): ?>
        setTimeout(function() {
            const popup = document.getElementById('popupMessage');
            popup.classList.add('fade-out');
            setTimeout(() => popup.remove(), 500);
        }, 6000);
        <?php endif; ?>

        // File input label update
        document.getElementById('image').addEventListener('change', function(e) {
            const fileName = e.target.files[0]?.name || 'Choose an image...';
            document.querySelector('.file-label').textContent = fileName;
        });

        // Improve button feedback
        document.querySelectorAll('.btn, .action-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                this.style.transform = 'scale(0.98)';
                setTimeout(() => {
                    this.style.transform = '';
                }, 200);
            });
        });
    </script>
</body>
</html>
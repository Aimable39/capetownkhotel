<?php
try {
    $pdo = new PDO("mysql:host=localhost;dbname=hotel_db;charset=utf8", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Receive form data
    $name = $_POST['name'];
    $email = $_POST['email'];
    $telephone = $_POST['telephone'];
    $checkin = $_POST['checkin'];
    $checkout = $_POST['checkout'];
    $guests = (int)$_POST['guests'];

    // Calculate days
    $days = (strtotime($checkout) - strtotime($checkin)) / (60*60*24);

    // Automatic room type + amount
    if ($guests <= 2) {
        $room_type = "Standard";
        $amount = $days * 50000; // 50,000 RWF/day
    } elseif ($guests <= 4) {
        $room_type = "Deluxe";
        $amount = $days * 80000;
    } else {
        $room_type = "Family";
        $amount = $days * 120000;
    }

    // Insert booking
    $stmt = $pdo->prepare("INSERT INTO bookings 
        (name, email, telephone, checkin, checkout, guests, amount, room_type, status) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Pending')");
    $stmt->execute([$name, $email, $telephone, $checkin, $checkout, $guests, $amount, $room_type]);

    echo "<h2 style='color:green;text-align:center;'>Murakoze kubooking! Tuzabahuza vuba.</h2>";
    echo "<p style='text-align:center;'><a href='admin.php'>Reba mu Admin</a></p>";

} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>

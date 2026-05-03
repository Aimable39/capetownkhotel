<?php
// Connection settings
$servername = "localhost";
$username = "root";   // hindura niba ufite username itandukanye
$password = "";       // shyiramo password yawe
$dbname = "hotel_booking";

// Connect to DB
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(["message" => "Database connection failed"]));
}

// Get form data
$name = $_POST['name'];
$email = $_POST['email'];
$telephone = $_POST['telephone'];
$checkin = $_POST['checkin'];
$checkout = $_POST['checkout'];
$guests = (int)$_POST['guests'];

// Calculate days safely
$days = (strtotime($checkout) - strtotime($checkin)) / (60*60*24);
if ($days <= 0) {
    echo json_encode(["message" => "Check-out date must be after check-in"]);
    exit;
}

// Price per day (new: $50/day → $1500/month)
$pricePerDayUSD = 50;
$totalUSD = $days * $pricePerDayUSD;
$totalRWF = $totalUSD * 1500; // conversion rate (1 USD = 1500 RWF)

// Insert into DB with status
$stmt = $conn->prepare("INSERT INTO bookings (name,email,telephone,checkin,checkout,guests,days,totalUSD,totalRWF,status) VALUES (?,?,?,?,?,?,?,?,?,?)");
$status = "Pending";
$stmt->bind_param("sssssiidds", $name, $email, $telephone, $checkin, $checkout, $guests, $days, $totalUSD, $totalRWF, $status);

if ($stmt->execute()) {
    echo json_encode([
        "message" => "Booking Successful!",
        "name" => $name,
        "email" => $email,
        "telephone" => $telephone,
        "days" => $days,
        "guests" => $guests,
        "totalUSD" => $totalUSD,
        "totalRWF" => $totalRWF,
        "status" => $status
    ]);
} else {
    echo json_encode(["message" => "Booking Failed"]);
}

$stmt->close();
$conn->close();
?>

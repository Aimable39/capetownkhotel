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

// Calculate days
$days = (strtotime($checkout) - strtotime($checkin)) / (60*60*24);

// Price per day (example: $33/day → $1000/month)
$pricePerDayUSD = 33;
$totalUSD = $days * $pricePerDayUSD;
$totalRWF = $totalUSD * 1500; // example conversion rate

// Insert into DB
$stmt = $conn->prepare("INSERT INTO bookings (name,email,telephone,checkin,checkout,guests,days,totalUSD,totalRWF) VALUES (?,?,?,?,?,?,?,?,?)");
$stmt->bind_param("sssssiidd", $name, $email, $telephone, $checkin, $checkout, $guests, $days, $totalUSD, $totalRWF);

if ($stmt->execute()) {
    echo json_encode([
        "message" => "Booking Successful!",
        "name" => $name,
        "email" => $email,
        "telephone" => $telephone,
        "days" => $days,
        "guests" => $guests,
        "totalUSD" => $totalUSD,
        "totalRWF" => $totalRWF
    ]);
} else {
    echo json_encode(["message" => "Booking Failed"]);
}

$stmt->close();
$conn->close();
?>

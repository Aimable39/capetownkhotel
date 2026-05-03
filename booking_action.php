<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hotel_booking";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) { die("Database connection failed"); }

$id = intval($_POST['id']);
$action = $_POST['action'];

if ($action == "confirm") {
    $sql = "UPDATE bookings SET status='Paid' WHERE id=$id";
} elseif ($action == "cancel") {
    $sql = "UPDATE bookings SET status='Cancelled' WHERE id=$id";
} elseif ($action == "delete") {
    $sql = "DELETE FROM bookings WHERE id=$id";
}

$conn->query($sql);
$conn->close();

header("Location: admin.php");
exit;
?>

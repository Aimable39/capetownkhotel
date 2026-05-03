<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hotel_booking";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) { die("Database connection failed"); }

// Search filter
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

// Pagination setup
$limit = 10; 
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Count total
$total_sql = "SELECT COUNT(*) as total FROM bookings";
if ($search != '') {
    $total_sql .= " WHERE name LIKE '%$search%' OR email LIKE '%$search%'";
}
$total_result = $conn->query($total_sql);
$total_row = $total_result->fetch_assoc();
$total = $total_row['total'];
$total_pages = ceil($total / $limit);

// Main query
$sql = "SELECT * FROM bookings";
if ($search != '') {
    $sql .= " WHERE name LIKE '%$search%' OR email LIKE '%$search%'";
}
$sql .= " ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="rw">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard - Bookings</title>
  <style>
    body { font-family: Arial, sans-serif; background:#f4f4f4; padding:20px; }
    h1 { text-align:center; color:#2c3e50; }
    table { width:100%; border-collapse:collapse; margin-top:20px; background:white; }
    th, td { padding:12px; border:1px solid #ddd; text-align:center; }
    th { background:#3498db; color:white; }
    tr:nth-child(even) { background:#f9f9f9; }
    button { padding:5px 10px; border:none; border-radius:5px; cursor:pointer; }
    .confirm { background:green; color:white; }
    .cancel { background:red; color:white; }
    .delete { background:gray; color:white; }
    .status-paid { color:green; font-weight:bold; }
    .status-cancelled { color:red; font-weight:bold; }
    .status-pending { color:orange; font-weight:bold; }
    .pagination a { margin:0 5px; padding:5px 10px; border:1px solid #3498db; border-radius:4px; text-decoration:none; }
    .pagination a.active { background:#3498db; color:white; }
  </style>
</head>
<body>
  <h1>Bookings Admin Dashboard</h1>

  <!-- Search form -->
  <form method="get" style="text-align:center; margin-bottom:20px;">
    <input type="text" name="search" placeholder="Shakisha izina cyangwa email..." value="<?= htmlspecialchars($search) ?>" style="padding:8px; width:250px;">
    <button type="submit" style="padding:8px 15px;">Search</button>
  </form>

  <table>
    <tr>
      <th>ID</th><th>Izina</th><th>Email</th><th>Telephone</th>
      <th>Check-in</th><th>Check-out</th><th>Guests</th><th>Iminsi</th>
      <th>Total USD</th><th>Total RWF</th><th>Status</th><th>Created At</th><th>Actions</th>
    </tr>
    <?php if ($result->num_rows > 0): ?>
      <?php while($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?= $row['id'] ?></td>
          <td><?= htmlspecialchars($row['name']) ?></td>
          <td><?= htmlspecialchars($row['email']) ?></td>
          <td><?= htmlspecialchars($row['telephone']) ?></td>
          <td><?= $row['checkin'] ?></td>
          <td><?= $row['checkout'] ?></td>
          <td><?= $row['guests'] ?></td>
          <td><?= $row['days'] ?></td>
          <td><?= $row['totalUSD'] ?></td>
          <td><?= $row['totalRWF'] ?></td>
          <td class="<?php 
              if($row['status']=='Paid') echo 'status-paid';
              elseif($row['status']=='Cancelled') echo 'status-cancelled';
              else echo 'status-pending';
          ?>"><?= $row['status'] ?></td>
          <td><?= $row['created_at'] ?></td>
          <td>
            <form method="post" action="booking_action.php" style="display:inline;">
              <input type="hidden" name="id" value="<?= $row['id'] ?>">
              <input type="hidden" name="action" value="confirm">
              <button type="submit" class="confirm">Confirm</button>
            </form>
            <form method="post" action="booking_action.php" style="display:inline;">
              <input type="hidden" name="id" value="<?= $row['id'] ?>">
              <input type="hidden" name="action" value="cancel">
              <button type="submit" class="cancel">Cancel</button>
            </form>
            <form method="post" action="booking_action.php" style="display:inline;">
              <input type="hidden" name="id" value="<?= $row['id'] ?>">
              <input type="hidden" name="action" value="delete">
              <button type="submit" class="delete">Delete</button>
            </form>
          </td>
        </tr>
      <?php endwhile; ?>
    <?php else: ?>
      <tr><td colspan="13">Nta booking irabikwa</td></tr>
    <?php endif; ?>
  </table>

  <!-- Pagination -->
  <div class="pagination" style="text-align:center; margin-top:20px;">
    <?php for($i=1; $i<=$total_pages; $i++): ?>
      <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>" class="<?= ($i==$page)?'active':'' ?>"><?= $i ?></a>
    <?php endfor; ?>
  </div>
</body>
</html>

<?php $conn->close(); ?>

<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login_page.php"); // Redirect to login if not logged in
    exit();
}
$Myname = $_SESSION['username'];

include('db_con.php');

// Query for total customers
$totalCustomers = $conn->query("SELECT COUNT(*) as total FROM customers")->fetch_assoc()['total'];

// Query for total credit amount (remaining balance)
$totalCredit = $conn->query("SELECT SUM(remaining_balance) as total FROM remaining_bill")->fetch_assoc()['total'];
$totalCredit = $totalCredit ?? 0;

// Query for total items
$totalItems = $conn->query("SELECT COUNT(*) as total FROM items_detail")->fetch_assoc()['total'];

// Query for total orders (or Over Time)
$totalOrders = $conn->query("SELECT COUNT(*) as total FROM over_time")->fetch_assoc()['total'];

// Query for recent customers
$recentCustomers = $conn->query("SELECT customer_name FROM customers ORDER BY id DESC LIMIT 5");

// Query for recent items added
$recentItems = $conn->query("SELECT item_name FROM items_detail ORDER BY id DESC LIMIT 5");

// Query for highest remaining balance (due members)
$highestBills = $conn->query("SELECT name, remaining_balance FROM remaining_bill ORDER BY remaining_balance DESC LIMIT 5");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Dashboard</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: #f4f4f4;
      display: flex;
      min-height: 100vh;
    }



    .sidebar {
      width: 250px;
      background-color: #1e3d59;
      color: white;
      flex-shrink: 0;
      padding: 20px;
      position: fixed;
      height: 100vh;
      transition: transform 0.3s ease;
    }

    .sidebar h2 {
      text-align: center;
      margin-bottom: 30px;
    }

    .sidebar ul {
      list-style: none;
    }

    .sidebar ul li {
      margin: 15px 0;
    }

    .sidebar ul li a {
      color: white;
      text-decoration: none;
      display: block;
      padding: 10px;
      border-radius: 6px;
      transition: background 0.3s;
    }

    .sidebar ul li a:hover {
      background-color: #34495e;
    }

    .logout {
      background-color: #ff4d4d;
      color: white !important;
    }

    .logout:hover {
      background-color: #e60000 !important;
    }

    .main {
      margin-left: 250px;
      padding: 20px;
      flex-grow: 1;
      transition: margin-left 0.3s ease;
      width: 100%;
    }

    .topbar {
      background: white;
      padding: 15px 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      border-bottom: 1px solid #ccc;
      position: sticky;
      top: 0;
      z-index: 100;
    }

    .toggle-btn {
      font-size: 24px;
      cursor: pointer;
      display: none;
      color: #1e3d59;
    }

    @media (max-width: 768px) {
      .sidebar {
        transform: translateX(-100%);
        position: fixed;
        z-index: 999;
      }

      .sidebar.active {
        transform: translateX(0);
      }

      .main {
        margin-left: 0;
      }

      .toggle-btn {
        display: block;
      }
    }

    .content {
      margin-top: 20px;
    }

    .dashboard-cards {
      display: grid;
      grid-template-columns: repeat(4, 1fr); /* Set to 4 cards per row */
      gap: 20px;
      margin-top: 30px;
    }

    .card {
      background: linear-gradient(to bottom, #e3f2fd, #bbdefb);
      padding: 20px;
      border-radius: 12px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
      transition: 0.3s ease-in-out;
      cursor: pointer;
      position: relative;
    }

    .card:hover {
      transform: translateY(-10px);
      box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
    }

    .card h4 {
      font-size: 20px;
      color: #1e3d59;
      margin-bottom: 12px;
      font-weight: bold;
    }

    .card p {
      font-size: 28px;
      color: #1e3d59;
      font-weight: 700;
      margin-bottom: 15px;
    }

    .card ul {
      list-style: none;
    }

    .card ul li {
      font-size: 18px;
      color: #333;
      margin-bottom: 8px;
    }

    .card i {
      font-size: 40px;
      color: #1e3d59;
      position: absolute;
      top: 15px;
      right: 15px;
    }

  </style>
</head>
<body>
  <div class="sidebar" id="sidebar">
    <h2>Bhoomi Canteen Kondigre</h2>
    <ul>
      <li><a href="index.php">🏠 Home</a></li>
      <li><a href="order.php">📝 Orders</a></li>
      <li><a href="paying_bill.php">💰 Payments</a></li>
      <li><a href="view_page.php">📄 View Bills</a></li>
      <li><a href="add_customer.php">👥 Customers</a></li>
      <li><a href="Over_time.php">⏱ OT Emp</a></li>
      <li><a href="add_items.php">🍽 Add Items</a></li>
      <li><a href="logout.php" class="logout">🚪 Logout</a></li>
    </ul>
  </div>

  <div class="main">
    
    <div class="content">
      <h1>Welcome! <?php echo $Myname ?></h1>

      <div class="dashboard-cards">
        <div class="card">
          <i class="fas fa-users"></i>
          <h4>👥 Total Customers</h4>
          <p><?= $totalCustomers ?></p>
        </div>
        <div class="card">
          <i class="fas fa-wallet"></i>
          <h4>💸 Upcoming Credit Amount</h4>
          <p>₹ <?= number_format($totalCredit, 2) ?></p>
        </div>
        <div class="card">
          <i class="fas fa-utensils"></i>
          <h4>🍽 Total Items</h4>
          <p><?= $totalItems ?></p>
        </div>
        <div class="card">
          <i class="fas fa-clock"></i>
          <h4>🧾 Total OT</h4>
          <p><?= $totalOrders ?></p>
        </div>
      </div>

      <h3>Recent Additions</h3>
      <div class="dashboard-cards">
        <div class="card">
          <i class="fas fa-user-friends"></i>
          <h4>👥 Recent Customers</h4>
          <ul>
            <?php while ($row = $recentCustomers->fetch_assoc()): ?>
              <li><?= $row['customer_name'] ?></li>
            <?php endwhile; ?>
          </ul>
        </div>
        <div class="card">
          <i class="fas fa-box"></i>
          <h4>🍽 Recent Items</h4>
          <ul>
            <?php while ($row = $recentItems->fetch_assoc()): ?>
              <li><?= $row['item_name'] ?></li>
            <?php endwhile; ?>
          </ul>
        </div>
        <div class="card">
          <i class="fas fa-dollar-sign"></i>
          <h4>💸 Highest Bill Due</h4>
          <ul>
            <?php while ($row = $highestBills->fetch_assoc()): ?>
              <li><?= $row['name'] ?> - ₹ <?= number_format($row['remaining_balance'], 2) ?></li>
            <?php endwhile; ?>
          </ul>
        </div>
        <div class="card">
  <i class="fas fa-money-bill-wave"></i>
  <h4>💵 Recent Payments</h4>
  <ul>
    <?php
    $recentPayments = $conn->query("SELECT customer_name, paid_amount, payment_date FROM paid_bill ORDER BY payment_date DESC LIMIT 5");
    while ($row = $recentPayments->fetch_assoc()):
    ?>
      <li><strong><?= htmlspecialchars($row['customer_name']) ?></strong> - ₹<?= number_format($row['paid_amount'], 2) ?> 
      <small style="color: #777;">(<?= date("d M", strtotime($row['payment_date'])) ?>)</small></li>
    <?php endwhile; ?>
  </ul>
</div>

      </div>

    </div>
  </div>

  <script src="https://kit.fontawesome.com/a076d05399.js"></script>
  <script>
    function toggleSidebar() {
      document.getElementById('sidebar').classList.toggle('active');
    }
  </script>
</body>
</html>





<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Canteen Admin Dashboard</title>
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
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 20px;
  margin-top: 30px;
}

.card {
  background: white;
  padding: 20px;
  border-radius: 12px;
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
  transition: 0.3s;
}

.card:hover {
  transform: translateY(-5px);
}

.card h4 {
  font-size: 18px;
  color: #666;
  margin-bottom: 8px;
}

.card p {
  font-size: 24px;
  color: #1e3d59;
  font-weight: bold;
}

  </style>
</head>
<body>
<?php

session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login_page.php"); // Redirect to login if not logged in
    exit();
}


$Myname = $_SESSION['username'];

include('db_con.php');

// Total customers
$totalCustomers = $conn->query("SELECT COUNT(*) as total FROM customers")->fetch_assoc()['total'];

// Total credit amount
$totalCredit = $conn->query("SELECT SUM(remaining_balance) as total FROM remaining_bill")->fetch_assoc()['total'];
$totalCredit = $totalCredit ?? 0;

// Total items
$totalItems = $conn->query("SELECT COUNT(*) as total FROM items_detail")->fetch_assoc()['total'];

// Total orders
$totalOrders = $conn->query("SELECT COUNT(*) as total FROM transaction")->fetch_assoc()['total'];


?>
  <div class="sidebar" id="sidebar">
    <h2>Canteen Panel</h2>
    <ul>
      <li><a href="index.php">🏠 Home</a></li>
      <li><a href="paying_bill.php">💰 Add Payment</a></li>
      <li><a href="view_page.php">📄 View Bills</a></li>
      <li><a href="add_customer.php">👥 Customers</a></li>
      <li><a href="Over_time.php">⏱ OT Management</a></li>
      <li><a href="add_items.php">🍽 Add Items</a></li>
      <li><a href="logout.php" class="logout">🚪 Logout</a></li>
    </ul>
  </div>

  <div class="main">
    <div class="topbar">
      <span class="toggle-btn" onclick="toggleSidebar()">☰</span>
      <h3>Canteen Billing Dashboard</h3>
    </div>

    <div class="content">
      <h2>Welcome! <?php echo $Myname ?></h2>
      <p>This is your admin dashboard. Select a page from the sidebar to begin.</p>
      <div class="dashboard-cards">
  <div class="card">
    <h4>👥 Total Customers</h4>
    <p><?= $totalCustomers ?></p>
  </div>
  <div class="card">
    <h4>💸 Upcoming Credit Amount</h4>
    <p>₹ <?= number_format($totalCredit, 2) ?></p>
  </div>
  <div class="card">
    <h4>🍽 Total Items</h4>
    <p><?= $totalItems ?></p>
  </div>
  <div class="card">
    <h4>🧾 Total Orders</h4>
    <p><?= $totalOrders ?></p>
  </div>
</div>

    </div>
  </div>

  <script>
    function toggleSidebar() {
      document.getElementById('sidebar').classList.toggle('active');
    }
  </script>

</body>
</html>
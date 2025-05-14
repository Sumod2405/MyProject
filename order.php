<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login_page.php");
    exit();
}
$Myname = $_SESSION['username'];
include('db_con.php');

// Fetch data
$customer_result = $conn->query("SELECT id, customer_name FROM customers ORDER BY customer_name ASC");
$item_result = $conn->query("SELECT item_name FROM items_detail");

// Dashboard stats (optional use)
$totalCustomers = $conn->query("SELECT COUNT(*) as total FROM customers")->fetch_assoc()['total'];
$totalCredit = $conn->query("SELECT SUM(remaining_balance) as total FROM remaining_bill")->fetch_assoc()['total'] ?? 0;
$totalItems = $conn->query("SELECT COUNT(*) as total FROM items_detail")->fetch_assoc()['total'];
$totalOrders = $conn->query("SELECT COUNT(*) as total FROM transaction")->fetch_assoc()['total'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Create Order - Canteen Admin</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    body {
      font-family: 'Segoe UI', sans-serif;
      display: flex;
      background-color: #f4f6f8;
      min-height: 100vh;
    }
    .sidebar {
      width: 230px;
      background-color: #2c3e50;
      color: white;
      height: 100vh;
      padding: 20px;
      position: fixed;
      top: 0;
      left: 0;
      transition: all 0.3s;
    }
    .sidebar h2 {
      text-align: center;
      margin-bottom: 30px;
      font-size: 22px;
    }
    .sidebar ul {
      list-style: none;
    }
    .sidebar ul li {
      margin-bottom: 18px;
    }
    .sidebar ul li a {
      text-decoration: none;
      color: #ecf0f1;
      font-size: 16px;
      display: block;
      padding: 10px;
      border-radius: 4px;
      transition: 0.3s;
    }
    .sidebar ul li a:hover {
      background-color: #34495e;
    }
    .logout {
      color: #e74c3c !important;
    }

    .main {
      margin-left: 230px;
      flex: 1;
      display: flex;
      flex-direction: column;
    }

    .topbar {
      background-color: #fff;
      padding: 16px 20px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
      display: flex;
      align-items: center;
    }

    .topbar .toggle-btn {
      font-size: 20px;
      cursor: pointer;
      margin-right: 15px;
    }

    .content {
      padding: 30px 40px;
      flex: 1;
      background-color: #f4f6f8;
    }

    .container {
      background-color: #fff;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
      max-width: 750px;
      margin: 0 auto;
    }

    h3 {
      font-size: 24px;
      margin-bottom: 20px;
      color: #2c3e50;
    }

    form label {
      display: block;
      margin-top: 15px;
      font-weight: bold;
    }

    input[type="datetime-local"],
    select {
      width: 100%;
      padding: 10px;
      margin-top: 6px;
      border: 1px solid #ccc;
      border-radius: 6px;
    }

    .item-row {
      display: flex;
      gap: 10px;
      margin-top: 10px;
      align-items: center;
    }

    .item-row select {
      flex: 1;
    }

    .item-row button {
      padding: 6px 10px;
      background-color: #e74c3c;
      color: #fff;
      border: none;
      border-radius: 4px;
      cursor: pointer;
    }

    .actions {
      margin-top: 20px;
    }

    .actions button {
      background-color: #2980b9;
      color: white;
      padding: 12px 18px;
      border: none;
      border-radius: 6px;
      font-size: 16px;
      cursor: pointer;
      transition: background-color 0.3s;
    }

    .actions button:hover {
      background-color: #1f618d;
    }

    @media (max-width: 768px) {
      .sidebar {
        left: -230px;
        position: absolute;
      }

      .sidebar.active {
        left: 0;
      }

      .main {
        margin-left: 0;
      }
    }
  </style>
</head>
<body>
  <div class="sidebar" id="sidebar">
    <h2>Bhoomi Canteen Kondigre</h2>
    <ul>
      <li><a href="index.php">🏠 Home</a></li>
      <li><a href="paying_bill.php">💰 Payments</a></li>
      <li><a href="view_page.php">📄 View Bills</a></li>
      <li><a href="add_customer.php">👥 Customers</a></li>
      <li><a href="Over_time.php">⏱ OT Emp</a></li>
      <li><a href="add_items.php">🍽 Add Items</a></li>
      <li><a href="logout.php" class="logout">🚪 Logout</a></li>
    </ul>
  </div>

  <div class="main">
    <div class="topbar">
      <span class="toggle-btn" onclick="toggleSidebar()"></span>
      <h3>Create New Order</h3>
    </div>

    <div class="content">
      <div class="container">
        <form action="insert_order.php" method="POST">
          <label>Date & Time:</label>
          <input type="datetime-local" name="date_time" value="<?= date('Y-m-d\TH:i') ?>" required>

          <label>Customer Name:</label>
          <select name="customer_id" required>
            <option value="">-- Select Customer --</option>
            <?php while ($row = $customer_result->fetch_assoc()) { ?>
              <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['customer_name']) ?></option>
              <?php } ?>
                      </select>

          <label>Items:</label>
          <div id="items-container">
            <div class="item-row">
              <select name="item_name[]" required>
                <option value="">-- Select Item --</option>
                <?php $item_result->data_seek(0); while ($row = $item_result->fetch_assoc()) { ?>
                  <option value="<?= htmlspecialchars($row['item_name']) ?>"><?= htmlspecialchars($row['item_name']) ?></option>
                <?php } ?>
              </select>
              <select name="quantity[]" required>
                <?php for ($i = 1; $i <= 10; $i++) echo "<option value='$i'>$i</option>"; ?>
              </select>
              <button type="button" onclick="removeItem(this)">×</button>
            </div>
          </div>

          <div class="actions">
            <button type="button" onclick="addItem()">+ Add Item</button>
          </div>

          <label>Payment Method:</label>
          <select name="payment_method" id="paymentMethod" required>
            <option value="">-- Select Payment Method --</option>
            <option value="Cash">Cash</option>
            <option value="Online">Online</option>
            <option value="Credit">Credit</option>
          </select>

          <div class="actions">
            <button type="button" onclick="submitForm()">Submit Order</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script>
    function toggleSidebar() {
      document.getElementById('sidebar').classList.toggle('active');
    }

    function addItem() {
      const container = document.getElementById('items-container');
      const row = document.createElement('div');
      row.className = 'item-row';

      row.innerHTML = `
        <select name="item_name[]" required>
          <option value="">-- Select Item --</option>
          <?php $item_result->data_seek(0); while ($row = $item_result->fetch_assoc()) { ?>
            <option value="<?= htmlspecialchars($row['item_name']) ?>"><?= htmlspecialchars($row['item_name']) ?></option>
          <?php } ?>
        </select>
        <select name="quantity[]" required>
          <?php for ($i = 1; $i <= 10; $i++) echo "<option value='$i'>$i</option>"; ?>
        </select>
        <button type="button" onclick="removeItem(this)">×</button>
      `;
      container.appendChild(row);
    }

    function removeItem(btn) {
      const rows = document.querySelectorAll('.item-row');
      if (rows.length > 1) {
        btn.parentElement.remove();
      } else {
        alert("At least one item is required.");
      }
    }

    function submitForm() {
      const method = document.getElementById("paymentMethod").value;
      const form = document.querySelector("form");

      if (!method) {
        alert("Please select a payment method.");
        return;
      }

      const input = document.createElement("input");
      input.type = "hidden";
      input.name = method === "Credit" ? "credit" : "print";
      form.appendChild(input);
      form.submit();
    }
  </script>
<?php $conn->close(); ?>
</body>
</html>

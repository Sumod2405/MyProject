<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login_page.php");
    exit();
}
include('db_con.php');

// Fetch customers
$customer_query = "SELECT id, customer_name FROM customers ORDER BY customer_name ASC";
$customer_result = $conn->query($customer_query);

// Fetch items
$item_query = "SELECT item_name FROM items_detail";
$item_result = $conn->query($item_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Create Order</title>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
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
    .sidebar ul { list-style: none; }
    .sidebar ul li { margin: 15px 0; }
    .sidebar ul li a {
      color: white;
      text-decoration: none;
      display: block;
      padding: 10px;
      border-radius: 6px;
      transition: background 0.3s;
    }
    .sidebar ul li a:hover { background-color: #34495e; }
    .logout {
      background-color: #ff4d4d;
      color: white !important;
    }
    .logout:hover { background-color: #e60000 !important; }
    .main {
      margin-left: 250px;
      padding: 20px;
      flex-grow: 1;
      width: 100%;
      transition: margin-left 0.3s ease;
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
      .sidebar.active { transform: translateX(0); }
      .main { margin-left: 0; }
      .toggle-btn { display: block; }
    }

    /* Order Form Styling */
    .container {
        max-width: 900px;
        margin: 30px auto;
        padding: 30px 40px;
        background: #ffffff;
        border-radius: 15px;
        box-shadow: 0 12px 25px rgba(0, 0, 0, 0.1);
    }
    h2 {
        color: #333;
        margin-bottom: 25px;
        border-bottom: 2px solid #4a90e2;
        padding-bottom: 10px;
    }
    label {
        display: block;
        margin-top: 15px;
        margin-bottom: 5px;
        font-weight: bold;
        color: #444;
    }
    input[type="datetime-local"],
    select {
        width: 100%;
        padding: 12px;
        border-radius: 8px;
        border: 1px solid #ccc;
        font-size: 16px;
        background-color: #f9f9f9;
        margin-bottom: 15px;
    }
    .item-row {
        display: flex;
        gap: 15px;
        margin-bottom: 15px;
    }
    .item-row select {
        flex: 1;
    }
    .item-row button {
        background-color: #e74c3c;
        color: white;
        border: none;
        padding: 10px 14px;
        font-size: 18px;
        border-radius: 6px;
        cursor: pointer;
    }
    .item-row button:hover {
        background-color: #c0392b;
    }
    .actions {
        text-align: center;
        margin-top: 20px;
    }
    .actions button {
        background-color: #4a90e2;
        color: white;
        border: none;
        padding: 12px 24px;
        font-size: 16px;
        border-radius: 8px;
        cursor: pointer;
        margin: 10px;
    }
    .actions button:hover {
        background-color: #357abd;
    }
    @media (max-width: 600px) {
        .container { padding: 20px; }
        .item-row { flex-direction: column; }
    }
  </style>
</head>
<body>

<!-- Sidebar -->
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

<!-- Main Content -->
<div class="main">
  <div class="topbar">
    <span class="toggle-btn" onclick="toggleSidebar()">☰</span>
    <h3>Create New Order</h3>
  </div>

  <div class="container">
    <form action="insert_order.php" method="POST">
        <label>Date & Time:</label>
        <input type="datetime-local" name="date_time" value="<?php echo date('Y-m-d\TH:i'); ?>" required>

        <label>Customer Name:</label>
        <select name="customer_id" required>
            <option value="">-- Select Customer --</option>
            <?php while ($row = $customer_result->fetch_assoc()) { ?>
                <option value="<?= $row['id'] ?>"><?= $row['customer_name'] ?></option>
            <?php } ?>
        </select>

        <label>Items:</label>
        <div id="items-container">
            <div class="item-row">
                <select name="item_name[]" required>
                    <option value="">-- Select Item --</option>
                    <?php $item_result->data_seek(0); while ($row = $item_result->fetch_assoc()) { ?>
                        <option value="<?= $row['item_name'] ?>"><?= $row['item_name'] ?></option>
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
            <?php
            $item_result->data_seek(0);
            while ($row = $item_result->fetch_assoc()) {
                echo "<option value='{$row['item_name']}'>{$row['item_name']}</option>";
            }
            ?>
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

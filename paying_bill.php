<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login_page.php"); // Redirect to login if not logged in
    exit();
}

include('navbar.php');
include('db_con.php');

// Handle Payment Submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['customer_name']) && isset($_POST['paid_amount'])) {
    $customer_name = $_POST['customer_name']; // Selected customer from dropdown
    $paid_amount = $_POST['paid_amount'];

    if (!empty($customer_name) && !empty($paid_amount)) {
        // Insert payment into the paid_bill table with the current date
        $stmt = $conn->prepare("INSERT INTO paid_bill (customer_name, paid_amount, payment_date) VALUES (?, ?, NOW())");
        $stmt->bind_param("sd", $customer_name, $paid_amount);

        if ($stmt->execute()) {
            echo "<script>alert('Payment added successfully!');</script>";
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    }
}

// Fetch Customers for Dropdown
$customers = $conn->query("SELECT customer_name FROM customers");

 ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Billing System</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f7faff;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 900px;
            margin: 100px auto 40px;
            padding: 30px 40px;
            background: #ffffff;
            border-radius: 15px;
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #333;
            margin-bottom: 20px;
            border-bottom: 2px solid #4a90e2;
            padding-bottom: 10px;
        }

        form {
            margin-bottom: 40px;
        }

        label {
            display: block;
            margin: 15px 0 5px;
            font-weight: bold;
            color: #444;
        }

        select, input[type="number"] {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 16px;
            background-color: #f9f9f9;
        }

        button {
            margin-top: 20px;
            padding: 12px 25px;
            background-color: #4a90e2;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #357abd;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 15px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 14px;
            text-align: center;
        }

        th {
            background-color: #4a90e2;
            color: #fff;
        }

        tr:nth-child(even) {
            background-color: #f2f6ff;
        }

        @media (max-width: 600px) {
            .container {
                padding: 20px;
            }

            h2 {
                font-size: 20px;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Enter Customer Payment</h2>
    <form method="post">
        <label for="customer_name">Select Customer:</label>
        <select name="customer_name" required>
            <option value="">-- Select Customer --</option>
            <?php 
            if ($customers->num_rows > 0) {
                while ($row = $customers->fetch_assoc()) { 
                    echo "<option value='".$row['customer_name']."'>".$row['customer_name']."</option>";
                }
            } else {
                echo "<option disabled>No customers found</option>";
            }
            ?>
        </select>

        <label for="paid_amount">Enter Paid Amount:</label>
        <input type="number" name="paid_amount" required step="0.01">

        <button type="submit">Submit Payment</button>
    </form>

    <h2>Paid Bill</h2>
    <table>
        <tr>
            <th>Customer Name</th>
            <th>Total Bill</th>
            <th>Total Paid</th>
            <th>Remaining Balance</th>
        </tr>
        <?php 
        $sql = "SELECT * FROM remaining_bill";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['name']}</td>
                        <td>{$row['total_bill']}</td>
                        <td>{$row['total_paid']}</td>
                        <td>{$row['remaining_balance']}</td>
                    </tr>";
            }
        }
        ?>
    </table>
</div>

<div class="container">
<?php
include('db_con.php');
$result = $conn->query("SELECT customer_name, paid_amount, payment_date FROM paid_bill ORDER BY payment_date DESC");
?>

<h2>💰 Payment History</h2>

<table style="width: 100%; border-collapse: collapse; background: #fff; box-shadow: 0 4px 10px rgba(0,0,0,0.05); border-radius: 10px; overflow: hidden; margin-top: 20px;">
  <thead style="background-color: #1e3d59; color: white;">
    <tr>
      <th style="padding: 12px;">Customer Name</th>
      <th style="padding: 12px;">Paid Amount</th>
      <th style="padding: 12px;">Payment Date</th>
    </tr>
  </thead>
  <tbody>
    <?php while ($row = $result->fetch_assoc()): ?>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px;"><?= htmlspecialchars($row['customer_name']) ?></td>
        <td style="padding: 12px;">₹<?= number_format($row['paid_amount'], 2) ?></td>
        <td style="padding: 12px;"><?= date("d M Y, h:i A", strtotime($row['payment_date'])) ?></td>
      </tr>
    <?php endwhile; ?>
  </tbody>
</table>

</div>

<?php $conn->close(); ?>


</body>
</html>
 
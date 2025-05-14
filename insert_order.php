<?php
session_start();
include('db_con.php');
if (!isset($_SESSION['username'])) {
    header("Location: login_page.php");
    exit();
}

// Handle credit transaction
if (isset($_POST['credit']) && $_SERVER["REQUEST_METHOD"] == "POST") {
    $customer_id = $_POST['customer_id'] ?? '';
    $item_names = $_POST['item_name'] ?? [];
    $quantities = $_POST['quantity'] ?? [];

    if (empty($customer_id) || empty($item_names) || empty($quantities)) {
        echo "<script>alert('Please select a customer and at least one item.');</script>";
    } else {
        $stmt = $conn->prepare("INSERT INTO `transaction` (`name`, `item_name`, `quantiy`) 
                                VALUES ((SELECT customer_name FROM customers WHERE id = ?), ?, ?)");
        $success = true;
        for ($i = 0; $i < count($item_names); $i++) {
            $item_name = $item_names[$i];
            $quantity = (int)$quantities[$i];
            $stmt->bind_param("ssi", $customer_id, $item_name, $quantity);
            if (!$stmt->execute()) {
                $success = false;
                break;
            }
        }
        $stmt->close();

        if ($success) {
            echo "<script>alert('Transaction added successfully!'); window.location.href='order.php';</script>";
        } else {
            echo "<script>alert('Error: Could not add transaction!');</script>";
        }
    }
}

if (isset($_POST['print'])) {
    $date_time = $_POST['date_time'];
    $item_names = $_POST['item_name'];
    $quantities = $_POST['quantity'];

    include('db_con.php');

    $total_amount = 0;
    $items_details = [];

    foreach ($item_names as $index => $item_name) {
        $quantity = $quantities[$index];
        $item_result = $conn->query("SELECT item_price FROM items_detail WHERE item_name = '$item_name'");
        $item = $item_result->fetch_assoc();
        $price = $item['item_price'];
        $amount = $price * $quantity;
        $total_amount += $amount;

        $items_details[] = [
            'name' => $item_name,
            'quantity' => $quantity,
            'price' => $price,
            'amount' => $amount
        ];
    }

    $conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Print Bill - Bhoomi Canteen</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f0f2f5;
        }
        .container-box {
            max-width: 850px;
            background: white;
            margin: 30px auto;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        h2 {
            color: #1e3d59;
            text-align: center;
            margin-bottom: 10px;
        }
        .header-info {
            text-align: center;
            font-size: 14px;
            color: #555;
            margin-bottom: 20px;
        }
        .table th {
            background-color: #edf4ff;
            color: #1e3d59;
        }
        .total-row td {
            font-weight: bold;
            background-color: #f9f9f9;
            color: #1e3d59;
        }
        .thank-you {
            text-align: center;
            font-size: 15px;
            margin-top: 30px;
            color: #333;
        }
        .footer {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
            font-size: 13px;
            color: #555;
        }
        .print-btn, .back-btn {
            display: block;
            margin: 20px auto 0;
            padding: 10px 25px;
            background-color: #1e3d59;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 15px;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
        }
        .print-btn:hover, .back-btn:hover {
            background-color: #142f44;
        }
        @media print {
            body * {
                visibility: hidden;
            }
            .container-box, .container-box * {
                visibility: visible;
            }
            .container-box {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                margin: 0;
                padding: 0;
                box-shadow: none;
                border: none;
            }
            .print-btn, .back-btn {
                display: none;
            }
        }
    </style>
</head>
<body>
<div class="container container-box">
    <h2>Bhoomi Canteen Kondigre</h2>
    <div class="header-info">
        <!-- <p>Proprietor: Sumod Metre | Contact: 9022906961</p> -->
        <p>Owned & Managed by : Sumod Metre | Contact: 9022906961</p>
        <p><strong>Date & Time:</strong> <?php echo htmlspecialchars($date_time); ?></p>
    </div>
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>S. No</th>
            <th>Item</th>
            <th>Rate (₹)</th>
            <th>Qty</th>
            <th>Total (₹)</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($items_details as $index => $item): ?>
            <tr>
                <td><?php echo $index + 1; ?></td>
                <td><?php echo htmlspecialchars($item['name']); ?></td>
                <td><?php echo number_format($item['price'], 2); ?></td>
                <td><?php echo $item['quantity']; ?></td>
                <td><?php echo number_format($item['amount'], 2); ?></td>
            </tr>
        <?php endforeach; ?>
        <tr class="total-row">
            <td colspan="4">Grand Total</td>
            <td>₹<?php echo number_format($total_amount, 2); ?></td>
        </tr>
        </tbody>
    </table>
    <div class="thank-you">
        <p>🙏 Thank you for visiting Bhoomi Canteen!</p>
        <p>Please visit again.</p>
    </div>
    <div class="footer">
        <div>Customer Signature</div>
        <div>Checked By</div>
        <div>Manager</div>
    </div>
    <button class="print-btn" onclick="printBill()">🖨️ Print Bill</button>
    <a class="back-btn" href="index.php">← Back to Home</a>
</div>
<script>
    function printBill() {
        window.print();
    }
</script>
</body>
</html>
<?php } ?>

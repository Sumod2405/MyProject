<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login_page.php");
    exit();
}
include('navbar.php');
include('db_con.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction Details</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f7faff;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 1100px;
            margin: 100px auto 40px;
            padding: 30px 40px;
            background: #ffffff;
            border-radius: 15px;
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.1);
        }

        h2, h3 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        form {
            margin-bottom: 30px;
            text-align: center;
        }

        select, button {
            padding: 10px 15px;
            margin: 10px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 16px;
            width: 220px;
        }

        button {
            background-color: #4a90e2;
            color: #fff;
            border: none;
            transition: background-color 0.3s ease;
            cursor: pointer;
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
            padding: 12px;
            text-align: center;
        }

        th {
            background-color: #4a90e2;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f2f6ff;
        }

        a {
            display: inline-block;
            text-align: center;
            text-decoration: none;
            color: #4a90e2;
            margin-top: 30px;
            font-size: 16px;
        }

        @media (max-width: 768px) {
            .container {
                padding: 20px;
            }

            table, th, td {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Total Amount of All Customers</h2>
    <table>
        <tr>
            <th>Customer Name</th>
            <th>Total Bill</th>
        </tr>
        <?php
        $sql = "SELECT * FROM total_bill_view";
        $result = $conn->query($sql);
        while ($row = $result->fetch_assoc()) {
            echo "<tr><td>{$row['name']}</td><td>{$row['total_bill']}</td></tr>";
        }
        ?>
    </table>

    <h2>Search Particular Customer Total Amount</h2>
    <form method="POST">
        <select name="customer_name">
            <option value="">Select Customer</option>
            <?php
            $sql = "SELECT DISTINCT customer_name FROM customers";
            $result = $conn->query($sql);
            while ($row = $result->fetch_assoc()) {
                echo "<option value='{$row['customer_name']}'>{$row['customer_name']}</option>";
            }
            ?>
        </select>
        <button type="submit" name="search_total">Search</button>
    </form>

    <?php
    if (isset($_POST['search_total']) && !empty($_POST['customer_name'])) {
        $customer_name = $_POST['customer_name'];
        $sql = "SELECT * FROM total_bill_view WHERE name = '$customer_name'";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            echo "<h3>Total Amount for $customer_name</h3>";
            echo "<table><tr><th>Customer Name</th><th>Total Bill</th></tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr><td>{$row['name']}</td><td>{$row['total_bill']}</td></tr>";
            }
            echo "</table>";
        } else {
            echo "<p style='text-align:center;'>No records found for $customer_name.</p>";
        }
    }
    ?>

    <h2>Search Particular Customer Transactions</h2>
    <form method="POST">
        <select name="customer_transactions">
            <option value="">Select Customer</option>
            <?php
            $sql = "SELECT DISTINCT customer_name FROM customers";
            $result = $conn->query($sql);
            while ($row = $result->fetch_assoc()) {
                echo "<option value='{$row['customer_name']}'>{$row['customer_name']}</option>";
            }
            ?>
        </select>
        <button type="submit" name="search_transactions">Search</button>
    </form>

    <?php
    if (isset($_POST['search_transactions']) && !empty($_POST['customer_transactions'])) {
        $customer_name = $_POST['customer_transactions'];
        $sql = "SELECT * FROM all_transaction_detail WHERE customer_name  = '$customer_name'";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            echo "<h3>Transactions for $customer_name</h3>";
            echo "<table>
                <tr>
                    <th>Date Time</th>
                    <th>Item Name</th>
                    <th>Quantity</th>
                    <th>Item Price</th>
                    <th>Total Price</th>
                </tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                    <td>{$row['date_time']}</td>
                    <td>{$row['item_names']}</td>
                    <td>{$row['quantities']}</td>
                    <td>{$row['item_prices']}</td>
                    <td>{$row['total_price']}</td>
                </tr>";
            }
            echo "</table>";
        } else {
            echo "<p style='text-align:center;'>No transactions found for $customer_name.</p>";
        }
    }
    ?>

    <h2>All Transactions</h2>
    <table>
        <tr>
            <th>Date</th>
            <th>Customer Name</th>
            <th>Item Name</th>
            <th>Quantity</th>
            <th>Item Price</th>
            <th>Total Price</th>
        </tr>
        <?php
        $sql = "SELECT * FROM all_transaction_detail ORDER BY date_time DESC";
        $result = $conn->query($sql);
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                <td>{$row['date_time']}</td>
                <td>{$row['customer_name']}</td>
                <td>{$row['item_names']}</td>
                <td>{$row['quantities']}</td>
                <td>{$row['item_prices']}</td>
                <td>{$row['total_price']}</td>
            </tr>";
        }
        ?>
    </table>

    <div style="text-align:center;">
        <a href="index.php">Back to Home</a>
    </div>
</div>

</body>
</html>

<?php
$conn->close();
?>

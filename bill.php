<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login_page.php"); // Redirect to login if not logged in
    exit();
}
include('navbar.php'); 
include('db_con.php'); 

// Fetch Customers
$customer_query = "SELECT id, customer_name FROM customers ORDER BY customer_name ASC ";
$customer_result = $conn->query($customer_query);

// Fetch Items
$item_query = "SELECT item_name FROM items_detail";
$item_result = $conn->query($item_query);

// Handle Order Submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $customer_id = $_POST['customer_id'];
    $date_time = $_POST['date_time'];
    $items = $_POST['item_name'];
    $quantities = $_POST['quantity'];
    $total_amount = 0;

    foreach ($items as $index => $item_name) {
        $quantity = $quantities[$index];

        // Fetch item price
        $price_query = "SELECT item_price FROM items_detail WHERE item_name = '$item_name'";
        $price_result = $conn->query($price_query);
        $price_row = $price_result->fetch_assoc();
        $item_price = $price_row['item_price'];

        $total_price = $quantity * $item_price;
        $total_amount += $total_price;

        // Insert order
        $insert_order = "INSERT INTO customer_orders (customer_id, item_name, quantity, total_price, date_time) 
                         VALUES ('$customer_id', '$item_name', '$quantity', '$total_price', '$date_time')";
        $conn->query($insert_order);
    }

    // Fetch Customer Name
    $customer_query = "SELECT customer_name FROM customers WHERE id = '$customer_id'";
    $customer_result = $conn->query($customer_query);
    $customer_row = $customer_result->fetch_assoc();
    $customer_name = $customer_row['customer_name'];

    // Fetch Order Details
    $order_query = "SELECT item_name, quantity, total_price FROM customer_orders WHERE customer_id = '$customer_id' AND date_time = '$date_time'";
    $order_result = $conn->query($order_query);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Form</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 50px;
        }
        form {
            width: 400px;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
            box-shadow: 2px 2px 10px rgba(0,0,0,0.1);
        }
        label {
            display: block;
            margin-top: 10px;
        }
        select, input {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
        }
        button {
            margin-top: 15px;
            padding: 10px;
            background: blue;
            color: white;
            border: none;
            cursor: pointer;
        }
        #invoice {
            display: none;
            margin-top: 20px;
            padding: 20px;
            border: 1px solid #000;
            width: 400px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table, th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }
    </style>
</head>
<body>
    
    <h2>Order Form</h2>
    <form method="POST">
        <input style="width: 95%;" type="datetime-local" name="date_time" value="<?php echo date('Y-m-d\TH:i'); ?>" required>
        <label>Customer Name:</label>
        <select name="customer_id" required>
            <option value="">Select Customer</option>
            <?php while ($row = $customer_result->fetch_assoc()) { ?>
                <option value="<?php echo $row['id']; ?>"><?php echo $row['customer_name']; ?></option>
            <?php } ?>
        </select>

        <label>Items:</label>
        <div id="items-container">
            <div class="item-row">
                <select name="item_name[]" required>
                    <option value="">-- Select Item --</option>
                    <?php 
                    $item_result->data_seek(0);
                    while ($row = $item_result->fetch_assoc()) { ?>
                        <option value="<?php echo $row['item_name']; ?>"><?php echo $row['item_name']; ?></option>
                    <?php } ?>
                </select>
                <select name="quantity[]" required>
                    <?php for ($i = 1; $i <= 10; $i++) {
                        echo "<option value='$i'>$i</option>";
                    } ?>
                </select>
            </div>
        </div>
        
        <button type="button" onclick="removeItem(this)">Remove</button>
        <button type="button" onclick="addItem()">Add Item</button>
        <button type="submit">Place Order</button>
    </form>

    <h2><a href="index.php">Home</a></h2> 

    <?php if (isset($order_result)) { ?>
    <div id="invoice">
        <h2>Invoice</h2>
        <p><strong>Customer Name:</strong> <?php echo $customer_name; ?></p>
        <p><strong>Date & Time:</strong> <?php echo $date_time; ?></p>

        <table>
            <tr>
                <th>Item Name</th>
                <th>Quantity</th>
                <th>Total Price</th>
            </tr>
            <?php while ($row = $order_result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $row['item_name']; ?></td>
                <td><?php echo $row['quantity']; ?></td>
                <td>₹<?php echo number_format($row['total_price'], 2); ?></td>
            </tr>
            <?php } ?>
        </table>

        <h3>Total Amount: ₹<?php echo number_format($total_amount, 2); ?></h3>
        <button onclick="printInvoice()">Print Bill</button>
    </div>

    <script>
        document.getElementById("invoice").style.display = "block"; // Show invoice after order

        function addItem() {
            let container = document.getElementById("items-container");
            let newRow = document.createElement("div");
            newRow.classList.add("item-row");
            newRow.innerHTML = container.firstElementChild.innerHTML;
            container.appendChild(newRow);
        }

        function removeItem(button) {
            if (document.querySelectorAll(".item-row").length > 1) {
                button.parentElement.remove();
            }
        }

        function printInvoice() {
            let printContent = document.getElementById("invoice").innerHTML;
            let originalContent = document.body.innerHTML;

            document.body.innerHTML = "<html><head><title>Print Invoice</title></head><body>" + printContent + "</body></html>";
            window.print();
            document.body.innerHTML = originalContent;
        }
    </script>
    <?php } ?>

</body>
</html>

<?php $conn->close(); ?>

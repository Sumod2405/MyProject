<?php
include('db_con.php'); 

// Fetch Customers
$customer_query = "SELECT id, customer_name FROM customers ORDER BY customer_name ASC ";
$customer_result = $conn->query($customer_query);

// Fetch Items
$item_query = "SELECT item_name FROM items_detail";
$item_result = $conn->query($item_query);
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
            background: #f7f7f7;
            padding: 40px;
        }
        h2 {
            color: #333;
        }
        nav {
            background: #333;
            color: white;
            padding: 10px 20px;
            text-align: center;
        }
        nav a {
            color: white;
            margin: 0 15px;
            text-decoration: none;
            font-size: 18px;
        }
        nav a:hover {
            text-decoration: underline;
        }
        form {
            background: #fff;
            width: 450px;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 2px 2px 15px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-top: 15px;
            color: #555;
        }
        select, input[type="datetime-local"] {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .item-row {
            margin-bottom: 10px;
            display: flex;
            gap: 10px;
        }
        .item-row select {
            flex: 1;
        }
        .item-row button {
            background-color: crimson;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 5px 10px;
            cursor: pointer;
        }
        .actions {
            margin-top: 20px;
        }
        .actions button, .actions input[type="submit"] {
            padding: 10px 15px;
            margin-right: 10px;
            border: none;
            border-radius: 5px;
            background-color: #007bff;
            color: white;
            cursor: pointer;
        }
        .actions button:hover, .actions input[type="submit"]:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

    <nav>
        <a href="view_page.php">Show Orders</a>
        <a href="add_customer.php">Add Customer</a>
        <a href="add_items.php">Add Items</a>
        <a href="Over_Time.php">Over Time</a>
        <!-- <a href="bill.php">Print Bill</a> -->
    </nav>

    <h2>Order Form</h2>
    <form action="insert_order.php" method="POST">
        <label>Date & Time:</label>
        <input type="datetime-local" name="date_time" value="<?php echo date('Y-m-d\TH:i'); ?>" required>

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
                    $item_result->data_seek(0); // Reset result set
                    while ($row = $item_result->fetch_assoc()) { ?>
                        <option value="<?php echo $row['item_name']; ?>"><?php echo $row['item_name']; ?></option>
                    <?php } ?>
                </select>
                <select name="quantity[]" required>
                    <?php for ($i = 1; $i <= 10; $i++) {
                        echo "<option value='$i'>$i</option>";
                    } ?>
                </select>
                <button type="button" onclick="removeItem(this)">Remove</button>
            </div>
        </div>
                        
        <div class="actions">
            <button type="button" onclick="addItem()">Add Item</button>
            <button type="submit" name="credit">Credit</button>
            <label>Payment Method:</label>
<select name="payment_method" required>
  <option value="Cash">Cash</option>
  <option value="Online">Online</option>
</select>

            <input type="submit" name="print" value="Print Bill">
        </div>
    </form>

    <script>
        function addItem() {
            let container = document.getElementById("items-container");
            let newRow = document.createElement("div");
            newRow.classList.add("item-row");
            newRow.innerHTML = `
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
                <button type="button" onclick="removeItem(this)">Remove</button>
            `;
            container.appendChild(newRow);
        }

        function removeItem(button) {
            if (document.querySelectorAll(".item-row").length > 1) {
                button.parentElement.remove();
            } else {
                alert("At least one item must be selected.");
            }
        }
    </script>

</body>
</html>

<?php $conn->close(); ?>  
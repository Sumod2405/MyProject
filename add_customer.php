<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login_page.php");
    exit();
}
include('navbar.php');
include('db_con.php');

// Add Customer
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_customer'])) {
    $name = $_POST['customer_name'];
    $address = $_POST['address'];
    $phone_number = $_POST['phone_number'];
    $department = $_POST['department'];

    $sql = "INSERT INTO customers (customer_name, address, phone_number, department)
            VALUES ('$name', '$address', '$phone_number', '$department')";
    $conn->query($sql);
    
    header("Location: add_customer.php");
    exit();
}

// Update Customer
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_customer'])) {
    $id = $_POST['id'];
    $name = $_POST['customer_name'];
    $address = $_POST['address'];
    $phone_number = $_POST['phone_number'];
    $department = $_POST['department'];

    $sql = "UPDATE customers SET customer_name='$name', address='$address', phone_number='$phone_number', department='$department' WHERE id=$id";
    $conn->query($sql);
    header("Location: add_customer.php");
    exit();
}

// Delete Customer
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM customers WHERE id=$id");
    header("Location: add_customer.php");
    exit();
}

// Fetch all customers
// $result = $conn->query("SELECT * FROM customers");
// Default sort: newest first
$order = isset($_GET['order']) && $_GET['order'] === 'asc' ? 'ASC' : 'DESC';
$result = $conn->query("SELECT * FROM customers ORDER BY id $order");


// For edit form
$edit_customer = null;
if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    $edit_result = $conn->query("SELECT * FROM customers WHERE id=$edit_id");
    $edit_customer = $edit_result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customer Management</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(to right, #f8f9fa, #e3f2fd);
            margin: 0;
            padding: 0;
        }
        .page-wrapper {
            padding: 80px 20px 40px;
            max-width: 1200px;
            margin: auto;
        }
        .form-container, .table-container {
            background: #fff;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }
        h2 {
            margin-bottom: 20px;
            font-size: 24px;
            color: #333;
            border-bottom: 2px solid #4a90e2;
            display: inline-block;
        }
        input[type="text"], select {
            width: 100%;
            padding: 12px;
            margin-bottom: 18px;
            border: 1px solid #ccc;
            border-radius: 8px;
            background: #f9f9f9;
            font-size: 15px;
        }
        button {
            padding: 10px 24px;
            background: #4a90e2;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            cursor: pointer;
        }
        button:hover {
            background: #357ae8;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 15px;
        }
        th, td {
            padding: 12px 10px;
            border-bottom: 1px solid #ddd;
            text-align: center;
        }
        th {
            background: #4a90e2;
            color: white;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .action-buttons a {
            margin: 0 5px;
            text-decoration: none;
            color: #4a90e2;
            font-weight: bold;
        }
        @media (max-width: 768px) {
            .form-container, .table-container {
                padding: 20px;
            }
            h2 {
                font-size: 20px;
            }
        }
    </style>
</head>
<body>

<div class="page-wrapper">

    <div class="form-container">
        <h2><?= $edit_customer ? 'Edit Customer' : 'Add Customer' ?></h2>
        <form method="POST">
            <?php if ($edit_customer): ?>
                <input type="hidden" name="id" value="<?= $edit_customer['id'] ?>">
            <?php endif; ?>
            <input type="text" name="customer_name" placeholder="Customer Name" value="<?= htmlspecialchars($edit_customer['customer_name'] ?? '') ?>" required>
            <input type="text" name="address" placeholder="Address" value="<?= htmlspecialchars($edit_customer['address'] ?? '') ?>"  >
            <input type="text" name="phone_number" placeholder="Phone Number" value="<?= htmlspecialchars($edit_customer['phone_number'] ?? '') ?>"  >
            <select name="department" required>
                <option value="">Select Department</option>
                <?php
                $departments = ['Admin', 'Security', 'Sizing', 'Loom', 'Mending', 'Boiler', 'Gangajal/Adi/Co-workers', 'Guests','Staff' , 'Others','Maintainance','Coating'];
                foreach ($departments as $dep) {
                    $selected = ($edit_customer && $edit_customer['department'] === $dep) ? 'selected' : '';
                    echo "<option value=\"$dep\" $selected>$dep</option>";
                }
                ?>
            </select>
            <button type="submit" name="<?= $edit_customer ? 'update_customer' : 'add_customer' ?>">
                <?= $edit_customer ? 'Update' : 'Add' ?> Customer
            </button>
            <?php if ($edit_customer): ?>
                <a href="add_customer.php" style="margin-left:10px; color:red;">Cancel</a>
            <?php endif; ?>
        </form>
    </div>

</div>

    <div class="table-container">
        <h2>Customer List</h2>
        <table>
            <thead>
  
            <div style="margin-bottom: 20px; text-align: right;">
    <a href="?order=desc" style="text-decoration: none; padding: 8px 16px; background-color: #4a90e2; color: white; border-radius: 6px; margin-right: 10px; font-weight: 500; transition: background 0.3s;">
        🔽 Newest First
    </a>
    <a href="?order=asc" style="text-decoration: none; padding: 8px 16px; background-color: #6c757d; color: white; border-radius: 6px; font-weight: 500; transition: background 0.3s;">
        🔼 Oldest First
    </a>
</div>

  
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Address</th>
                <th>Phone</th>
                <th>Department</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['customer_name'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['address'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['phone_number'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['department'] ?? '') ?></td>
                    <td class="action-buttons">
                        <a href="?edit=<?= $row['id'] ?>">Edit</a> |
                        <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Delete this customer?')">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>

</div>

</body>
</html>

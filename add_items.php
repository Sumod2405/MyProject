<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login_page.php");
    exit();
}

include('navbar.php');
include('db_con.php');

// Handle Add
if (isset($_POST['add'])) {
    $item_name = $_POST['item_name'];
    $item_price = $_POST['item_price'];
    $conn->query("INSERT INTO items_detail (item_name, item_price) VALUES ('$item_name', '$item_price')");
    header("Location: add_items.php");
    exit();
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM items_detail WHERE id = $id");
    header("Location: add_items.php");
    exit();
}

// Handle Edit Request (load data)
$edit_mode = false;
$edit_id = $edit_name = $edit_price = '';
if (isset($_GET['edit'])) {
    $edit_mode = true;
    $edit_id = $_GET['edit'];
    $result = $conn->query("SELECT * FROM items_detail WHERE id = $edit_id");
    $row = $result->fetch_assoc();
    $edit_name = $row['item_name'];
    $edit_price = $row['item_price'];
}

// Handle Update
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $item_name = $_POST['item_name'];
    $item_price = $_POST['item_price'];
    $conn->query("UPDATE items_detail SET item_name='$item_name', item_price='$item_price' WHERE id=$id");
    header("Location: add_items.php");
    exit();
}

// Get All Items
$items = $conn->query("SELECT * FROM items_detail ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Items</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        /* ... same styles from your form page, plus table styles ... */

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(to right, #fffde7, #e1f5fe);
            margin: 0;
            padding: 0;
        }

        .page-wrapper {
            padding: 80px 20px 40px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .form-container {
            background-color: #ffffff;
            padding: 30px 40px;
            border-radius: 20px;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
            max-width: 500px;
            width: 100%;
            margin-bottom: 40px;
        }

        h2 {
            margin-bottom: 20px;
            color: #2c3e50;
            font-size: 26px;
            border-bottom: 2px solid #4a90e2;
            display: inline-block;
            padding-bottom: 5px;
        }

        input[type="text"],
        input[type="number"] {
            width: 100%;
            padding: 12px 14px;
            margin-bottom: 18px;
            border: 1px solid #ccc;
            border-radius: 10px;
            font-size: 15px;
            background-color: #f9f9f9;
        }

        button {
            padding: 10px 25px;
            background: linear-gradient(to right, #4caf50, #66bb6a);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            cursor: pointer;
        }

        button:hover {
            background: linear-gradient(to right, #43a047, #388e3c);
        }

        table {
            border-collapse: collapse;
            width: 90%;
            background-color: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        th, td {
            padding: 12px 15px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #4a90e2;
            color: white;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .action-btns a {
            margin: 0 5px;
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 14px;
        }

        .edit-btn { background-color: #f39c12; }
        .delete-btn { background-color: #e74c3c; }

        @media (max-width: 600px) {
            .form-container {
                padding: 20px;
            }

            table {
                font-size: 13px;
            }
        }
    </style>
</head>
<body>

<div class="page-wrapper">
    <div class="form-container">
        <h2><?= $edit_mode ? 'Edit Item' : 'Add Item' ?></h2>
        <form method="POST">
            <input type="hidden" name="id" value="<?= $edit_id ?>">
            <input type="text" name="item_name" placeholder="Enter item name" value="<?= htmlspecialchars($edit_name) ?>" required>
            <input type="number" name="item_price" placeholder="Enter item price" value="<?= htmlspecialchars($edit_price) ?>" required>
            <button type="submit" name="<?= $edit_mode ? 'update' : 'add' ?>">
                <?= $edit_mode ? 'Update Item' : 'Add Item' ?>
            </button>
        </form>
    </div>

    <h2 style="margin-bottom: 20px;">All Items</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Item Name</th>
                <th>Item Price (₹)</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($item = $items->fetch_assoc()) { ?>
                <tr>
                    <td><?= $item['id'] ?></td>
                    <td><?= htmlspecialchars($item['item_name']) ?></td>
                    <td><?= number_format($item['item_price'], 2) ?></td>
                    <td class="action-btns">
                        <a href="?edit=<?= $item['id'] ?>" class="edit-btn">Edit</a>
                        <a href="?delete=<?= $item['id'] ?>" class="delete-btn" onclick="return confirm('Are you sure to delete this item?')">Delete</a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

</body>
</html>

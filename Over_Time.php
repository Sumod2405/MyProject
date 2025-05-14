<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login_page.php");
    exit();
}
include('navbar.php');
include('db_con.php');

// Fetch customer list
$customer_query = "SELECT customer_name FROM customers ORDER BY customer_name ASC";
$customer_result = $conn->query($customer_query);

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $employee_name = $_POST['employee_name'];
    $shift = $_POST['shift'];
    $date = date('Y-m-d');

    $sql = "INSERT INTO Over_Time (Employee_Name, Shift, `Date`) VALUES ('$employee_name', '$shift', '$date')";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Record added successfully!'); window.location.href='Over_Time.php';</script>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Fetch existing records
$sql = "SELECT * FROM Over_Time ORDER BY `Date` DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Overtime Management</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f7faff;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 900px;
            margin: 100px auto 40px;
            padding: 30px 40px;
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.08);
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 25px;
        }

        form {
            margin-bottom: 40px;
        }

        label {
            display: block;
            margin-top: 15px;
            font-weight: 600;
        }

        select {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 16px;
        }

        button {
            margin-top: 20px;
            width: 100%;
            background-color: #28a745;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #218838;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
            font-size: 15px;
        }

        th, td {
            padding: 12px;
            text-align: center;
            border: 1px solid #ddd;
        }

        th {
            background-color: #007bff;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f2f6ff;
        }

        tr:hover {
            background-color: #eef4ff;
        }

        a {
            display: block;
            margin-top: 30px;
            text-align: center;
            color: #007bff;
            font-size: 16px;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
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
    <h2>Overtime Entry Form</h2>
    <form method="post">
        <label for="employee_name">Employee Name:</label>
        <select name="employee_name" id="employee_name" required>
            <option value="">Select Customer</option>
            <?php while ($row = $customer_result->fetch_assoc()) { ?>
                <option value="<?php echo $row['customer_name']; ?>"><?php echo $row['customer_name']; ?></option>
            <?php } ?>
        </select>

        <label for="shift">Shift:</label>
        <select name="shift" id="shift" required>
            <option value="">Select Shift</option>
            <option value="8-4">8-4</option>
            <option value="4-12">4-12</option>
            <option value="12-8">12-8</option>
        </select>

        <button type="submit">Submit</button>
    </form>

    <h2>Overtime Records</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Employee Name</th>
            <th>Shift</th>
            <th>Date</th>
        </tr>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['ID']}</td>
                        <td>{$row['Employee_Name']}</td>
                        <td>{$row['Shift']}</td>
                        <td>{$row['Date']}</td>
                    </tr>";
            }
        } else {
            echo "<tr><td colspan='4'>No records found.</td></tr>";
        }
        ?>
    </table>

    <a href="index.php">Back to Home</a>
</div>

</body>
</html>

<?php
$conn->close();
?>

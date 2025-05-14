<?php
$connection = new mysqli("localhost", "root", "", "mydb");
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

$search = $_GET['search'] ?? '';

$sql = "SELECT * FROM customers WHERE customer_name LIKE ? ORDER BY customer_name ASC";
$stmt = $connection->prepare($sql);
$search_param = "%$search%";
$stmt->bind_param("s", $search_param);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Customer Records</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f2f2f2;
      padding: 20px;
    }
    h2 {
      text-align: center;
    }
    input[type="text"] {
      width: 50%;
      padding: 10px;
      margin: 20px auto;
      display: block;
      border-radius: 5px;
      border: 1px solid #ccc;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      background: white;
      margin-top: 10px;
    }
    th, td {
      padding: 12px;
      border: 1px solid #ddd;
      text-align: left;
    }
    th {
      background-color: #007BFF;
      color: white;
    }
    tr:hover {
      background-color: #f1f1f1;
    }
  </style>
</head>
<body>

<h2>Customer List</h2>
<input type="text" id="search" placeholder="Search by customer name...">

<table>
  <thead>
    <tr>
      <th>ID</th>
      <th>Customer Name</th>
      <th>Address</th>
      <th>Phone Number</th>
      <th>Department</th>
    </tr>
  </thead>
  <tbody id="customerTable">
    <?php while ($row = $result->fetch_assoc()): ?>
      <tr>
        <td><?= htmlspecialchars($row['id']) ?></td>
        <td><?= htmlspecialchars($row['customer_name']) ?></td>
        <td><?= htmlspecialchars($row['address']) ?></td>
        <td><?= htmlspecialchars($row['phone_number']) ?></td>
        <td><?= htmlspecialchars($row['department']) ?></td>
      </tr>
    <?php endwhile; ?>
  </tbody>
</table>

<script>
document.getElementById("search").addEventListener("keyup", function() {
  const query = this.value;
  const xhr = new XMLHttpRequest();
  xhr.open("GET", "customers.php?search=" + encodeURIComponent(query), true);
  xhr.onload = function() {
    if (this.status === 200) {
      const parser = new DOMParser();
      const doc = parser.parseFromString(this.responseText, "text/html");
      const newRows = doc.getElementById("customerTable").innerHTML;
      document.getElementById("customerTable").innerHTML = newRows;
    }
  };
  xhr.send();
});
</script>

</body>
</html>

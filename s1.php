<?php
include('db_con.php');

$username = "Sumod";
$password = "1111";

// Hash the password before saving
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Insert into admins table
$query = "INSERT INTO admins (username, password) VALUES (?, ?)";
$stmt = $conn->prepare($query);
$stmt->bind_param("ss", $username, $hashedPassword);

if ($stmt->execute()) {
    echo "Admin inserted successfully.";
} else {
    echo "Error: " . $stmt->error;
}
?> 
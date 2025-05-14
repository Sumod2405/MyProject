<!--  
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
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
    </style>
</head>
<body>
<nav style="background-color:#333; padding:10px;">
  <a href="index.php" style="color:white; margin-right:15px;">Home</a>
  <a href="paying_bill.php" style="color:white; margin-right:15px;">Add Payment</a>
  <a href="view_page.php" style="color:white; margin-right:15px;">View Bills</a>
  <a href="add_customer.php" style="color:white;">Customers</a>
  <a href="Over_time.php" style="color:white;">OT Management</a> 
  <a href="add_items.php" style="color:white;">Add Items</a> 
  <a href="logout.php">Logout</a>

</nav>
</body>
</html>
 -->
 <!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Navbar</title>
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
    }

    nav {
      background-color: #1e3d59;
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      align-items: center;
      padding: 12px 20px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    nav a {
      color: #f0f0f0;
      margin: 8px 15px;
      text-decoration: none;
      font-size: 16px;
      padding: 8px 14px;
      border-radius: 6px;
      transition: background-color 0.3s, color 0.3s;
    }

    nav a:hover {
      background-color: #f0f0f0;
      color: #1e3d59;
    }

    nav a:last-child {
      background-color: #ff4d4d;
      color: white;
      font-weight: bold;
    }

    nav a:last-child:hover {
      background-color: #ff1a1a;
    }

    @media (max-width: 600px) {
      nav {
        flex-direction: column;
      }

      nav a {
        margin: 6px 0;
      }
    }
  </style>
</head>
<body>

<nav>
  <a href="index.php">🏠 Home</a>
  <a href="order.php">📝 Orders</a>
  <a href="paying_bill.php">💰 Payments</a>
  <a href="view_page.php">📄 View Bills</a>
  <a href="add_customer.php">👥 Customers</a>
  <a href="Over_time.php">⏱ OT Emp</a>
  <a href="add_items.php">🍽 Add Items</a>
  <a href="logout.php">🚪 Logout</a>
</nav>

</body>
</html>

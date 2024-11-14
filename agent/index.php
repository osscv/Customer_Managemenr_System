<?php
include "config.php";
session_start();

// Redirect if already logged in
if (isset($_SESSION["agent_name"])) {
    header("Location: http://51.222.110.90:288/agent/dashboard.php");
    exit();
}

// Database connection parameters
$servername = "localhost";
$username = "callingsys";
$password = "Xann7ADX8nCytZyh";
$dbname = "callingsys";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle login form submission
$login_error = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $agent_name = $_POST['agent_name'];
    $password = $_POST['password'];

    // Prepare and execute the query to check credentials
    $stmt = $conn->prepare("SELECT Password FROM agents WHERE Agent_Name = ?");
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("s", $agent_name);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if credentials are correct
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if ($password === $row['Password']) {
            // User is authenticated
            $_SESSION["agent_name"] = $agent_name;
            header("Location: http://51.222.110.90:288/agent/dashboard.php");
            exit();
        } else {
            // Authentication failed
            $login_error = "Invalid username or password.";
        }
    } else {
        // Authentication failed
        $login_error = "Invalid username or password.";
    }

    // Close the statement
    $stmt->close();
}

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AGENT Login- Customer Management by KHOO LAY YANG DKLY</title>
    <style>
        /* Global Styles */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f7fa;
            color: #4a4a4a;
            line-height: 1.6;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        h2 {
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
            color: #007bff;
        }

        form {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }

        input {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            transition: border-color 0.3s;
        }

        input:focus {
            border-color: #007bff;
            outline: none;
        }

        .error {
            color: red;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>AGENT | Customer Management by DKLY</h1>
        <h2>Login</h2>
        <?php if ($login_error): ?>
            <div class="error"><?php echo htmlspecialchars($login_error); ?></div>
        <?php endif; ?>
        <form action="" method="POST">
            <label for="agent_name">Agent Name:</label>
            <input type="text" id="agent_name" name="agent_name" required>
            
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            
            <input type="submit" value="Login">
        </form>
    </div>
</body>
</html>

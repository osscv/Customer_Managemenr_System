<!-- REGISTER -->
<!-- By KHOO LAY YANG -->
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
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

/* Header Styles */
h2 {
    border-bottom: 2px solid #007bff;
    padding-bottom: 10px;
    color: #007bff;
}

/* Form Styles */
form {
    background-color: #ffffff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    margin-bottom: 20px;
}

label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
    color: #333;
}

input, select, textarea {
    width: 100%;
    padding: 12px;
    margin-bottom: 15px;
    border: 1px solid #ccc;
    border-radius: 5px;
    transition: border-color 0.3s;
}

input:focus, select:focus, textarea:focus {
    border-color: #007bff;
    outline: none;
}

/* Checkbox Group */
.checkbox-group {
    margin-bottom: 15px;
}

.checkbox-group label {
    margin-right: 15px;
}

/* Table Styles */
table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
    background-color: #fff;
    border-radius: 5px;
    overflow: hidden;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
}

th, td {
    padding: 12px;
    text-align: left;
    border: 1px solid #ddd;
}

th {
    background-color: #007bff;
    color: black; /* Change header text color to black */
}

/* Pagination Styles */
.pagination {
    margin-top: 20px;
    text-align: center;
}

.pagination a {
    margin: 0 5px;
    padding: 10px 15px;
    border: 1px solid #007bff;
    border-radius: 5px;
    color: #007bff;
    text-decoration: none;
    transition: background-color 0.3s, color 0.3s;
}

.pagination a:hover {
    background-color: #0056b3;
    color: white;
}

.pagination a.active {
    background-color: #007bff;
    color: white;
}

/* Responsive Styles */
@media (max-width: 768px) {
    input, select, textarea {
        padding: 10px;
    }

    th, td {
        padding: 10px;
    }
}


</style>
<style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        form {
            margin-bottom: 20px;
            padding: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            max-width: 400px;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input, select, textarea {
            width: 100%;
            margin-bottom: 10px;
            padding: 8px;
            border-radius: 4px;
            border: 1px solid #ccc;
        }
        .checkbox-group {
            margin-bottom: 10px;
        }
        .hidden {
            display: none;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .pagination {
            margin-top: 20px;
        }
        .pagination a {
            margin: 0 5px;
            text-decoration: none;
            padding: 5px 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .pagination a.active {
            background-color: #007bff;
            color: white;
        }
    </style>
<?php
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

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $agent_name = $_POST['agent_name'];
    $password = $_POST['password'];
    $remarks = $_POST['remarks'];

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO agents (Agent_Name, Password, Remarks) VALUES (?, ?, ?)");
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("sss", $agent_name, $password, $remarks);

    // Execute the statement
    if ($stmt->execute()) {
        echo "New account created successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close the statement
    $stmt->close();
}

// Fetch agents list
$agents = [];
$result = $conn->query("SELECT * FROM agents");
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $agents[] = $row;
    }
}

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
</head>
<body>
    
    <form action="" method="POST">
        <h2>Register New Agent</h2>
        <label for="agent_name">Agent Name:</label>
        <input type="text" id="agent_name" name="agent_name" required><br><br>
        
        <label for="password">Password:</label>
        <input type="text" id="password" name="password" required><br><br>
        
        <label for="remarks">Remarks:</label>
        <textarea id="remarks" name="remarks"></textarea><br><br>
        
        <input type="submit" value="Register">
    </form>

    <h2>Agent List</h2>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Agent Name</th>
            <th>Password</th>
            <th>Remarks</th>
        </tr>
        <?php if (!empty($agents)): ?>
            <?php foreach ($agents as $agent): ?>
                <tr>
                    <td><?php echo htmlspecialchars($agent['ID']); ?></td>
                    <td><?php echo htmlspecialchars($agent['Agent_Name']); ?></td>
                    <td><?php echo htmlspecialchars($agent['Password']); ?></td>
                    <td><?php echo htmlspecialchars($agent['Remarks']); ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="4">No agents found.</td>
            </tr>
        <?php endif; ?>
    </table>
</body>
</html>
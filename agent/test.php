<?php
session_start(); // Start the session for login functionality

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

// Handle login
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $agent_name = trim($_POST['agent_name']);

    // Store the agent's name in the session
    $_SESSION['agent_name'] = $agent_name;
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if adding a customer
    if (isset($_POST['add_customer'])) {
        $name = trim($_POST['name']);
        $hp_number = $_POST['hp_number'];
        $age = (int)$_POST['age'];
        $nationality = $_POST['nationality'];
        $language = isset($_POST['language']) ? implode(',', $_POST['language']) : '';
        $status = $_POST['status'];
        $agent = $_SESSION['agent_name'] ?? 'Unknown'; // Get agent name from session

        // Prepare and execute the SQL query using prepared statements
        $stmt = $conn->prepare("INSERT INTO customers (Name, HP_Number, Age, Nationality, Language, Status, Agent) 
                                VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssisiss", $name, $hp_number, $age, $nationality, $language, $status, $agent);

        if ($stmt->execute()) {
            echo "New customer record created successfully.";
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    }

    // Check if adding an additional record
    if (isset($_POST['add_record'])) {
        $customer_id = (int)$_POST['customer_id'];
        $status = $_POST['status'];
        $important_remarks = $_POST['important_remarks'];
        $package = $_POST['package'];
        $appointment_date_time = $_POST['appointment_date_time'];
        $agent_name = $_SESSION['agent_name'] ?? 'Unknown'; // Get agent name from session
        $customer_spoken = $_POST['customer_spoken'];
        $agent_remarks = $_POST['agent_remarks'];

        // Get current date and time
        $add_record_datetime = date('Y-m-d H:i:s');

        // Prepare Appointment Date & Time based on the status
        $appointment_date_time = ($status === 'Interested' || $status === 'Booked Appointment') ? 
            (!empty($appointment_date_time) ? $appointment_date_time : NULL) : NULL;

        // Prepare and execute the SQL query using prepared statements
        $stmt = $conn->prepare("INSERT INTO additional_records (Customer_ID, Status, Important_Remarks, Package, 
                                Appointment_Date_Time, Agent_Name, Customer_Spoken, Agent_Remarks, Added_Date_Time) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssssss", $customer_id, $status, $important_remarks, $package, $appointment_date_time, $agent_name, $customer_spoken, $agent_remarks, $add_record_datetime);

        // Prepare and execute the status update query
        $update_status_stmt = $conn->prepare("UPDATE customers SET Status=? WHERE ID=?");
        $update_status_stmt->bind_param("si", $status, $customer_id);

        if ($stmt->execute() && $update_status_stmt->execute()) {
            echo "New additional record created successfully.";
        } else {
            echo "Error: " . $stmt->error . "<br>" . $update_status_stmt->error;
        }
        $stmt->close();
        $update_status_stmt->close();
    }
}

// Retrieve customers for the logged-in agent with search and filter functionality
$agent_name = $_SESSION['agent_name'] ?? '';
$search_name = $_POST['search_name'] ?? '';
$filter_status = $_POST['filter_status'] ?? '';

$customers_sql = "SELECT * FROM customers WHERE Agent=?";
$params = [$agent_name];
$types = "s";

// Add search and filter conditions
if (!empty($search_name)) {
    $customers_sql .= " AND Name LIKE ?";
    $params[] = "%$search_name%";
    $types .= "s";
}

if (!empty($filter_status) && $filter_status != 'All') {
    $customers_sql .= " AND Status=?";
    $params[] = $filter_status;
    $types .= "s";
}

// Prepare and execute the SQL query using prepared statements
$stmt = $conn->prepare($customers_sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$customers_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AGENT - Customer Management by KHOO LAY YANG DKLY</title>
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
            color: black; /* Header text color */
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

        /* Hidden Class for Appointment Field */
        .hidden {
            display: none;
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
    <script>
        function toggleAppointmentField() {
            var status = document.getElementById('status_add_record').value;
            var appointmentField = document.getElementById('appointment_date_time_field');
            if (status === 'Interested' || status === 'Booked Appointment') {
                appointmentField.classList.remove('hidden');
            } else {
                appointmentField.classList.add('hidden');
            }
        }
    </script>
</head>
<body>

<div class="container">
    <h1>AGENT - Customer Management by DKLY</h1>
    <br>
    <h2>Login</h2>
    <form action="" method="POST">
        <label for="agent_name">Agent Name:</label>
        <input type="text" id="agent_name" name="agent_name" required>
        <input type="submit" name="login" value="Login">
    </form>
    <p>1) You need to LOGIN with your UserName first before accessing the Function.<br>
    2) If unable to view anything, please clear cache of the browser.</p>
    <br>
    <h2>Add Customer</h2>
    <form action="" method="POST">
        <label for="name">Customer Name:</label>
        <input type="text" id="name" name="name" required>

        <label for="hp_number">H/P Number:</label>
        <input type="text" id="hp_number" name="hp_number" placeholder="+60" required>

        <label for="age">Age:</label>
        <input type="number" id="age" name="age" required>

        <label for="nationality">Nationality:</label>
        <select id="nationality" name="nationality" required>
            <option value="Malaysia">Malaysia</option>
            <option value="Singapore">Singapore</option>
        </select>

        <label>Language:</label>
        <div class="checkbox-group">
            <label><input type="checkbox" name="language[]" value="Bahasa Melayu"> Bahasa Melayu</label>
            <label><input type="checkbox" name="language[]" value="Chinese/Dialect"> Chinese/Dialect</label>
            <label><input type="checkbox" name="language[]" value="English"> English</label>
        </div>

        <label for="status">Status:</label>
        <select id="status" name="status" required>
            <option value="Unknown">Unknown</option>
            <option value="Done">Done</option>
            <option value="Interested">Interested</option>
            <option value="Unable to Reach">Unable to Reach</option>
            <option value="Rejected">Rejected</option>
            <option value="Booked Appointment">Booked Appointment</option>
        </select>

        <input type="submit" name="add_customer" value="Add Customer">
    </form>

    <h2>Add Additional Record</h2>
    <form action="" method="POST">
        <label for="customer_id">Customer ID:</label>
        <input type="number" id="customer_id" name="customer_id" required>

        <label for="status_add_record">Status:</label>
        <select id="status_add_record" name="status" required onchange="toggleAppointmentField()">
            <option value="Unknown">Unknown</option>
            <option value="Done">Done</option>
            <option value="Interested">Interested</option>
            <option value="Unable to Reach">Unable to Reach</option>
            <option value="Rejected">Rejected</option>
            <option value="Booked Appointment">Booked Appointment</option>
            <!-- <option value="test_status">test_status</option> -->
        </select>

        <div id="appointment_date_time_field" class="hidden">
            <label for="appointment_date_time">Appointment Date & Time:</label>
            <input type="datetime-local" id="appointment_date_time" name="appointment_date_time">
        </div>

        <label for="important_remarks">Important Remarks:</label>
        <select id="important_remarks" name="important_remarks" required>
            <option value="N/A">N/A</option>
            <option value="Transfer Agent Servicing">Transfer Agent Servicing</option>
            <option value="Required to Follow Up">Required to Follow Up</option>
            <option value="No Manners (Rude)">No Manners (Rude)</option>
            <!-- <option value="test_imremarks">test_imremarks</option> -->
        </select>

        <label for="package">Package:</label>
        <select id="package" name="package" required>
            <option value="Package A">Package A</option>
            <option value="Package B">Package B</option>
            <!-- <option value="test_package">test_package</option> -->
        </select>

        <label for="agent_name">Agent Name:</label>
        <input type="text" id="agent_name" name="agent_name" value="<?php echo htmlspecialchars($_SESSION['agent_name'] ?? '', ENT_QUOTES); ?>" readonly>

        <label for="customer_spoken">Customer Spoken:</label>
        <input type="text" id="customer_spoken" name="customer_spoken" required>

        <label for="agent_remarks">Agent Remarks:</label>
        <textarea id="agent_remarks" name="agent_remarks" required></textarea>

        <input type="submit" name="add_record" value="Add Record">
    </form>

    <h2>Search Customers</h2>
    <form action="" method="POST">
        <label for="search_name">Search Customer Name:</label>
        <input type="text" id="search_name" name="search_name" value="<?php echo htmlspecialchars($search_name, ENT_QUOTES); ?>">

        <label for="filter_status">Filter by Status:</label>
        <select id="filter_status" name="filter_status">
            <option value="All">All</option>
            <option value="Unknown" <?php if ($filter_status == 'Unknown') echo 'selected'; ?>>Unknown</option>
            <option value="Done" <?php if ($filter_status == 'Done') echo 'selected'; ?>>Done</option>
            <option value="Interested" <?php if ($filter_status == 'Interested') echo 'selected'; ?>>Interested</option>
            <option value="Unable to Reach" <?php if ($filter_status == 'Unable to Reach') echo 'selected'; ?>>Unable to Reach</option>
            <option value="Rejected" <?php if ($filter_status == 'Rejected') echo 'selected'; ?>>Rejected</option>
            <option value="Booked Appointment" <?php if ($filter_status == 'Booked Appointment') echo 'selected'; ?>>Booked Appointment</option>
        </select>

        <input type="submit" value="Search">
    </form>

    <h2>Customer List</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>H/P Number</th>
            <th>Age</th>
            <th>Nationality</th>
            <th>Language</th>
            <th>Status</th>
            <th>Agent</th>
            <th>Additional Records</th>
        </tr>
        <?php
        if ($customers_result->num_rows > 0) {
            while($row = $customers_result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['ID'], ENT_QUOTES) . "</td>";
                echo "<td>" . htmlspecialchars($row['Name'], ENT_QUOTES) . "</td>";
                echo "<td>" . htmlspecialchars($row['HP_Number'], ENT_QUOTES) . "</td>";
                echo "<td>" . htmlspecialchars($row['Age'], ENT_QUOTES) . "</td>";
                echo "<td>" . htmlspecialchars($row['Nationality'], ENT_QUOTES) . "</td>";
                echo "<td>" . htmlspecialchars($row['Language'], ENT_QUOTES) . "</td>";
                echo "<td>" . htmlspecialchars($row['Status'], ENT_QUOTES) . "</td>";
                echo "<td>" . htmlspecialchars($row['Agent'], ENT_QUOTES) . "</td>"; // Display the agent name

                $customer_id = $row['ID'];
                $additional_records_sql = "SELECT * FROM additional_records WHERE Customer_ID=?";
                $additional_records_stmt = $conn->prepare($additional_records_sql);
                $additional_records_stmt->bind_param("i", $customer_id);
                $additional_records_stmt->execute();
                $additional_records_result = $additional_records_stmt->get_result();

                echo "<td>";
                if ($additional_records_result && $additional_records_result->num_rows > 0) {
                    echo "<ul>";
                    while ($record = $additional_records_result->fetch_assoc()) {
                        echo "<li>" . 
                             "<strong>" . htmlspecialchars($record['Added_Date_Time'], ENT_QUOTES) . "</strong>" .
                             "<br>Status: " . htmlspecialchars($record['Status'], ENT_QUOTES) . "<br>" .
                             "Important Remarks: " . htmlspecialchars($record['Important_Remarks'], ENT_QUOTES) .  "<br>" .
                             "Package: " . htmlspecialchars($record['Package'], ENT_QUOTES) .  "<br>" .
                             "Appointment Date & Time: " . htmlspecialchars($record['Appointment_Date_Time'], ENT_QUOTES) .  "<br>" .
                             "Agent Name: " . htmlspecialchars($record['Agent_Name'], ENT_QUOTES) .  "<br>" .
                             "Customer Spoken: " . htmlspecialchars($record['Customer_Spoken'], ENT_QUOTES) .  "<br>" .
                             "Agent Remarks: " . htmlspecialchars($record['Agent_Remarks'], ENT_QUOTES) .  "<br>" .
                             "</li><br>";
                    }

                    echo "</ul>";
                } else {
                    echo "No additional records.";
                }
                echo "</td>";

                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='9'>No customers found.</td></tr>";
        }
        ?>
    </table>
</div>

</body>
</html>

<?php
$conn->close();
?>

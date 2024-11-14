<!-- AGENT -->
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

//Handle login (have bug)
//need to fix the validation logical flow
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $agent_name = $_POST['agent_name'];

    // Store the agent's name in the session
    $_SESSION['agent_name'] = $agent_name;
}  



// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if adding a customer
    if (isset($_POST['add_customer'])) {
        $name = $_POST['name'];
        $hp_number = $_POST['hp_number'];
        $age = $_POST['age'];
        $nationality = $_POST['nationality'];
        $language = isset($_POST['language']) ? implode(',', $_POST['language']) : '';
        $status = $_POST['status'];
        $agent = isset($_SESSION['agent_name']) ? $_SESSION['agent_name'] : 'Unknown'; // Get agent name from session

        $sql = "INSERT INTO customers (Name, HP_Number, Age, Nationality, Language, Status, Agent) 
                VALUES ('$name', '$hp_number', $age, '$nationality', '$language', '$status', '$agent')";

        if ($conn->query($sql) === TRUE) {
            echo "New customer record created successfully.";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }


    // Check if adding an additional record
    if (isset($_POST['add_record'])) {
        $customer_id = $_POST['customer_id'];
        $status = $_POST['status'];
        $important_remarks = $_POST['important_remarks'];
        $package = $_POST['package'];
        $appointment_date_time = $_POST['appointment_date_time'];
        $agent_name = $_SESSION['agent_name']; // Get agent name from session
        $customer_spoken = $_POST['customer_spoken'];
        $agent_remarks = $_POST['agent_remarks'];

        // Get current date and time
        $add_record_datetime = date('Y-m-d H:i:s');

        // Prepare Appointment Date & Time based on the status
        if ($status === 'Interested' || $status === 'Booked Appointment') {
            $appointment_date_time = !empty($appointment_date_time) ? "'" . $appointment_date_time . "'" : "NULL";
        } else {
            $appointment_date_time = "NULL";
        }

        //command of INSERT data to the database
        $sql = "INSERT INTO additional_records (Customer_ID, Status, Important_Remarks, Package, 
                Appointment_Date_Time, Agent_Name, Customer_Spoken, Agent_Remarks, Added_Date_Time) 
                VALUES ($customer_id, '$status', '$important_remarks', '$package', $appointment_date_time, 
                '$agent_name', '$customer_spoken', '$agent_remarks', '$add_record_datetime')";
        
        //this to update the data to the database
        $update_status_sql = "UPDATE customers SET Status='$status' WHERE ID=$customer_id";

        if ($conn->query($sql) === TRUE && $conn->query($update_status_sql) === TRUE) {
            echo "New additional record created successfully.";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}

// Retrieve customers for the logged-in agent with search and filter functionality
//session is to cache the login username (agent name)
$agent_name = isset($_SESSION['agent_name']) ? $_SESSION['agent_name'] : '';
$search_name = isset($_POST['search_name']) ? $_POST['search_name'] : '';
$filter_status = isset($_POST['filter_status']) ? $_POST['filter_status'] : '';

$customers_sql = "SELECT * FROM customers WHERE Agent='$agent_name'";

// Add search and filter conditions
if (!empty($search_name)) {
    $customers_sql .= " AND Name LIKE '%" . $conn->real_escape_string($search_name) . "%'";
}

if (!empty($filter_status) && $filter_status != 'All') {
    $customers_sql .= " AND Status='" . $conn->real_escape_string($filter_status) . "'";
}

$customers_result = $conn->query($customers_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AGENT - Customer Management by KHOO LAY YANG DKLY</title>
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

<h1>AGENT - Customer Management by DKLY</h1>
<br>
<h2>Login</h2>
<p>1) You need to LOGIN with your UserName frist before accessing the Function.<br>2) If unable to view anything, please clear cache of the browser.</p>
<form action="" method="POST">
    <label for="agent_name">Agent Name:</label>
    <input type="text" id="agent_name" name="agent_name" required>
    <input type="submit" name="login" value="Login">
</form>
    

<br>
<h2>Add Customer</h2>
<p>1) If the the Language; Nationality; Package is not there. Kindly please refer to the technical team.<br>
2) While filling the H/P Number, please fill in with the country code. Example: +60-1234567890</p>
<form action="" method="POST">
    <label for="name">Customer Name:</label>
    <input type="text" id="name" name="name" required>

    <label for="hp_number">H/P Number:</label>
    <input type="text" id="hp_number" name="hp_number" placeholder="+60-1234567890" required>

    <label for="age">Age:</label>
    <input type="number" id="age" name="age" required>
    
<!-- can be added, unlimited -->
    <label for="nationality">Nationality:</label>
    <select id="nationality" name="nationality" required>
        <option value="Malaysia">Malaysia</option>
        <option value="Singapore">Singapore</option>
        <option value="Indonesia">Indonesia</option>
    </select>

    <label for="agent_name">Agent Name:</label>
    <input type="text" id="agent_name" name="agent_name" value="<?php echo isset($_SESSION['agent_name']) ? $_SESSION['agent_name'] : ''; ?>">
    
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
        <option value="Pending Follow-Up">Pending Follow-Up</option>
        <option value="In Progress">In Progress</option>
        <option value="No Show">No Show</option>
        <option value="Awaiting Response">Awaiting Response</option>
        <option value="Confirmed">Confirmed</option>
        <option value="Completed">Completed</option>
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
        <option value="Escalate to Supervisor">Escalate to Supervisor</option>
        <option value="Billing Issue">Billing Issue</option>
        <option value="Technical Support Required">Technical Support Required</option>
        <option value="Customer Request for Refund">Customer Request for Refund</option>
        <option value="General Inquiry">General Inquiry</option>
        <option value="Urgent Attention Required">Urgent Attention Required</option>
        <option value="Complaint Filed">Complaint Filed</option>
        <option value="Clarification Needed">Clarification Needed</option>
        <option value="Feedback Provided">Feedback Provided</option>
    </select>
<!-- can be added, unlimited -->
    <label for="package">Package:</label>
    <select id="package" name="package" required>
        <option value="N/A">N/A</option>
        <option value="Package A">Package A</option>
        <option value="Package B">Package B</option>
    </select>

    <label for="agent_name">Agent Name:</label>
    <input type="text" id="agent_name" name="agent_name" value="<?php echo isset($_SESSION['agent_name']) ? $_SESSION['agent_name'] : ''; ?>" readonly>

    <label for="customer_spoken">Customer Spoken:</label>
    <input type="text" id="customer_spoken" name="customer_spoken" required>

    <label for="agent_remarks">Agent Remarks:</label>
    <textarea id="agent_remarks" name="agent_remarks" required></textarea>

    <input type="submit" name="add_record" value="Add Record">
</form>

<h2>Search Customers</h2>
<p>1) It is not nessary to fill up all section to Filter.<br>
2) You may choose to Search by Name or just by Status.</p>
<form action="" method="POST">
    <label for="search_name">Search Customer Name:</label>
    <input type="text" id="search_name" name="search_name" value="<?php echo htmlspecialchars($search_name); ?>">

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
<p>1) It will only show the customer that related to you only.<br>
2) You are not allowed to view other agent customer data.</p>
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
            echo "<td>" . $row['ID'] . "</td>";
            echo "<td>" . $row['Name'] . "</td>";
            echo "<td>" . $row['HP_Number'] . "</td>";
            echo "<td>" . $row['Age'] . "</td>";
            echo "<td>" . $row['Nationality'] . "</td>";
            echo "<td>" . $row['Language'] . "</td>";
            echo "<td>" . $row['Status'] . "</td>";
            echo "<td>" . $row['Agent'] . "</td>"; // Display the agent name

            $customer_id = $row['ID'];
            $additional_records_sql = "SELECT * FROM additional_records WHERE Customer_ID=$customer_id";
            $additional_records_result = $conn->query($additional_records_sql);

            echo "<td>";
            if ($additional_records_result && $additional_records_result->num_rows > 0) {
                echo "<ul>";
                while ($record = $additional_records_result->fetch_assoc()) {
                    echo "<li>" . 
                         "<strong>" . $record['Added_Date_Time'] . "</strong>" .
                         "<br>Status: " . $record['Status'] . "<br>" .
                         "Important Remarks: " . $record['Important_Remarks'] .  "<br>" .
                         "Package: " . $record['Package'] .  "<br>" .
                         "Appointment Date & Time: " . $record['Appointment_Date_Time'] .  "<br>" .
                         "Agent Name: " . $record['Agent_Name'] .  "<br>" .
                         "Customer Spoken: " . $record['Customer_Spoken'] .  "<br>" .
                         "Agent Remarks: " . $record['Agent_Remarks'] .  "<br>" .
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

</body>
</html>

<?php
$conn->close();
?>

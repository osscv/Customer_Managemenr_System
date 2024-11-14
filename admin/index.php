<!-- ADMIN -->
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
<?php
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
    // Check if adding a customer
    if (isset($_POST['add_customer'])) {
        $name = $_POST['name'];
        $hp_number = $_POST['hp_number'];
        $age = $_POST['age'];
        $nationality = $_POST['nationality'];
        $language = isset($_POST['language']) ? implode(',', $_POST['language']) : '';
        $status = $_POST['status'];
        $agent = isset($_POST['agent_name']) ? $_POST['agent_name'] : 'Unknown';

        
        // agent_name is now applyed
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
        $agent_name = $_POST['agent_name'];
        $customer_spoken = $_POST['customer_spoken'];
        $agent_remarks = $_POST['agent_remarks'];
        

        // Get current date and time
        //2024-11-12 12:10:07
        $add_record_datetime = date('Y-m-d H:i:s');

        // Prepare Appointment Date & Time based on the status
        if ($status === 'Interested' || $status === 'Booked Appointment') {
            $appointment_date_time = !empty($appointment_date_time) ? "'" . $appointment_date_time . "'" : "NULL";
        } else {
            $appointment_date_time = "NULL"; //which mean when no booking shown
        }

        $sql = "INSERT INTO additional_records (Customer_ID, Status, Important_Remarks, Package, 
                Appointment_Date_Time, Agent_Name, Customer_Spoken, Agent_Remarks, Added_Date_Time) 
                VALUES ($customer_id, '$status', '$important_remarks', '$package', $appointment_date_time, 
                '$agent_name', '$customer_spoken', '$agent_remarks', '$add_record_datetime')";

        $update_status_sql = "UPDATE customers SET Status='$status' WHERE ID=$customer_id";

        if ($conn->query($sql) === TRUE && $conn->query($update_status_sql) === TRUE) {
            echo "New additional record created successfully.";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}

// Prepare to retrieve customers with search and filter functionality
$search_name = isset($_POST['search_name']) ? $_POST['search_name'] : '';
$filter_status = isset($_POST['filter_status']) ? $_POST['filter_status'] : '';
$filter_agent = isset($_POST['filter_agent']) ? $_POST['filter_agent'] : '';

// Pagination variables
$limit = 30; // Number of records per page, can change the number. recommend 30 per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Current page
$offset = ($page - 1) * $limit; // Offset for the SQL query

$customers_sql = "SELECT * FROM customers WHERE 1=1";

// Apply filters
if (!empty($search_name)) {
    $customers_sql .= " AND Name LIKE '%$search_name%'";
}
if (!empty($filter_status)) {
    $customers_sql .= " AND Status='$filter_status'";
}
if (!empty($filter_agent)) {
    // Assuming Agent_Name is in additional_records table
    $customers_sql .= " AND ID IN (SELECT Customer_ID FROM additional_records WHERE Agent_Name LIKE '%$filter_agent%')";
}

$customers_sql .= " LIMIT $limit OFFSET $offset"; // Add limit and offset
$customers_result = $conn->query($customers_sql);

// Get total records for pagination
$total_customers_sql = "SELECT COUNT(*) as total FROM customers WHERE 1=1";
if (!empty($search_name)) {
    $total_customers_sql .= " AND Name LIKE '%$search_name%'";
}
if (!empty($filter_status)) {
    $total_customers_sql .= " AND Status='$filter_status'";
}
if (!empty($filter_agent)) {
    $total_customers_sql .= " AND ID IN (SELECT Customer_ID FROM additional_records WHERE Agent_Name LIKE '%$filter_agent%')";
}
$total_result = $conn->query($total_customers_sql);
$total_row = $total_result->fetch_assoc();
$total_records = $total_row['total'];
$total_pages = ceil($total_records / $limit); // Calculate total pages

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADMIN - Customer Management by KHOO LAY YANG DKLY</title>
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
<h1>ADMIN - Customer Management by DKLY</h1>
<style>
        iframe {
            width: 100%;
            min-height: 850px;
            border: none;
        }
</style>
<h3>Section A</h3>
<iframe src="register.php" title="Registration Form"></iframe>

<h3>Section B</h3>

<div class="form-container">
    <form action="" method="POST">
        <h2>Add Customer</h2>
    <p>1) If the the Language; Nationality; Package is not there. Kindly please refer to the technical team.<br>
    2) While filling the H/P Number, please fill in with the country code. Example: +60-1234567890</p>
        <label for="name">Customer Name:</label>
        <input type="text" id="name" name="name" required>
    
        <label for="hp_number">H/P Number:</label>
        <input type="text" id="hp_number" name="hp_number" placeholder="+60-1234567890" required>
    
        <label for="age">Age:</label>
        <input type="number" id="age" name="age" required>
<!-- nationality CannotCannot bebe multiple -->
        <label for="nationality">Nationality:</label>
        <select id="nationality" name="nationality" required>
            <option value="" disabled selected>Select your nationality</option>
            <option value="Malaysia">Malaysia</option>
            <option value="Singapore">Singapore</option>
            <option value="Afghanistan">Afghanistan</option>
            <option value="Albania">Albania</option>
            <option value="Algeria">Algeria</option>
            <option value="Andorra">Andorra</option>
            <option value="Angola">Angola</option>
            <option value="Antigua and Barbuda">Antigua and Barbuda</option>
            <option value="Argentina">Argentina</option>
            <option value="Armenia">Armenia</option>
            <option value="Australia">Australia</option>
            <option value="Austria">Austria</option>
            <option value="Azerbaijan">Azerbaijan</option>
            <option value="Bahamas">Bahamas</option>
            <option value="Bahrain">Bahrain</option>
            <option value="Bangladesh">Bangladesh</option>
            <option value="Barbados">Barbados</option>
            <option value="Belarus">Belarus</option>
            <option value="Belgium">Belgium</option>
            <option value="Belize">Belize</option>
            <option value="Benin">Benin</option>
            <option value="Bhutan">Bhutan</option>
            <option value="Bolivia">Bolivia</option>
            <option value="Bosnia and Herzegovina">Bosnia and Herzegovina</option>
            <option value="Botswana">Botswana</option>
            <option value="Brazil">Brazil</option>
            <option value="Brunei">Brunei</option>
            <option value="Bulgaria">Bulgaria</option>
            <option value="Burkina Faso">Burkina Faso</option>
            <option value="Burundi">Burundi</option>
            <option value="Cabo Verde">Cabo Verde</option>
            <option value="Cambodia">Cambodia</option>
            <option value="Cameroon">Cameroon</option>
            <option value="Canada">Canada</option>
            <option value="Central African Republic">Central African Republic</option>
            <option value="Chad">Chad</option>
            <option value="Chile">Chile</option>
            <option value="China">China</option>
            <option value="Colombia">Colombia</option>
            <option value="Comoros">Comoros</option>
            <option value="Congo (Congo-Brazzaville)">Congo (Congo-Brazzaville)</option>
            <option value="Costa Rica">Costa Rica</option>
            <option value="Croatia">Croatia</option>
            <option value="Cuba">Cuba</option>
            <option value="Cyprus">Cyprus</option>
            <option value="Czechia (Czech Republic)">Czechia (Czech Republic)</option>
            <option value="Denmark">Denmark</option>
            <option value="Djibouti">Djibouti</option>
            <option value="Dominica">Dominica</option>
            <option value="Dominican Republic">Dominican Republic</option>
            <option value="Ecuador">Ecuador</option>
            <option value="Egypt">Egypt</option>
            <option value="El Salvador">El Salvador</option>
            <option value="Equatorial Guinea">Equatorial Guinea</option>
            <option value="Eritrea">Eritrea</option>
            <option value="Estonia">Estonia</option>
            <option value="Eswatini (fmr. "Swaziland")">Eswatini (fmr. "Swaziland")</option>
            <option value="Ethiopia">Ethiopia</option>
            <option value="Fiji">Fiji</option>
            <option value="Finland">Finland</option>
            <option value="France">France</option>
            <option value="Gabon">Gabon</option>
            <option value="Gambia">Gambia</option>
            <option value="Georgia">Georgia</option>
            <option value="Germany">Germany</option>
            <option value="Ghana">Ghana</option>
            <option value="Greece">Greece</option>
            <option value="Grenada">Grenada</option>
            <option value="Guatemala">Guatemala</option>
            <option value="Guinea">Guinea</option>
            <option value="Guinea-Bissau">Guinea-Bissau</option>
            <option value="Guyana">Guyana</option>
            <option value="Haiti">Haiti</option>
            <option value="Honduras">Honduras</option>
            <option value="Hungary">Hungary</option>
            <option value="Iceland">Iceland</option>
            <option value="India">India</option>
            <option value="Indonesia">Indonesia</option>
            <option value="Iran">Iran</option>
            <option value="Iraq">Iraq</option>
            <option value="Ireland">Ireland</option>
            <option value="Israel">Israel</option>
            <option value="Italy">Italy</option>
            <option value="Jamaica">Jamaica</option>
            <option value="Japan">Japan</option>
            <option value="Jordan">Jordan</option>
            <option value="Kazakhstan">Kazakhstan</option>
            <option value="Kenya">Kenya</option>
            <option value="Kiribati">Kiribati</option>
            <option value="Korea (North)">Korea (North)</option>
            <option value="Korea (South)">Korea (South)</option>
            <option value="Kosovo">Kosovo</option>
            <option value="Kuwait">Kuwait</option>
            <option value="Kyrgyzstan">Kyrgyzstan</option>
            <option value="Laos">Laos</option>
            <option value="Latvia">Latvia</option>
            <option value="Lebanon">Lebanon</option>
            <option value="Lesotho">Lesotho</option>
            <option value="Liberia">Liberia</option>
            <option value="Libya">Libya</option>
            <option value="Liechtenstein">Liechtenstein</option>
            <option value="Lithuania">Lithuania</option>
            <option value="Luxembourg">Luxembourg</option>
            <option value="Madagascar">Madagascar</option>
            <option value="Malawi">Malawi</option>
            <option value="Maldives">Maldives</option>
            <option value="Mali">Mali</option>
            <option value="Malta">Malta</option>
            <option value="Marshall Islands">Marshall Islands</option>
            <option value="Mauritania">Mauritania</option>
            <option value="Mauritius">Mauritius</option>
            <option value="Mexico">Mexico</option>
            <option value="Micronesia">Micronesia</option>
            <option value="Moldova">Moldova</option>
            <option value="Monaco">Monaco</option>
            <option value="Mongolia">Mongolia</option>
            <option value="Montenegro">Montenegro</option>
            <option value="Morocco">Morocco</option>
            <option value="Mozambique">Mozambique</option>
            <option value="Myanmar (formerly Burma)">Myanmar (formerly Burma)</option>
            <option value="Namibia">Namibia</option>
            <option value="Nauru">Nauru</option>
            <option value="Nepal">Nepal</option>
            <option value="Netherlands">Netherlands</option>
            <option value="New Zealand">New Zealand</option>
            <option value="Nicaragua">Nicaragua</option>
            <option value="Niger">Niger</option>
            <option value="Nigeria">Nigeria</option>
            <option value="North Macedonia">North Macedonia</option>
            <option value="Norway">Norway</option>
            <option value="Oman">Oman</option>
            <option value="Pakistan">Pakistan</option>
            <option value="Palau">Palau</option>
            <option value="Palestine State">Palestine State</option>
            <option value="Panama">Panama</option>
            <option value="Papua New Guinea">Papua New Guinea</option>
            <option value="Paraguay">Paraguay</option>
            <option value="Peru">Peru</option>
            <option value="Philippines">Philippines</option>
            <option value="Poland">Poland</option>
            <option value="Portugal">Portugal</option>
            <option value="Qatar">Qatar</option>
            <option value="Romania">Romania</option>
            <option value="Russia">Russia</option>
            <option value="Rwanda">Rwanda</option>
            <option value="Saint Kitts and Nevis">Saint Kitts and Nevis</option>
            <option value="Saint Lucia">Saint Lucia</option>
            <option value="Saint Vincent and the Grenadines">Saint Vincent and the Grenadines</option>
            <option value="Samoa">Samoa</option>
            <option value="San Marino">San Marino</option>
            <option value="Sao Tome and Principe">Sao Tome and Principe</option>
            <option value="Saudi Arabia">Saudi Arabia</option>
            <option value="Senegal">Senegal</option>
            <option value="Serbia">Serbia</option>
            <option value="Seychelles">Seychelles</option>
            <option value="Sierra Leone">Sierra Leone</option>
            <option value="Slovakia">Slovakia</option>
            <option value="Slovenia">Slovenia</option>
            <option value="Solomon Islands">Solomon Islands</option>
            <option value="Somalia">Somalia</option>
            <option value="South Africa">South Africa</option>
            <option value="South Sudan">South Sudan</option>
            <option value="Spain">Spain</option>
            <option value="Sri Lanka">Sri Lanka</option>
            <option value="Sudan">Sudan</option>
            <option value="Suriname">Suriname</option>
            <option value="Sweden">Sweden</option>
            <option value="Switzerland">Switzerland</option>
            <option value="Syria">Syria</option>
            <option value="Taiwan">Taiwan</option>
            <option value="Tajikistan">Tajikistan</option>
            <option value="Tanzania">Tanzania</option>
            <option value="Thailand">Thailand</option>
            <option value="Timor-Leste">Timor-Leste</option>
            <option value="Turkey">Turkey</option>
            <option value="Turkmenistan">Turkmenistan</option>
            <option value="Tuvalu">Tuvalu</option>
            <option value="Uganda">Uganda</option>
            <option value="Ukraine">Ukraine</option>
            <option value="United Arab Emirates">United Arab Emirates</option>
            <option value="United Kingdom">United Kingdom</option>
            <option value="United States of America">United States of America</option>
            <option value="Uruguay">Uruguay</option>
            <option value="Uzbekistan">Uzbekistan</option>
            <option value="Vanuatu">Vanuatu</option>
            <option value="Vatican City (Holy See)">Vatican City (Holy See)</option>
            <option value="Venezuela">Venezuela</option>
            <option value="Vietnam">Vietnam</option>
            <option value="Yemen">Yemen</option>
            <option value="Zambia">Zambia</option>
            <option value="Zimbabwe">Zimbabwe</option>
        </select>
    
        <!--<label>Language:</label>
        <div class="checkbox-group">
            <label><input type="checkbox" name="language[]" value="Bahasa Melayu"> Bahasa Melayu</label>
            <label><input type="checkbox" name="language[]" value="Chinese/Dialect"> Chinese/Dialect</label>
            <label><input type="checkbox" name="language[]" value="English"> English</label>
        </div>-->
    
    <!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />

<!-- jQuery (necessary for Select2) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>


    <label for="languages">Languages:</label>
<select id="languages" name="language[]" multiple="multiple" style="width: 100%;">
    <option value="Bahasa Melayu">Bahasa Melayu</option>
    <option value="Chinese/Dialect">Chinese/Dialect</option>
    <option value="English">English</option>
    <option value="Spanish">Spanish</option>
    <option value="French">French</option>
    <option value="German">German</option>
    <option value="Italian">Italian</option>
    <option value="Portuguese">Portuguese</option>
    <option value="Russian">Russian</option>
    <option value="Japanese">Japanese</option>
    <option value="Korean">Korean</option>
    <option value="Arabic">Arabic</option>
    <option value="Hindi">Hindi</option>
    <option value="Bengali">Bengali</option>
    <option value="Urdu">Urdu</option>
    <option value="Turkish">Turkish</option>
    <option value="Vietnamese">Vietnamese</option>
    <option value="Thai">Thai</option>
    <option value="Swahili">Swahili</option>
    <option value="Dutch">Dutch</option>
    <option value="Greek">Greek</option>
    <option value="Hebrew">Hebrew</option>
    <option value="Polish">Polish</option>
    <option value="Czech">Czech</option>
    <option value="Hungarian">Hungarian</option>
    <option value="Danish">Danish</option>
    <option value="Swedish">Swedish</option>
    <option value="Norwegian">Norwegian</option>
    <option value="Finnish">Finnish</option>
    <option value="Romanian">Romanian</option>
    <option value="Serbian">Serbian</option>
    <option value="Croatian">Croatian</option>
    <option value="Slovak">Slovak</option>
    <option value="Bulgarian">Bulgarian</option>
    <option value="Ukrainian">Ukrainian</option>
    <option value="Lithuanian">Lithuanian</option>
    <option value="Latvian">Latvian</option>
    <option value="Estonian">Estonian</option>
    <option value="Maltese">Maltese</option>
    <option value="Icelandic">Icelandic</option>
    <option value="Armenian">Armenian</option>
    <option value="Georgian">Georgian</option>
    <option value="Kazakh">Kazakh</option>
    <option value="Uzbek">Uzbek</option>
    <option value="Turkmen">Turkmen</option>
    <option value="Kyrgyz">Kyrgyz</option>
    <option value="Pashto">Pashto</option>
    <option value="Farsi">Farsi</option>
    <option value="Mongolian">Mongolian</option>
    <option value="Burmese">Burmese</option>
    <option value="Khmer">Khmer</option>
    <option value="Lao">Lao</option>
    <option value="Nepali">Nepali</option>
    <option value="Sinhalese">Sinhalese</option>
    <option value="Tamil">Tamil</option>
    <option value="Balochi">Balochi</option>
    <option value="Sindhi">Sindhi</option>
    <option value="Hmong">Hmong</option>
    <option value="Tagalog">Tagalog</option>
    <option value="Malay">Malay</option>
    <option value="Javanese">Javanese</option>
    <option value="Sundanese">Sundanese</option>
    <option value="Haitian Creole">Haitian Creole</option>
    <option value="Yoruba">Yoruba</option>
    <option value="Igbo">Igbo</option>
    <option value="Zulu">Zulu</option>
    <option value="Xhosa">Xhosa</option>
    <option value="Amharic">Amharic</option>
    <option value="Somali">Somali</option>
    <option value="Tigrinya">Tigrinya</option>
    <option value="Wolof">Wolof</option>
    <option value="Lingala">Lingala</option>
    <option value="Quechua">Quechua</option>
    <option value="Aymara">Aymara</option>
    <option value="Guarani">Guarani</option>
    <option value="Hausa">Hausa</option>
    <option value="Māori">Māori</option>
    <option value="Bemba">Bemba</option>
    <option value="Luganda">Luganda</option>
    <option value="Sesotho">Sesotho</option>
    <option value="Tswana">Tswana</option>
    <option value="Twi">Twi</option>
</select>
<script>
$(document).ready(function() {
    $('#languages').select2({
        placeholder: 'Select languages',
        allowClear: true
    });
});
</script>

    
    
        <label for="agent_name">Agent Name:</label>
        <input type="text" id="agent_name" name="agent_name" required>
        
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
    
    
    <form action="" method="POST">
        <h2>Add Additional Record</h2>
        <label for="customer_id">Customer ID:</label>
        <input type="number" id="customer_id" name="customer_id" required>
    
    <!-- 
    Unknown: The status of the record is not yet determined.
    Done: The task or record has been completed.
    Interested: The customer or contact has shown interest.
    Unable to Reach: The agent was not able to contact the customer.
    Rejected: The customer has rejected the offer or request.
    Booked Appointment: An appointment has been scheduled with the customer.
    Pending Follow-Up: Follow-up is needed or scheduled.
    In Progress: The record or task is currently being worked on.
    No Show: The customer did not attend the scheduled appointment.
    Awaiting Response: Waiting for a reply from the customer.
    Confirmed: The customer has confirmed an appointment or action.
    Completed: The task or action is fully completed and closed.
    -->
    
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
    
    <!--
    N/A: Not applicable or no specific remarks.
    Transfer Agent Servicing: Requires attention or action by a transfer agent.
    Required to Follow Up: Needs further follow-up action from the agent.
    No Manners (Rude): The customer displayed rudeness or poor manners.
    Need an Appointment: Customer requires scheduling an appointment.
    Escalate to Supervisor: Issue needs to be elevated to a supervisor for resolution.
    Billing Issue: There is a problem related to billing or charges.
    Technical Support Required: Customer needs assistance with technical issues.
    Customer Request for Refund: Customer has requested a refund.
    General Inquiry: Customer has made a general question or request.
    Urgent Attention Required: The issue requires immediate attention or action.
    Complaint Filed: A formal complaint has been lodged by the customer.
    Clarification Needed: Additional clarification or information is required.
    Feedback Provided: Customer has given feedback or suggestions.
    -->
    
        <label for="important_remarks">Important Remarks:</label>
        <select id="important_remarks" name="important_remarks" required>
            <option value="N/A">N/A</option>
            <option value="Transfer Agent Servicing">Transfer Agent Servicing</option>
            <option value="Required to Follow Up">Required to Follow Up</option>
            <option value="No Manners (Rude)">No Manners (Rude)</option>
            <option value="Need an Appointment">Need an Appointment</option>
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

        <label for="package">Package:</label>
        <select id="package" name="package" required>
            <option value="N/A">N/A</option>
            <option value="Package A">Package A</option> <!--Can be added or remove-->
            <option value="Package B">Package B</option>
        </select>
    
        <label for="agent_name">Agent Name:</label>
        <input type="text" id="agent_name" name="agent_name" required>
    
        <label for="customer_spoken">Customer Spoken:</label>
        <input type="text" id="customer_spoken" name="customer_spoken" required>
    
        <label for="agent_remarks">Agent Remarks:</label>
        <textarea id="agent_remarks" name="agent_remarks" required></textarea> <!-- Agent can key in the Remarks that they wanted for thier own notes-->
    
        <input type="submit" name="add_record" value="Add Record">
    </form>
    
    
    <form action="" method="POST">
        <h2>Search & Filter Customers</h2>
        <p>1) It is not nessary to fill up all section to Filter.<br>
        2)You may choose to Search by Name or just by Status or Agent Name</p>
        <label for="search_name">Search by Customer Name:</label>
        <input type="text" id="search_name" name="search_name" value="<?php echo htmlspecialchars($search_name); ?>">
    
        <label for="filter_status">Filter by Status:</label>
        <select id="filter_status" name="filter_status">
            <option value="">All</option>
            <option value="Unknown" <?php if ($filter_status === 'Unknown') echo 'selected'; ?>>Unknown</option>
            <option value="Done" <?php if ($filter_status === 'Done') echo 'selected'; ?>>Done</option>
            <option value="Interested" <?php if ($filter_status === 'Interested') echo 'selected'; ?>>Interested</option>
            <option value="Unable to Reach" <?php if ($filter_status === 'Unable to Reach') echo 'selected'; ?>>Unable to Reach</option>
            <option value="Rejected" <?php if ($filter_status === 'Rejected') echo 'selected'; ?>>Rejected</option>
            <option value="Booked Appointment" <?php if ($filter_status === 'Booked Appointment') echo 'selected'; ?>>Booked Appointment</option>
        </select>
    
        <label for="filter_agent">Filter by Agent Name:</label>
        <input type="text" id="filter_agent" name="filter_agent" value="<?php echo htmlspecialchars($filter_agent); ?>">
    
        <input type="submit" value="Search">
    </form>
</div>
<style>
    .form-container {
        display: flex;
        gap: 20px; /* Space between the forms */
        flex-wrap: wrap; /* Allows forms to wrap on smaller screens */
        margin: 0 -10px; /* Offset padding on the container for consistent spacing */
    }

    .form {
        flex: 1; /* Allows forms to take up equal space */
        padding: 20px;
        border: 1px solid #ccc; /* Optional: border around forms */
        border-radius: 4px; /* Optional: rounded corners */
    }

    .form h2 {
        margin-top: 0; /* Remove top margin for consistency */
    }

    .hidden {
        display: none;
    }
</style>

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
        echo "<tr><td colspan='8'>No customers found.</td></tr>";
    }
    ?>
</table>

<!-- Pagination Links -->
<div class="pagination">
    <?php if ($page > 1): ?>
        <a href="?page=<?php echo $page - 1; ?>">&laquo; Previous</a>
    <?php endif; ?>
    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
        <a href="?page=<?php echo $i; ?>" class="<?php echo ($i === $page) ? 'active' : ''; ?>"><?php echo $i; ?></a>
    <?php endfor; ?>
    <?php if ($page < $total_pages): ?>
        <a href="?page=<?php echo $page + 1; ?>">Next &raquo;</a>
    <?php endif; ?>
</div>

</body>
</html>

<?php
$conn->close();
?>

# Customer Management System

Welcome to the **Customer Management System**! This project is a web-based application (WebApp) designed to enable admins and agents to conveniently manage customer data. 
This is the **V3 Version**. The final version has not been released publicly, as it was used for another project, but all functionalities are working well and can be used.

Developed by: [www.dkly.top](https://www.dkly.top)

### 🛠 Language and Tools

<div align="left">
  <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/javascript/javascript-original.svg" height="30" alt="javascript logo" />
  <img width="12" />
  <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/html5/html5-original.svg" height="30" alt="html5 logo" />
  <img width="12" />
  <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/css3/css3-original.svg" height="30" alt="css3 logo" />
  <img width="12" />
  <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/c/c-original.svg" height="30" alt="c logo" />
  <img width="12" />
  <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/cloudflare/cloudflare-original.svg" height="30" alt="cloudflare logo" />
  <img width="12" />
  <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/nginx/nginx-original.svg" height="30" alt="nginx logo" />
  <img width="12" />
  <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/php/php-original.svg" height="30" alt="php logo" />
  <img width="12" />
  <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/mysql/mysql-original-wordmark.svg" height="30" alt="mysql logo" />
</div>

---

If you use this repository, please support it by pressing the star button at the top-right of the page. Your support helps motivate continued development and improvements!

This project was created as part of an educational assignment, and I do not currently plan to continue it as it has already been submitted. However, we welcome educational collaboration!

### Contributing

1. Fork this repository and create a new branch (e.g., `feature/new-feature`).
2. Make your changes and commit them.
3. Push your branch and open a pull request.

Thank you for your support and contributions!

### Usage Guidelines

- **Business Use**: For commercial purposes, please contact me via email to request authorization.
- **Academic Use**: If using this project as a reference or incorporating its code into your assignment, please ensure you properly cite this project and specify the code that you have used to avoid any issues with plagiarism.




# Guide: Setting Up and Changing MySQL Password in `config.php`

To set up and change the MySQL password for the **Customer Management System**, follow these steps:

---

### 1. **Clone the Repository**

Clone the repository to your local machine using the following command:

```bash
git clone https://github.com/osscv/Customer_Managemenr_System.zip
```

### 2. **Upload to Your Server**

Upload the files to your server or hosting provider. For example, if you are using a BT Panel or a similar web hosting panel, you can upload the files to the desired directory (e.g., `/www/wwwroot/calling-system.dkly.top/v3/`).

### 3. **Unzip the Files**

After uploading the repository, navigate to the directory where you uploaded the files. Unzip the files if necessary. You can do this via your hosting panel or via SSH (e.g., using the `unzip` command):

```bash
unzip Customer_Managemenr_System.zip
```
This will extract the contents into the directory so that the system is ready to use.
![image](https://github.com/user-attachments/assets/73da876a-0a78-4839-84fe-ff52245ddbd9)

### 4. **Update the `config.php` File**

In your `config.php` file, change the password in the `$password` variable to your new MySQL password. Here's an updated version of the `config.php` file:

```php
<?php
$servername = "localhost";
$username = "callingsys";
// Change this to your new MySQL password
$password = "NEW_PASSWORD_HERE";
$dbname = "callingsys";
 
// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
 
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
```

Replace `NEW_PASSWORD_HERE` with your new password.

---

### 5. **Change MySQL Password**

To change the MySQL password for the `callingsys` user, follow these steps:

#### a. Log into MySQL

Open your terminal and run the following command to log into MySQL (replace `root` with your MySQL admin user if it's different):

```bash
mysql -u root -p
```

Enter your MySQL root password when prompted.

#### b. Change the Password

Once you're logged in, run the following SQL command to change the password for the `callingsys` user:

```sql
ALTER USER 'callingsys'@'localhost' IDENTIFIED BY 'NEW_PASSWORD_HERE';
```

Replace `NEW_PASSWORD_HERE` with the new password you want to set.

#### c. Flush Privileges

To ensure the changes take effect, run the following command:

```sql
FLUSH PRIVILEGES;
```

#### d. Exit MySQL

After the password is changed, exit MySQL by running:

```sql
EXIT;
```

---

### 6. **Test the Connection**

Once you've updated the password in both MySQL and `config.php`, test your connection to ensure everything is working correctly. If there are any issues, double-check the credentials in the `config.php` file and verify that the new password is correct.

---

### Additional Notes:
- **Business Use**: For commercial purposes, please contact me via email to request authorization.
- **Academic Use**: If using this project as a reference or incorporating its code into your assignment, please ensure you properly cite this project and specify the code that you have used to avoid any issues with plagiarism.


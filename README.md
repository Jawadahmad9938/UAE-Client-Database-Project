AutomobileCompanyDB Project Setup Guide
Welcome to the AutomobileCompanyDB project! This PHP web application manages an automobile company’s database, allowing you to view, add, update, and delete records for companies, brands, models, vehicles, customers, and more. The interface features a sidebar for table navigation, a data grid, and forms with modal pop-ups for success/error messages. This guide explains how to set up and run the project on your local machine using XAMPP.
Contents of the Zip File

AutomobileCompanyDB.sql: Database schema and sample data (20 entries).
index.php: Main interface with sidebar, data grid, and forms.
crud.php: Handles create, read, update, and delete operations.
db_connect.php: Database connection configuration.
README.md: This setup guide.

Prerequisites

Operating System: Windows (macOS/Linux users, see notes in each step).
Internet Connection: To download XAMPP.
Software:
A web browser (e.g., Chrome, Firefox, Edge).
A text editor (e.g., Notepad, Visual Studio Code) for editing configuration files.


Zip File: automobile_project.zip containing the above files.

Step 1: Install XAMPP
XAMPP provides Apache (web server), MariaDB (database), and PHP, required to run this project.

Download XAMPP:

Visit https://www.apachefriends.org/download.html.
Download the latest version for your OS (e.g., XAMPP for Windows 8.2.12 with PHP 8.2.12).
Example: xampp-windows-x64-8.2.12-0-VS16-installer.exe for Windows.


Install XAMPP:

Windows:
Run the installer and allow User Account Control (UAC) if prompted.
Select components: Apache, MySQL/MariaDB, PHP, phpMyAdmin (default).
Install to C:\xampp (recommended).
Click “Next” and “Finish” to complete.


macOS:
Drag XAMPP.app to the Applications folder.


Linux:
Run sudo chmod +x xampp-linux-*-installer.run and sudo ./xampp-linux-*-installer.run.
Install to /opt/lampp.




Start XAMPP:

Open the XAMPP Control Panel:
Windows: Start menu > “XAMPP Control Panel” or C:\xampp\xampp-control.exe.
macOS: /Applications/XAMPP/xampp-control.app.
Linux: sudo /opt/lampp/xampp-control.


Click “Start” for Apache and MySQL. They should turn green.
If they don’t start, see Troubleshooting.



Note: Add firewall exceptions for Apache and MySQL if you have antivirus software.
Step 2: Configure Apache to Use Port 8080
To avoid conflicts with port 80, configure Apache to use port 8080.

Edit httpd.conf:

In XAMPP Control Panel, click “Config” next to Apache, select Apache (httpd.conf).
Or open:
Windows: C:\xampp\apache\conf\httpd.conf
macOS: /Applications/XAMPP/etc/httpd.conf
Linux: /opt/lampp/etc/httpd.conf


Find Listen 80 and change to Listen 8080.
Find ServerName localhost:80 and change to ServerName localhost:8080.
Save the file.


Restart Apache:

In XAMPP Control Panel, click “Stop” then “Start” for Apache.
Ensure it turns green. If not, see Troubleshooting.


Test Apache:

Open a browser and go to http://localhost:8080.
You should see the XAMPP dashboard. If not, see Troubleshooting.



Note: Access phpMyAdmin at http://localhost:8080/phpmyadmin.
Step 3: Import the Database
The project uses the AutomobileCompanyDB database, provided in AutomobileCompanyDB.sql.

Open phpMyAdmin:

In XAMPP Control Panel, click “Admin” next to MySQL or go to http://localhost:8080/phpmyadmin.
Log in with:
Username: root
Password: (blank, unless set during installation).




Create the Database:

Click “New” in the left sidebar.
Enter AutomobileCompanyDB as the database name and click “Create”.


Import the SQL File:

Select AutomobileCompanyDB in the left sidebar.
Click the “Import” tab.
Click “Choose File” and select AutomobileCompanyDB.sql from the unzipped folder.
Click “Go” or “Import”. A success message should appear.
The database includes tables (Company, Brand, Vehicle, etc.) and 20 sample entries (e.g., modelID 20–23).


Verify Data:

Click AutomobileCompanyDB in phpMyAdmin.
Check tables and data (e.g., Vehicle with VINs like JH4DC53008S000020).



Note: If the import fails, increase upload_max_filesize in C:\xampp\php\php.ini (e.g., to 20M) and restart Apache.
Step 4: Set Up Project Files
Place the PHP files in XAMPP’s htdocs folder.

Locate htdocs:

Open:
Windows: C:\xampp\htdocs
macOS: /Applications/XAMPP/htdocs
Linux: /opt/lampp/htdocs


Use the “Explorer” button in XAMPP Control Panel.


Create Project Folder:

Create a folder named automobile_project in htdocs (e.g., C:\xampp\htdocs\automobile_project).


Copy Files:

Unzip automobile_project.zip.
Copy index.php, crud.php, and db_connect.php to C:\xampp\htdocs\automobile_project.


Configure Database Connection:

Open db_connect.php in a text editor.
Verify it uses:$host = "localhost";
$username = "root";
$password = ""; // Update if you set a password
$database = "AutomobileCompanyDB";
$conn = new mysqli($host, $username, $password, $database);


If you set a MySQL root password in phpMyAdmin, update $password.


Test the Project:

Ensure Apache and MySQL are running.
Open http://localhost:8080/AutomobileWebApp/index.php in a browser.
You should see a sidebar with tables (e.g., Vehicle, Customer), a data grid, and a form.
Test features:
Navigate tables via the sidebar.
Add a new record (e.g., a Customer).
Update/delete records (e.g., Vehicle with VIN JH4DC53008S000020).
Check modal pop-ups for success/error messages (e.g., “Invalid Model ID”).





Step 5: Required Software

XAMPP: Includes Apache, MariaDB, PHP, phpMyAdmin.
Web Browser: To access the project.
No additional software is needed, as Tailwind CSS is loaded via CDN.

Troubleshooting

Apache Won’t Start:

Port Conflict: If 8080 is in use, change to 8081 in httpd.conf (Listen 8081, ServerName localhost:8081). Access http://localhost:8081.
Antivirus: Add exceptions for Apache/MySQL.
Logs: Check C:\xampp\apache\logs\error.log.


MySQL Won’t Start:

Change port in C:\xampp\mysql\bin\my.ini (port=3307 under [mysqld]). Restart MySQL.
Ensure no other MySQL instances are running.


Database Import Fails:

Verify AutomobileCompanyDB.sql is not corrupted.
Increase upload_max_filesize in C:\xampp\php\php.ini.


Project Errors:

Connection Error: Check db_connect.php credentials.
File Path: Ensure files are in C:\xampp\htdocs\automobile_project.
PHP Errors: Check C:\xampp\php\logs\php_error_log.


phpMyAdmin Access Denied:

Update C:\xampp\phpMyAdmin\config.inc.php ($cfg['Servers'][$i]['host'] = 'localhost';).



Security Notes

For local development only. Do not expose localhost:8080 to the internet.
If you set a root password, update db_connect.php.
Keep XAMPP updated for security patches.

Getting Started

Explore the GUI: View tables, add/edit/delete records, and verify modal messages.
Contact the developer with error details if needed.

For help, visit https://www.apachefriends.org or contact the developer.
Last Updated: May 28, 2025


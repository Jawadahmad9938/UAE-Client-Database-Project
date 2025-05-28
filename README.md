AutomobileCompanyDB
A relational database and PHP web application for managing an automobile company’s operations, developed for the CSC302 Database Management Systems course (Spring 2024–2025). This project implements a MySQL database with a user-friendly interface to handle vehicle inventory, sales, customers, dealers, and suppliers, demonstrating skills in database design and web development.
Project Overview
The AutomobileCompanyDB project simulates a database system for an automobile company (e.g., General Motors, Toyota). It includes a conceptual design (E-R model), a relational database schema, sample data, and a PHP web application for data management. The application features a sidebar for table navigation, forms for CRUD operations, and modal pop-ups for user feedback.
Features

Database Schema: 11 tables (e.g., Company, Brand, Vehicle, Customer) with primary keys, foreign keys, and realistic attributes (e.g., VINs, customer incomes).
Conceptual Design: E-R diagram (in progress) with entities, relationships, and cardinalities.
Web Application:
Sidebar navigation for all tables.
Forms to add, update, and delete records with validation (e.g., duplicate key errors).
Modal pop-ups for success/error messages.


Sample Data: 20 records across tables (to be expanded to 20 per table).
Technologies: MySQL, PHP, Tailwind CSS (via CDN), XAMPP.

Project Structure
AutomobileCompanyDB/
├── AutomobileCompanyDB.sql   # Database schema and data
├── index.php                 # Main interface with sidebar and forms
├── crud.php                  # Handles CRUD operations
├── db_connect.php            # Database connection configuration
├── er_diagram.pdf            # E-R diagram (to be added)
└── README.md                 # Client setup guide (for zip file)

Setup Instructions
To run this project locally:

Install XAMPP:

Download and install XAMPP from https://www.apachefriends.org.
Start Apache and MySQL in the XAMPP Control Panel.


Configure Apache:

Edit C:\xampp\apache\conf\httpd.conf:
Change Listen 80 to Listen 8080.
Change ServerName localhost:80 to ServerName localhost:8080.


Restart Apache.


Import Database:

Open http://localhost:8080/phpmyadmin.
Create a database named AutomobileCompanyDB.
Import AutomobileCompanyDB.sql via the “Import” tab.


Set Up Project Files:

Clone the repository: git clone https://github.com/yourusername/AutomobileCompanyDB.git.
Copy the project files to C:\xampp\htdocs\automobile_project.
Ensure db_connect.php has correct credentials (default: root, no password).


Run the Application:

Open http://localhost:8080/automobile_project/index.php in a browser.
Use the sidebar to navigate tables, add/edit/delete records, and test functionality.



Project Requirements Addressed

E-R Model: Designed entities (Vehicles, Brands, Models, etc.) and relationships (e.g., Dealer–Brand N:M). Diagram in progress.
Relational Model: MySQL schema with primary/foreign keys and constraints.
Data Population: 20 sample records (to be expanded).
Web Application: PHP interface with CRUD forms, validation, and modal feedback.
Queries and Reports: To be implemented (e.g., sales trends, top brands).

Future Improvements

Add 20 records per table (220 total) for robust query testing.
Implement required queries (e.g., sales trends, inventory times).
Enhance the application with record navigation and a “Reports” section.
Add cascade update/delete constraints.

License
This project is for educational purposes and not licensed for commercial use.
Acknowledgments

Instructor: Dr. Latif U. Khan
Course: CSC302 Database Management Systems, Spring 2024–2025

Last Updated: May 28, 2025

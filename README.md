# ERP System

This ERP system project is designed using HTML, CSS, JavaScript, PHP, and MySQL. It provides functionalities for managing customers, items, and generating various reports. The system includes features for inserting, updating, deleting, and searching data efficiently.

## Links
- **The source code and database files** - https://github.com/muneef-dev/erp-system
- **A video demonstrating the system** - https://drive.google.com/file/d/1DYOSaPuoIOgMNNssvpMprIN-TKbGAIx7/view?usp=sharing

## Table of Contents

1. [Assumptions](#assumptions)
2. [Installation](#installation)
3. [Setup](#setup)
4. [Usage](#usage)
5. [Features](#features)
6. [Additional Notes](#additional-notes)
7. [Contributing](#contributing)

## Assumptions

- **Technology Stack:** HTML, CSS, JavaScript, PHP, and MySQL.
- **Development Environment:** Local development environment (e.g., VS code, XAMPP).
- **Database:** MySQL database server.
- **Functionality:** The system provides functionalities for managing customers, items, and generating reports.
- **Form Validation:** Form validation is implemented to ensure data integrity.

## Installation

1. **Install a Local Development Environment:**
   - Download and install a local development environment such as [XAMPP](https://www.apachefriends.org/index.html). These packages include an Apache web server, MySQL database server, and PHP interpreter. Follow the installation instructions provided by the software.

2. **Create a Database:**
   - Access the MySQL administration panel (typically through phpMyAdmin) using your credentials.
   - Create a new database for your ERP system (e.g., `erp_system`).

3. **Clone or Download Project Files:**
   - If using Git, clone the project repository to your local development directory:

     ```bash
     git clone https://github.com/muneef-dev/erp-system.git
     ```

   - Alternatively, download the project files and extract them to a suitable location.

4. **Configure Database Connection:**
   - Locate the PHP file responsible for the database connection (e.g., `config.php`).
   - Update the database credentials (hostname, username, password, database name) to match your local environment settings.

     ```php
     // config.php
     $servername = "localhost";
     $username = "root";
     $password = "";
     $dbname = "erp_system";

     // Create connection
     $conn = new mysqli($servername, $username, $password, $dbname);

     // Check connection
     if ($conn->connect_error) {
         die("Connection failed: " . $conn->connect_error);
     }
     ```

5. **Start the Development Server:**
   - Start the Apache web server within your local development environment. This typically involves starting the appropriate service in the application's control panel.

6. **Access the Application:**
   - Open a web browser and navigate to the directory where you placed the project files (e.g., `http://localhost/erp-system/`).
   - You should now be able to access the ERP system's interface.

## Setup

1. **Open Your Web Browser:**
   - Navigate to `http://localhost/erp-system` to access the application.

2. **Login:**
   - Ensure that you have the default login credentials set up or add new user accounts if needed.

## Usage

### Task 1: Customer Management

- **Store/Register Customer Data with View List:**
  - A form captures customer details including Title, First Name, Last Name, Contact Number, and District.
  - Form validation ensures that required fields are filled correctly.
  - Upon successful registration, customer data is stored in the database.

### Task 2: Item Management

- **Store/Register Item Details with View List:**
  - A form allows adding item details such as Item Code, Item Name, Item Category, Item Subcategory, Quantity, and Unit Price.
  - Form validation is enforced to ensure accurate data entry.
  - Validated item information is saved in the database.
  - A dedicated page displays a list of registered items.

### Task 3: Reports

- **Invoice Report:**
  - Users can select a date range to search for invoices.
  - The report displays invoice details including:
    - Invoice Number/Date
    - Customer Information (Name, District)
    - Item Count
    - Invoice Amount

- **Invoice Item Report:**
  - Users can specify a date range to search for invoice items.
  - The report lists details for each item in the selected invoices, including:
    - Invoice Number/Date
    - Customer Name
    - Item Name with Code
    - Item Category and Subcategory
    - Item Unit Price

- **Item Report:**
  - This report provides an overview of items, displaying:
    - Item Name (without duplicates)
    - Item Category
    - Item Subcategory
    - Item Quantity

## Features

1. **Customer Management**
   - Form validation for customer details.
   - CRUD operations for customer data.

2. **Item Management**
   - Form validation for item details.
   - CRUD operations for item data.

3. **Reports**
   - Ability to generate and view detailed reports based on date ranges and criteria.

## Additional Notes

- Implement user authentication and authorization mechanisms for secure access control.
- Incorporate error handling and logging for efficient troubleshooting.
- Design the user interface with usability and visual appeal in mind.
- Enhance the project by adding functionalities like customer and item editing, deletion, and search capabilities within the reports section.

## Contributing

We welcome contributions! Please submit issues or pull requests. Ensure that your code adheres to the project's coding standards and includes appropriate documentation.

---

For any issues or further questions, please contact the project maintainer at muneef.dev@gmail.com
.

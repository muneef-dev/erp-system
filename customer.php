<?php
include 'config.php'; // Ensure db.php is included to establish the database connection

// Handle CRUD operations
$action = $_POST['action'] ?? $_GET['action'] ?? '';
$search = $_POST['search'] ?? '';

// Define function to escape output
function escape($value)
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

if ($action === 'add_edit') {
    $id = $_POST['customer_id'] ?? 0;
    $title = $_POST['title'] ?? '';
    $first_name = $_POST['first_name'] ?? '';
    $middle_name = $_POST['middle_name'] ?? '';
    $last_name = $_POST['last_name'] ?? '';
    $contact_no = $_POST['contact_no'] ?? '';
    $district_id = $_POST['district'] ?? '';

    if ($title && $first_name && $last_name && $contact_no && $district_id) {
        if ($id) {
            // Update existing customer
            $stmt = $con->prepare("UPDATE customer SET title=?, first_name=?, middle_name=?, last_name=?, contact_no=?, district=? WHERE id=?");
            $stmt->bind_param("ssssssi", $title, $first_name, $middle_name, $last_name, $contact_no, $district_id, $id);
        } else {
            // Add new customer
            $stmt = $con->prepare("INSERT INTO customer (title, first_name, middle_name, last_name, contact_no, district) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $title, $first_name, $middle_name, $last_name, $contact_no, $district_id);
        }

        $stmt->execute();
        $stmt->close();
        echo json_encode(['status' => 'success', 'message' => $id ? 'Customer updated successfully' : 'Customer added successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'All fields are required']);
    }
    exit;
}

if ($action === 'delete') {
    $id = $_POST['id'] ?? 0;
    if ($id) {
        $stmt = $con->prepare("DELETE FROM customer WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        echo json_encode(['status' => 'success', 'message' => 'Customer deleted']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid customer ID']);
    }
    exit;
}

if ($action === 'get') {
    $id = $_GET['id'] ?? 0;
    if ($id) {
        $stmt = $con->prepare("SELECT * FROM customer WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $customer = $result->fetch_assoc();
        echo json_encode($customer);
        $stmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid customer ID']);
    }
    exit;
}

// Fetch data from the customer table with district name
$search = $con->real_escape_string($search);
$sql = $search ? "
    SELECT customer.id, customer.title, customer.first_name, customer.middle_name, customer.last_name, customer.contact_no, district.district 
    FROM customer 
    JOIN district ON customer.district = district.id 
    WHERE customer.id LIKE '%$search%' OR customer.first_name LIKE '%$search%' OR customer.middle_name LIKE '%$search%' OR customer.last_name LIKE '%$search%'
" : "
    SELECT customer.id, customer.title, customer.first_name, customer.middle_name, customer.last_name, customer.contact_no, district.district 
    FROM customer 
    JOIN district ON customer.district = district.id
";

if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
    $result = $con->query($sql);
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . escape($row['id']) . "</td>";
        echo "<td>" . escape($row['title']) . "</td>";
        echo "<td>" . escape($row['first_name']) . "</td>";
        echo "<td>" . escape($row['middle_name']) . "</td>";
        echo "<td>" . escape($row['last_name']) . "</td>";
        echo "<td>" . escape($row['contact_no']) . "</td>";
        echo "<td>" . escape($row['district']) . "</td>";
        echo "<td>
                <button onclick=\"editCustomer(" . $row['id'] . ")\"><i class='lni lni-pencil'></i></button>
                <button onclick=\"confirmDelete(" . $row['id'] . ",'customer')\"><i class='lni lni-trash-can'></i></button>
              </td>";
        echo "</tr>";
    }
    $con->close();
    exit;
}

// Fetch data from the district table
$district_sql = "SELECT * FROM district WHERE active = 'Yes'";
$district_result = $con->query($district_sql);
$districts = [];
while ($row = $district_result->fetch_assoc()) {
    $districts[] = $row;
}
?>

<div class="text-center">
    <div class="header_fixed">
        <div class="header-top">
            <h1>Customer Management</h1>
            <form id="search-form">
                <input type="search" name="search" id="search-bar" placeholder="Search" value="<?= escape($search) ?>">
                <button type="button" onclick="searchCustomers()">Search</button>
                <button type="button" onclick="toggleForm()" id="addNewBtn"><i class="lni lni-plus"></i> Add New</button>
            </form>
        </div>
        <table id="customer-table">
            <thead>
                <tr>
                    <th>C Id.</th>
                    <th>Title</th>
                    <th>First Name</th>
                    <th>Middle Name</th>
                    <th>Last Name</th>
                    <th>Contact No</th>
                    <th>District</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <!-- Rows will be inserted here via JavaScript -->
            </tbody>
        </table>
    </div>
    <div class="form-container" id="customer-form-container" style="display: none;">
        <div class="header-top-form">
            <h1>Add/Edit Customer</h1>
            <div class="header-top-form-buttons">
                <button type="button" onclick="clearCustomerForm()">Clear</button>
                <button type="button" onclick="toggleForm()">View Customers</button>
            </div>
        </div>
        <hr>
        <form id="customer-form">
            <input type="hidden" id="customer_id" name="customer_id">
            <input type="hidden" name="action" value="add_edit">

            <div class="form-group">
                <label for="title">Title</label>
                <select id="title" name="title" required>
                    <option value="Mr">Mr</option>
                    <option value="Mrs">Mrs</option>
                    <option value="Miss">Miss</option>
                    <option value="Dr">Dr</option>
                </select>
            </div>
            <div class="form-group">
                <label for="first_name">First Name</label>
                <input type="text" id="first_name" name="first_name" required pattern="[A-Za-z\s]+" title="Only letters and spaces allowed">
            </div>
            <div class="form-group">
                <label for="middle_name">Middle Name</label>
                <input type="text" id="middle_name" name="middle_name" pattern="[A-Za-z\s]*" title="Only letters and spaces allowed">
            </div>
            <div class="form-group">
                <label for="last_name">Last Name</label>
                <input type="text" id="last_name" name="last_name" required pattern="[A-Za-z\s]+" title="Only letters and spaces allowed">
            </div>
            <div class="form-group">
                <label for="contact_no">Contact No</label>
                <input type="text" id="contact_no" name="contact_no" required pattern="\d+" title="Only numbers allowed">
            </div>
            <div class="form-group">
                <label for="district">District</label>
                <select id="district" name="district" required>
                    <?php foreach ($districts as $district) : ?>
                        <option value="<?= escape($district['id']) ?>"><?= escape($district['district']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit">Save</button>
        </form>
    </div>
</div>
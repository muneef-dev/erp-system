<?php
include 'config.php';

// Handle date range filter
$start_date = $_POST['start_date'] ?? '';
$end_date = $_POST['end_date'] ?? '';

// Construct SQL query with date range filtering
$sql = "SELECT i.invoice_no, i.date, c.first_name, c.last_name, c.district, i.item_count, i.amount 
        FROM invoice i
        JOIN customer c ON i.customer = c.id
        WHERE (i.date >= ? OR ? = '') 
        AND (i.date <= ? OR ? = '')";

$stmt = $con->prepare($sql);
$stmt->bind_param('ssss', $start_date, $start_date, $end_date, $end_date);


if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['invoice_no']) . "</td>";
        echo "<td>" . htmlspecialchars($row['date']) . "</td>";
        echo "<td>" . htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['district']) . "</td>";
        echo "<td>" . htmlspecialchars($row['item_count']) . "</td>";
        echo "<td>" . htmlspecialchars($row['amount']) . "</td>";
        echo "</tr>";
    }
    $stmt->close();
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
            <h1>Invoice Report</h1>
            <form id="search-form">
                <input type="date" name="start_date" id="start-date" placeholder="Start Date" class="date-field">
                <input type="date" name="end_date" id="end-date" placeholder="End Date" class="date-field">
                <button type="button" onclick="searchInvoices()">Search</button>
            </form>
        </div>
        <table id="invoice-table">
            <thead>
                <tr>
                    <th>Invoice No</th>
                    <th>Date</th>
                    <th>Customer</th>
                    <th>Customer District</th>
                    <th>Item Count</th>
                    <th>Invoice Amount</th>
                </tr>
            </thead>
            <tbody>
                <!-- Rows will be inserted here via JavaScript -->
            </tbody>
        </table>
    </div>
</div>

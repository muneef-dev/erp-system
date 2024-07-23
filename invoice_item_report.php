<?php
include 'config.php';

// Handle the date range search
$start_date = $_POST['start_date'] ?? '';
$end_date = $_POST['end_date'] ?? '';

// SQL Query to join item with category to fetch category names
$sql = "SELECT i.invoice_no, i.date AS date, c.first_name, c.last_name, it.item_name, it.item_code, ct.category AS category, im.unit_price
        FROM invoice i
        JOIN customer c ON i.customer = c.id
        JOIN invoice_master im ON i.invoice_no = im.invoice_no
        JOIN item it ON im.item_id = it.id
        JOIN item_category ct ON it.id = ct.id
        WHERE (i.date >= ? OR ? = '') 
        AND (i.date <= ? OR ? = '')
        ORDER BY i.date";

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
        echo "<td>" . htmlspecialchars($row['item_name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['item_code']) . "</td>";
        echo "<td>" . htmlspecialchars($row['category']) . "</td>";
        echo "<td>" . htmlspecialchars($row['unit_price']) . "</td>";
        echo "</tr>";
    }
    $stmt->close();
    $con->close();
    exit;
}


?>

<div class="text-center">
    <div class="header_fixed">
        <div class="header-top">
            <h1>Invoice Item Report</h1>
            <form id="search-form">
                <input type="date" name="start_date" id="start-date" placeholder="Start Date" class="date-field" required>
                <input type="date" name="end_date" id="end-date" placeholder="End Date" class="date-field" required>
                <button type="button" onclick="sendAjaxRequest('invoice_item_report.php', getInvoiceParams(),'invoice-table')">Search</button>
            </form>
        </div>
        <table id="invoice-table">
            <thead>
                <tr>
                    <th>Invoice Number</th>
                    <th>Invoiced Date</th>
                    <th>Customer Name</th>
                    <th>Item Name</th>
                    <th>Item Code</th>
                    <th>Item Category</th>
                    <th>Unit Price</th>
                </tr>
            </thead>
            <tbody>
                <!-- Rows will be inserted here via JavaScript -->
            </tbody>
        </table>
    </div>
</div>

<?php
$stmt->close();
$con->close();
?>

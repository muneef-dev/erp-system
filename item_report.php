<?php
include 'config.php';

// Fetch item data
$search = $_POST['search'] ?? '';
$search = $con->real_escape_string($search);

// Construct SQL query with joins
$sql = "SELECT i.item_name, c.category AS item_category, s.sub_category AS item_subcategory, i.quantity 
        FROM item i
        JOIN item_category c ON i.item_category = c.id
        JOIN item_subcategory s ON i.item_subcategory = s.id";

if ($search) {
    $sql .= " WHERE i.item_name LIKE '%$search%' 
              OR c.category LIKE '%$search%' 
              OR s.sub_category LIKE '%$search%'";
}

// Execute the query
$result = $con->query($sql);

if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['item_name']) . "</td>";
            echo "<td>" . htmlspecialchars($row['item_category']) . "</td>";
            echo "<td>" . htmlspecialchars($row['item_subcategory']) . "</td>";
            echo "<td>" . htmlspecialchars($row['quantity']) . "</td>";
            echo "</tr>";
        }
    } else {
        echo "Error: " . $con->error;
    }
    $con->close();
    exit;
}
?>

<div class="text-center">
    <div class="header_fixed">
        <div class="header-top">
            <h1>Item Report</h1>
            <form id="search-form">
                <input type="search" name="search" id="search-bar" placeholder="Search" value="<?= htmlspecialchars($search) ?>">
                <button type="button" onclick="searchItemReport()">Search</button>
            </form>
        </div>
        <table id="item-table">
            <thead>
                <tr>
                    <th>Item Name</th>
                    <th>Item Category</th>
                    <th>Item Subcategory</th>
                    <th>Quantity</th>
                </tr>
            </thead>
            <tbody>
                <!-- Rows will be inserted here via JavaScript -->
            </tbody>
        </table>
    </div>
</div>
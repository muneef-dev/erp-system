<?php
include 'config.php';

// Handle CRUD operations
$action = $_POST['action'] ?? $_GET['action'] ?? '';
$search = $_POST['search'] ?? '';

if ($action === 'add_edit') {
    $id = $_POST['item_id'] ?? 0;
    $item_code = $_POST['item_code'];
    $item_category = $_POST['item_category'];
    $item_subcategory = $_POST['item_subcategory'];
    $item_name = $_POST['item_name'];
    $quantity = $_POST['quantity'];
    $unit_price = $_POST['unit_price'];

    if ($id) {
        // Update existing item
        $stmt = $con->prepare("UPDATE item SET item_code=?, item_category=?, item_subcategory=?, item_name=?, quantity=?, unit_price=? WHERE id=?");
        $stmt->bind_param("ssssssi", $item_code, $item_category, $item_subcategory, $item_name, $quantity, $unit_price, $id);
    } else {
        // Add new item
        $stmt = $con->prepare("INSERT INTO item (item_code, item_category, item_subcategory, item_name, quantity, unit_price) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $item_code, $item_category, $item_subcategory, $item_name, $quantity, $unit_price);
    }

    $stmt->execute();
    $stmt->close();
    echo json_encode(['status' => 'success', 'message' => $id ? 'Item updated successfully' : 'Item added successfully']);
    exit;
}

if ($action === 'delete') {
    $id = $_POST['id'];
    $stmt = $con->prepare("DELETE FROM item WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    echo json_encode(['status' => 'success', 'message' => 'Item deleted']);
    exit;
}

if ($action === 'get') {
    $id = $_GET['id'];
    $stmt = $con->prepare("SELECT * FROM item WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $item = $result->fetch_assoc();
    echo json_encode($item);
    $stmt->close();
    exit;
}

// Fetch data from the item table with category and subcategory names
$search = $con->real_escape_string($search);
$sql = $search ? "SELECT item.*, item_category.category, item_subcategory.sub_category 
                  FROM item 
                  LEFT JOIN item_category ON item.item_category = item_category.id 
                  LEFT JOIN item_subcategory ON item.item_subcategory = item_subcategory.id 
                  WHERE item_code LIKE '%$search%' OR item_name LIKE '%$search%'"
              : "SELECT item.*, item_category.category, item_subcategory.sub_category 
                  FROM item 
                  LEFT JOIN item_category ON item.item_category = item_category.id 
                  LEFT JOIN item_subcategory ON item.item_subcategory = item_subcategory.id";

if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
    $result = $con->query($sql);
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['id']) . "</td>";
        echo "<td>" . htmlspecialchars($row['item_code']) . "</td>";
        echo "<td>" . htmlspecialchars($row['category']) . "</td>";
        echo "<td>" . htmlspecialchars($row['sub_category']) . "</td>";
        echo "<td>" . htmlspecialchars($row['item_name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['quantity']) . "</td>";
        echo "<td>" . htmlspecialchars($row['unit_price']) . "</td>";
        echo "<td>
                <button onclick=\"editItem(" . $row['id'] . ")\"><i class='lni lni-pencil'></i></button>
                <button onclick=\"confirmDelete(" . $row['id'] . ",'item')\"><i class='lni lni-trash-can'></i></button>
              </td>";
        echo "</tr>";
    }
    $con->close();
    exit;
}

// Fetch data from the item_category table
$item_category_sql = "SELECT * FROM item_category";
$item_category_result = $con->query($item_category_sql);
$item_categories = [];
while ($row = $item_category_result->fetch_assoc()) {
    $item_categories[] = $row;
}

// Fetch data from the item_subcategory table
$item_subcategory_sql = "SELECT * FROM item_subcategory";
$item_subcategory_result = $con->query($item_subcategory_sql);
$item_subcategories = [];
while ($row = $item_subcategory_result->fetch_assoc()) {
    $item_subcategories[] = $row;
}
?>

<div class="text-center">
    <div class="header_fixed">
        <div class="header-top">
            <h1>Item Management</h1>
            <form id="search-form">
                <input type="search" name="search" id="search-bar" placeholder="Search" value="<?= htmlspecialchars($search) ?>">
                <button type="button" onclick="loadTableData('item')">Search</button>
                <button type="button" onclick="toggleForm()"><i class="lni lni-plus"></i> Add New</button>
            </form>
        </div>
        <table id="item-table">
            <thead>
                <tr>
                    <th>Item ID</th>
                    <th>Item Code</th>
                    <th>Category</th>
                    <th>Subcategory</th>
                    <th>Item Name</th>
                    <th>Quantity</th>
                    <th>Unit Price</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <!-- Rows will be inserted here via JavaScript -->
            </tbody>
        </table>
    </div>
    <div class="form-container" id="item-form-container" style="display: none;">
        <div class="header-top-form">
            <h1>Add/Edit Item</h1>
            <div class="header-top-form-buttons">
                <button type="button" onclick="clearItemForm()">Clear</button>
                <button type="button" onclick="toggleForm()">View Items</button>
            </div>
        </div>
        <hr>
        <form id="item-form">
            <input type="hidden" id="item_id" name="item_id">
            <input type="hidden" name="action" value="add_edit">

            <div class="form-group">
                <label for="item_code">Item Code</label>
                <input type="text" id="item_code" name="item_code" class="uppercase-input" required pattern="[A-Za-z0-9]+" title="Item code must be alphanumeric." oninput="convertToUpper(this)">
            </div>

            <div class="form-group">
                <label for="item_category">Category</label>
                <select id="item_category" name="item_category" required>
                    <?php foreach ($item_categories as $item_category) : ?>
                        <option value="<?= htmlspecialchars($item_category['id']) ?>"><?= htmlspecialchars($item_category['category']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="item_subcategory">Subcategory</label>
                <select id="item_subcategory" name="item_subcategory">
                    <?php foreach ($item_subcategories as $item_subcategory) : ?>
                        <option value="<?= htmlspecialchars($item_subcategory['id']) ?>"><?= htmlspecialchars($item_subcategory['sub_category']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="item_name">Item Name</label>
                <input type="text" id="item_name" name="item_name" required pattern="[A-Za-z\s]+" title="Item name must contain only letters and spaces.">
            </div>

            <div class="form-group">
                <label for="quantity">Quantity</label>
                <input type="number" id="quantity" name="quantity" required min="1" title="Quantity must be a positive number.">
            </div>

            <div class="form-group">
                <label for="unit_price">Unit Price</label>
                <input type="number" step="0.01" id="unit_price" name="unit_price" required min="0.01" title="Unit price must be a positive number.">
            </div>

            <button type="submit">Save</button>
        </form>
    </div>
</div>
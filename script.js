document.addEventListener('DOMContentLoaded', function() {
    // Load initial content
    loadContent('customer');

    // Sidebar link click event listener
    document.querySelectorAll('.sidebar-link').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const page = this.getAttribute('data-page');
            if (page) {
                loadContent(page);
            }
        });
    });
});

function loadContent(page) {
    var xhr = new XMLHttpRequest();
    xhr.open('GET', page + '.php', true);
    xhr.onload = function() {
        document.getElementById('main-content').innerHTML = this.responseText;
        if (page === 'customer') {
            initCustomerFunctions();
            loadTableData('customer');
        } else if (page === 'item') {
            initItemFunctions();
            loadTableData('item');
        } else if (page === 'invoice_report') {
            initInvoiceReportFunctions();
        } else if (page === 'invoice_item_report') {
            initInvoiceItemReportFunctions();
        } else if (page === 'item_report') {
            initItemReportFunctions();
        }
    };
    xhr.send();
}

function initInvoiceReportFunctions() {
    document.getElementById('search-form').addEventListener('submit', function(e) {
        e.preventDefault();
        searchInvoices();
    });
}

function searchInvoices() {
    var startDate = document.getElementById('start-date').value;
    var endDate = document.getElementById('end-date').value;
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'invoice_report.php', true);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        const tableBody = document.querySelector('#invoice-table tbody');
        if (tableBody) {
            tableBody.innerHTML = this.responseText;
        }
    };
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.send('start_date=' + encodeURIComponent(startDate) + '&end_date=' + encodeURIComponent(endDate));
}

function initInvoiceItemReportFunctions() {
    document.getElementById('search-form').addEventListener('submit', function(e) {
        e.preventDefault();
        searchInvoiceItemReport();
    });
}

function searchInvoiceItemReport() {
    var startDate = document.getElementById('start-date').value;
    var endDate = document.getElementById('end-date').value;
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'invoice_item_report.php', true);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        const tableBody = document.querySelector('#invoice-table tbody');
        if (tableBody) {
            tableBody.innerHTML = this.responseText;
        }
    };
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.send('start_date=' + encodeURIComponent(startDate) + '&end_date=' + encodeURIComponent(endDate));
}

function initItemReportFunctions() {
    document.getElementById('search-form').addEventListener('submit', function(e) {
        e.preventDefault();
        searchItemReport();
    });
}

function searchItemReport() {
    var search = document.getElementById('search-bar').value;
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'item_report.php', true);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        const tableBody = document.querySelector('#item-table tbody');
        if (tableBody) {
            tableBody.innerHTML = this.responseText;
        }
    };
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.send('search=' + encodeURIComponent(search));
}


// Initialize customer functions
function initCustomerFunctions() {
    document.getElementById('customer-form').addEventListener('submit', function(e) {
        e.preventDefault(); // Prevent default form submission
        submitCustomerForm();
    });

    document.getElementById('search-form').addEventListener('submit', function(e) {
        e.preventDefault(); // Prevent default form submission
        loadTableData('customer');
    });

    // Initialize event listeners for customer operations
    document.querySelectorAll('.btn-edit-customer').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            editCustomer(id);
        });
    });

    document.querySelectorAll('.btn-delete-customer').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            confirmDelete(id, 'customer');
        });
    });
}

// Submit customer form
function submitCustomerForm() {
    var formData = new FormData(document.getElementById('customer-form'));
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'customer.php', true);
    xhr.onload = function() {
        var response = JSON.parse(this.responseText);
        if (response.status === 'success') {
            showPopupMessage(response.message);
            clearCustomerForm();
            loadTableData('customer');
        } else {
            showPopupMessage(response.message, 'error');
        }
    };
    xhr.send(formData);
}

// Clear customer form
function clearCustomerForm() {
    document.getElementById('customer-form').reset();
    document.getElementById('customer_id').value = ''; // Clear hidden customer_id field
}

// Load customer or item table data
function loadTableData(page) {
    var xhr = new XMLHttpRequest();
    xhr.open('POST', page + '.php', true);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        const tableBody = document.querySelector('#main-content tbody');
        if (tableBody) {
            tableBody.innerHTML = this.responseText;
        }
    };
    var search = document.getElementById('search-bar').value;
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.send('search=' + encodeURIComponent(search));
}

// Confirm delete operation
function confirmDelete(id, type) {
    var popup = document.createElement('div');
    popup.className = 'confirm-popup';
    popup.innerHTML = `
        <p>Are you sure you want to delete this ${type}?</p>
        <button onclick="deleteItem(${id}, '${type}')">Yes</button>
        <button onclick="hidePopup()">No</button>
    `;
    document.body.appendChild(popup);
    popup.style.display = 'block';
}

// Delete item or customer
function deleteItem(id, type) {
    var xhr = new XMLHttpRequest();
    xhr.open('POST', type + '.php', true); // Adjust URL based on type
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        var response = JSON.parse(this.responseText);
        if (response.status === 'success') {
            showPopupMessage(response.message);
            loadTableData(type);
            hidePopup();
        } else {
            showPopupMessage(response.message, 'error');
        }
    };
    xhr.send('action=delete&id=' + id);
}

// Edit customer
function editCustomer(id) {
    var xhr = new XMLHttpRequest();
    xhr.open('GET', 'customer.php?action=get&id=' + id, true);
    xhr.onload = function() {
        var customer = JSON.parse(this.responseText);
        if (customer) {
            document.getElementById('customer_id').value = customer.id;
            document.getElementById('title').value = customer.title;
            document.getElementById('first_name').value = customer.first_name;
            document.getElementById('middle_name').value = customer.middle_name;
            document.getElementById('last_name').value = customer.last_name;
            document.getElementById('contact_no').value = customer.contact_no;
            document.getElementById('district').value = customer.district;
            // Other fields as necessary
            toggleForm();
        }
    };
    xhr.send();
}

// Initialize item functions
function initItemFunctions() {
    document.getElementById('item-form').addEventListener('submit', function(e) {
        e.preventDefault(); // Prevent default form submission
        submitItemForm();
    });

    document.getElementById('search-form').addEventListener('submit', function(e) {
        e.preventDefault(); // Prevent default form submission
        loadTableData('item');
    });

    // Initialize event listeners for item operations
    document.querySelectorAll('.btn-edit-item').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            editItem(id);
        });
    });

    document.querySelectorAll('.btn-delete-item').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            confirmDelete(id, 'item');
        });
    });
}

// Submit item form
function submitItemForm() {
    var formData = new FormData(document.getElementById('item-form'));
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'item.php', true);
    xhr.onload = function() {
        var response = JSON.parse(this.responseText);
        if (response.status === 'success') {
            showPopupMessage(response.message);
            clearItemForm();
            loadTableData('item');
        } else {
            showPopupMessage(response.message, 'error');
        }
    };
    xhr.send(formData);
}

// Clear item form
function clearItemForm() {
    document.getElementById('item-form').reset();
    document.getElementById('item_id').value = ''; // Clear hidden item_id field
}

// Edit item
function editItem(id) {
    var xhr = new XMLHttpRequest();
    xhr.open('GET', 'item.php?action=get&id=' + id, true);
    xhr.onload = function() {
        var item = JSON.parse(this.responseText);
        if (item) {
            document.getElementById('item_id').value = item.id;
            document.getElementById('item_code').value = item.item_code;
            document.getElementById('item_category').value = item.item_category;
            document.getElementById('item_subcategory').value = item.item_subcategory;
            document.getElementById('item_name').value = item.item_name;
            document.getElementById('quantity').value = item.quantity;
            document.getElementById('unit_price').value = item.unit_price;
            toggleForm();
        }
    };
    xhr.send();
}

// Show popup message
function showPopupMessage(message, type = 'success') {
    var popup = document.createElement('div');
    popup.className = 'popup-message ' + type;
    popup.textContent = message;
    document.body.appendChild(popup);
    popup.style.display = 'block';
    setTimeout(function() {
        popup.style.display = 'none';
        document.body.removeChild(popup);
    }, 3000);
}

// Hide popup
function hidePopup() {
    var popup = document.querySelector('.confirm-popup');
    if (popup) {
        popup.style.display = 'none';
        document.body.removeChild(popup);
    }
}

// Toggle form visibility
function toggleForm() {
    
    var table = document.querySelector('.header_fixed');
    var form = document.querySelector('.form-container');
    if (table.style.display === 'none') {
        table.style.display = 'block';
        form.style.display = 'none';
    } else {
        table.style.display = 'none';
        form.style.display = 'block';
    }
    table = null;
    form= null;
}

// Function to convert input to uppercase
function convertToUpper(input) {
    input.value = input.value.toUpperCase();
}

// Function to handle form submission
function handleFormSubmit(event) {
    const itemCodeInput = document.getElementById('item_code');
    convertToUpper(itemCodeInput);
}
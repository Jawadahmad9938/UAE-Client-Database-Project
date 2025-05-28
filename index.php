<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'db_connect.php';

// Tables and their columns mapping
$tables = [
    'Company'      => ['companyID', 'name'],
    'Brand'        => ['brandID', 'name', 'companyID'],
    'Model'        => ['modelID', 'name', 'brandID'],
    'Plant'        => ['plantID', 'location'],
    'Supplier'     => ['supplierID', 'name'],
    'Part'         => ['partID', 'name', 'supplierID', 'plantID'],
    'Dealer'       => ['dealerID', 'name', 'location'],
    'Customer'     => ['customerID', 'name', 'address', 'phone', 'gender', 'income'],
    'Vehicle'      => ['VIN', 'modelID', 'color', 'engine', 'transmission', 'bodyStyle', 'plantID', 'dealerID', 'inventoryDate', 'status', 'customerID', 'saleDate', 'price'],
    'DealerBrand'  => ['dealerID', 'brandID'],
    'ModelPart'    => ['modelID', 'partID']
];

// Check which table to display (default to Vehicle)
$table = isset($_GET['table']) ? $_GET['table'] : 'Vehicle';
if (!isset($tables[$table])) {
    $table = 'Vehicle';
}
$columns = $tables[$table];

// Fetch data for the selected table
$data = [];
$query = "SELECT * FROM `$table`";
if (isset($conn) && $query) {
    $result = $conn->query($query);
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    }
}

// Fetch options for foreign keys
$dropdowns = [];
if ($table === 'Vehicle') {
    $dropdowns['modelID'] = $conn->query("SELECT modelID, name FROM Model")->fetch_all(MYSQLI_ASSOC);
    $dropdowns['plantID'] = $conn->query("SELECT plantID, location FROM Plant")->fetch_all(MYSQLI_ASSOC);
    $dropdowns['dealerID'] = $conn->query("SELECT dealerID, name FROM Dealer")->fetch_all(MYSQLI_ASSOC);
    $dropdowns['customerID'] = $conn->query("SELECT customerID, name FROM Customer")->fetch_all(MYSQLI_ASSOC);
} elseif ($table === 'Brand') {
    $dropdowns['companyID'] = $conn->query("SELECT companyID, name FROM Company")->fetch_all(MYSQLI_ASSOC);
} elseif ($table === 'Part') {
    $dropdowns['supplierID'] = $conn->query("SELECT supplierID, name FROM Supplier")->fetch_all(MYSQLI_ASSOC);
    $dropdowns['plantID'] = $conn->query("SELECT plantID, location FROM Plant")->fetch_all(MYSQLI_ASSOC);
} elseif ($table === 'DealerBrand') {
    $dropdowns['dealerID'] = $conn->query("SELECT dealerID, name FROM Dealer")->fetch_all(MYSQLI_ASSOC);
    $dropdowns['brandID'] = $conn->query("SELECT brandID, name FROM Brand")->fetch_all(MYSQLI_ASSOC);
} elseif ($table === 'ModelPart') {
    $dropdowns['modelID'] = $conn->query("SELECT modelID, name FROM Model")->fetch_all(MYSQLI_ASSOC);
    $dropdowns['partID'] = $conn->query("SELECT partID, name FROM Part")->fetch_all(MYSQLI_ASSOC);
}

// Get message from URL
$msg = isset($_GET['msg']) ? htmlspecialchars($_GET['msg']) : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Automobile Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background-color: #f4f6f9;
        }
        .sidebar {
            width: 250px;
            background-color: #1f2937;
            color: white;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            overflow-y: auto;
            transition: transform 0.3s ease;
            z-index: 1000;
        }
        .sidebar-hidden {
            transform: translateX(-250px);
        }
        .sidebar a {
            display: block;
            padding: 1rem;
            color: white;
            text-decoration: none;
            border-bottom: 1px solid #374151;
        }
        .sidebar a:hover {
            background-color: #374151;
        }
        .sidebar a.active {
            background-color: #4b5563;
            font-weight: bold;
        }
        .content {
            margin-left: 250px;
            padding: 1rem;
            transition: margin-left 0.3s ease;
        }
        .content-full {
            margin-left: 0;
        }
        .grid-container {
            display: grid;
            grid-template-columns: 3fr 1fr;
            gap: 1rem;
            height: calc(100vh - 2rem);
        }
        .data-grid {
            overflow-x: auto;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            padding: 1rem;
            background-color: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .form-container {
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            padding: 1rem;
            background-color: #f9fafb;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 0.75rem;
            border: 1px solid #e5e7eb;
            text-align: left;
        }
        th {
            background-color: #e5e7eb;
            font-weight: 600;
        }
        tr:hover {
            background-color: #f3f4f6;
            cursor: pointer;
        }
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            z-index: 2000;
            align-items: center;
            justify-content: center;
        }
        .modal-content {
            background-color: white;
            padding: 2rem;
            border-radius: 0.5rem;
            max-width: 500px;
            width: 90%;
            position: relative;
        }
        .modal-success {
            border-left: 5px solid #10b981;
        }
        .modal-error {
            border-left: 5px solid #ef4444;
        }
        .modal-close {
            position: absolute;
            top: 0.5rem;
            right: 0.5rem;
            cursor: pointer;
            font-size: 1.5rem;
        }
        .toggle-btn {
            position: fixed;
            top: 1rem;
            left: 1rem;
            z-index: 1100;
            display: none;
        }
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-250px);
            }
            .sidebar-open {
                transform: translateX(0);
            }
            .content {
                margin-left: 0;
            }
            .toggle-btn {
                display: block;
            }
            .grid-container {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="p-4 text-xl font-bold border-b border-gray-700">Automobile Management</div>
        <?php foreach (array_keys($tables) as $t): ?>
            <a href="?table=<?php echo htmlspecialchars($t); ?>" class="<?php echo $t === $table ? 'active' : ''; ?>">
                <?php echo htmlspecialchars($t); ?>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- Toggle Button for Mobile -->
    <button class="toggle-btn bg-gray-800 text-white px-4 py-2 rounded" onclick="toggleSidebar()">☰</button>

    <!-- Main Content -->
    <div class="content" id="content">
        <div class="grid-container">
            <!-- Data Grid -->
            <div class="data-grid">
                <h3 class="text-2xl font-semibold mb-4 text-gray-800"><?php echo htmlspecialchars($table); ?> List</h3>
                <?php if (!empty($data)): ?>
                    <table>
                        <thead>
                            <tr>
                                <?php foreach ($columns as $col): ?>
                                    <th><?php echo htmlspecialchars($col); ?></th>
                                <?php endforeach; ?>
                                    <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data as $row): ?>
                                <tr onclick="populateForm('<?php echo htmlspecialchars(json_encode($row), ENT_QUOTES); ?>')">
                                    <?php foreach ($columns as $col): ?>
                                        <td><?php echo htmlspecialchars($row[$col] ?? ''); ?></td>
                                    <?php endforeach; ?>
                                    <td>
                                        <button onclick="event.stopPropagation(); deleteRecord('<?php echo htmlspecialchars($row[$columns[0]]); ?>', '<?php echo htmlspecialchars($table); ?>')" class="text-red-600 hover:underline">Delete</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="text-gray-600">No data found.</p>
                <?php endif; ?>
            </div>

            <!-- Form for Add/Edit -->
            <div class="form-container">
                <h3 class="text-2xl font-semibold mb-4 text-gray-800"><?php echo htmlspecialchars($table); ?> Form</h3>
                <form id="dataForm" action="crud.php" method="POST">
                    <input type="hidden" name="table" value="<?php echo htmlspecialchars($table); ?>">
                    <?php foreach ($columns as $col): ?>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700"><?php echo htmlspecialchars($col); ?>:</label>
                            <?php if ($col === $columns[0]): ?>
                                <input type="text" name="<?php echo htmlspecialchars($col); ?>" id="<?php echo htmlspecialchars($col); ?>" class="w-full p-2 border rounded-md focus:ring-2 focus:ring-blue-500" required>
                            <?php elseif (isset($dropdowns[$col])): ?>
                                <select name="<?php echo htmlspecialchars($col); ?>" id="<?php echo htmlspecialchars($col); ?>" class="w-full p-2 border rounded-md focus:ring-2 focus:ring-blue-500">
                                    <option value="">Select...</option>
                                    <?php foreach ($dropdowns[$col] as $option): ?>
                                        <option value="<?php echo htmlspecialchars($option[$col]); ?>">
                                            <?php echo htmlspecialchars($option[$col] . ' - ' . $option[array_keys($option)[1]]); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            <?php elseif (in_array($col, ['inventoryDate', 'saleDate'])): ?>
                                <input type="date" name="<?php echo htmlspecialchars($col); ?>" id="<?php echo htmlspecialchars($col); ?>" class="w-full p-2 border rounded-md focus:ring-2 focus:ring-blue-500">
                            <?php elseif (in_array($col, ['modelID', 'plantID', 'dealerID', 'customerID', 'companyID', 'brandID', 'supplierID', 'partID', 'year', 'price', 'income'])): ?>
                                <input type="number" name="<?php echo htmlspecialchars($col); ?>" id="<?php echo htmlspecialchars($col); ?>" class="w-full p-2 border rounded-md focus:ring-2 focus:ring-blue-500" <?php echo $col === 'price' ? 'step="0.01"' : ''; ?>>
                            <?php elseif ($col === 'status'): ?>
                                <select name="<?php echo htmlspecialchars($col); ?>" id="<?php echo htmlspecialchars($col); ?>" class="w-full p-2 border rounded-md focus:ring-2 focus:ring-blue-500">
                                    <option value="Available">Available</option>
                                    <option value="Sold">Sold</option>
                                </select>
                            <?php else: ?>
                                <input type="text" name="<?php echo htmlspecialchars($col); ?>" id="<?php echo htmlspecialchars($col); ?>" class="w-full p-2 border rounded-md focus:ring-2 focus:ring-blue-500">
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                    <div class="flex space-x-4">
                        <button type="submit" name="action" value="Add" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition">Add</button>
                        <button type="submit" name="action" value="Update" id="updateBtn" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition" disabled>Update</button>
                        <button type="button" onclick="resetForm()" class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition">Reset</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal for Messages -->
    <div class="modal" id="messageModal">
        <div class="modal-content <?php echo strpos($msg, 'Error') === 0 ? 'modal-error' : 'modal-success'; ?>">
            <span class="modal-close" onclick="closeModal()">&times;</span>
            <p id="modalMessage"><?php echo $msg; ?></p>
        </div>
    </div>

    <script>
        let primaryKeyValue = '';
        function populateForm(rowData) {
            const data = JSON.parse(rowData);
            <?php foreach ($columns as $col): ?>
                const <?php echo htmlspecialchars($col); ?> = document.getElementById('<?php echo htmlspecialchars($col); ?>');
                <?php echo htmlspecialchars($col); ?>.value = data['<?php echo htmlspecialchars($col); ?>'] || '';
            <?php endforeach; ?>
            primaryKeyValue = data['<?php echo htmlspecialchars($columns[0]); ?>'];
            document.getElementById('<?php echo htmlspecialchars($columns[0]); ?>').setAttribute('readonly', true);
            document.getElementById('updateBtn').disabled = false;
        }

        function deleteRecord(id, table) {
            if (confirm('Are you sure you want to delete this record?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'crud.php';
                form.innerHTML = `
                    <input type="hidden" name="table" value="${table}">
                    <input type="hidden" name="<?php echo htmlspecialchars($columns[0]); ?>" value="${id}">
                    <input type="hidden" name="action" value="Delete">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        function resetForm() {
            document.getElementById('dataForm').reset();
            document.getElementById('<?php echo htmlspecialchars($columns[0]); ?>').removeAttribute('readonly');
            document.getElementById('updateBtn').disabled = true;
        }

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const content = document.getElementById('content');
            sidebar.classList.toggle('sidebar-open');
            content.classList.toggle('content-full');
        }

        function closeModal() {
            document.getElementById('messageModal').style.display = 'none';
        }

        // Show modal if there is a message
        <?php if ($msg): ?>
            document.getElementById('messageModal').style.display = 'flex';
        <?php endif; ?>
    </script>
</body>
</html>

<?php
if (isset($conn)) {
    $conn->close();
}
?>
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

// Get POST data
$table = $_POST['table'] ?? '';
$action = $_POST['action'] ?? '';

if (!$table || !isset($tables[$table])) {
    header("Location: index.php?table=Vehicle&msg=" . urlencode("Invalid table selected"));
    exit;
}
$cols = $tables[$table];

// Validate foreign keys for Vehicle
if ($table === 'Vehicle' && in_array($action, ['Add', 'Update'])) {
    // Check modelID
    if (!empty($_POST['modelID'])) {
        $stmt = $conn->prepare("SELECT modelID FROM Model WHERE modelID = ?");
        $stmt->bind_param("i", $_POST['modelID']);
        $stmt->execute();
        if ($stmt->get_result()->num_rows === 0) {
            header("Location: index.php?table=$table&msg=" . urlencode("Invalid Model ID: The selected model does not exist."));
            exit;
        }
        $stmt->close();
    }
    // Check plantID
    if (!empty($_POST['plantID'])) {
        $stmt = $conn->prepare("SELECT plantID FROM Plant WHERE plantID = ?");
        $stmt->bind_param("i", $_POST['plantID']);
        $stmt->execute();
        if ($stmt->get_result()->num_rows === 0) {
            header("Location: index.php?table=$table&msg=" . urlencode("Invalid Plant ID: The selected plant does not exist."));
            exit;
        }
        $stmt->close();
    }
    // Check dealerID
    if (!empty($_POST['dealerID'])) {
        $stmt = $conn->prepare("SELECT dealerID FROM Dealer WHERE dealerID = ?");
        $stmt->bind_param("i", $_POST['dealerID']);
        $stmt->execute();
        if ($stmt->get_result()->num_rows === 0) {
            header("Location: index.php?table=$table&msg=" . urlencode("Invalid Dealer ID: The selected dealer does not exist."));
            exit;
        }
        $stmt->close();
    }
    // Check customerID if status is 'sold'
    if (strtolower($_POST['status'] ?? '') === 'sold' && !empty($_POST['customerID'])) {
        $stmt = $conn->prepare("SELECT customerID FROM Customer WHERE customerID = ?");
        $stmt->bind_param("i", $_POST['customerID']);
        $stmt->execute();
        if ($stmt->get_result()->num_rows === 0) {
            header("Location: index.php?table=$table&msg=" . urlencode("Invalid Customer ID: The selected customer does not exist."));
            exit;
        }
        $stmt->close();
    }
    // Clear customerID and saleDate if not sold
    if (strtolower($_POST['status'] ?? '') !== 'sold') {
        $_POST['customerID'] = null;
        $_POST['saleDate'] = null;
    }
}

// Build and execute SQL with prepared statements
switch ($action) {
    case 'Add':
        $placeholders = implode(',', array_fill(0, count($cols), '?'));
        $sql = "INSERT INTO `$table` (" . implode(',', $cols) . ") VALUES ($placeholders)";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            header("Location: index.php?table=$table&msg=" . urlencode("Database error: Failed to prepare the insert statement."));
            exit;
        }
        $types = str_repeat('s', count($cols)); // Treat all as strings for simplicity
        $params = array_map(fn($c) => $_POST[$c] ?? null, $cols);
        $stmt->bind_param($types, ...$params);
        break;

    case 'Update':
        $key = $cols[0];
        $sets = array_map(fn($c) => "`$c` = ?", array_slice($cols, 1));
        $sql = "UPDATE `$table` SET " . implode(',', $sets) . " WHERE `$key` = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            header("Location: index.php?table=$table&msg=" . urlencode("Database error: Failed to prepare the update statement."));
            exit;
        }
        $types = str_repeat('s', count($cols)); // Include key in types
        $params = array_map(fn($c) => $_POST[$c] ?? null, array_slice($cols, 1));
        $params[] = $_POST[$key] ?? null; // Add key value
        $stmt->bind_param($types, ...$params);
        break;

    case 'Delete':
        $key = $cols[0];
        $sql = "DELETE FROM `$table` WHERE `$key` = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            header("Location: index.php?table=$table&msg=" . urlencode("Database error: Failed to prepare the delete statement."));
            exit;
        }
        $stmt->bind_param('s', $_POST[$key]);
        break;

    default:
        header("Location: index.php?table=$table&msg=" . urlencode("Invalid action specified."));
        exit;
}

// Execute query and handle specific errors
try {
    if ($stmt->execute()) {
        $msg = 'Operation completed successfully.';
    } else {
        $msg = 'Error: Operation failed. Please try again.';
    }
} catch (mysqli_sql_exception $e) {
    $errorCode = $e->getCode();
    if ($errorCode == 1452) { // Foreign key constraint violation
        $msg = 'Error: Cannot perform this operation due to related records in another table.';
    } elseif ($errorCode == 1062) { // Duplicate entry
        $msg = 'Error: A record with this ID already exists.';
    } else {
        $msg = 'Error: ' . $e->getMessage();
    }
}
$stmt->close();
$conn->close();
header("Location: index.php?table=$table&msg=" . urlencode($msg));
exit;
?>
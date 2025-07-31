<?php
include('header.php');
include('sidebar.php');
include('connect.php');

// Initialize message variable
$message = '';

// DELETE logic
if (isset($_GET['delete_id'])) {
    $deleteId = intval($_GET['delete_id']);
    $deleteQuery = "DELETE FROM assets WHERE asset_id = $deleteId";
    if ($conn->query($deleteQuery)) {
        $message = 'Asset successfully deleted.';
    } else {
        $message = 'Error deleting asset: ' . $conn->error;
    }
}

// UPDATE logic (inline)
if (isset($_POST['save_id'])) {
    $id = intval($_POST['save_id']);
    $asset_name = $conn->real_escape_string($_POST['asset_name']);
    $asset_type = $conn->real_escape_string($_POST['asset_type']);
    $serial_number = $conn->real_escape_string($_POST['serial_number']);
    $purchase_date = $conn->real_escape_string($_POST['purchase_date']);
    $purchase_cost = floatval($_POST['purchase_cost']);
    $status = intval($_POST['status']);
    $location = intval($_POST['location']);
    // Assuming 'modified_by' is a fixed value for now, or retrieved from a session
    $modified_by = 1; // You might want to get this from a user session
    $modified_at = date('Y-m-d H:i:s');

    $updateQuery = "UPDATE assets SET
        asset_name = '$asset_name',
        asset_type = '$asset_type',
        serial_number = '$serial_number',
        purchase_date = '$purchase_date',
        purchase_cost = $purchase_cost,
        status = $status,
        location = $location,
        modified_by = $modified_by,
        modified_at = '$modified_at'
      WHERE asset_id = $id";
    
    if ($conn->query($updateQuery)) {
        $message = 'Asset successfully updated.';
    } else {
        $message = 'Error updating asset: ' . $conn->error;
    }
}

// ADD logic
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_asset"])) {
    $asset_name = htmlspecialchars(trim($_POST["asset_name"]));
    $asset_type = htmlspecialchars(trim($_POST["asset_type"]));
    $serial_number = htmlspecialchars(trim($_POST["serial_number"]));
    $purchase_date = htmlspecialchars(trim($_POST["purchase_date"]));
    $purchase_cost = isset($_POST["purchase_cost"]) ? floatval($_POST["purchase_cost"]) : 0.0;
    $status = isset($_POST["status"]) ? intval($_POST["status"]) : 0;
    $location_id = isset($_POST["location"]) ? intval($_POST["location"]) : 0;

    // Initialize response array
    $response = ['msg' => '', 'msg_code' => 1];

    // Validate input
    if (empty($asset_name) || empty($asset_type) || empty($serial_number) || empty($purchase_date) || $purchase_cost <= 0 || $location_id === 0) {
        $response['msg'] = 'All fields are required and purchase cost must be greater than zero.';
    } else {
        // Check for duplicate asset
        $check_q = "SELECT * FROM assets WHERE serial_number = '".$conn->real_escape_string($serial_number)."'";
        $check_r = $conn->query($check_q);

        if ($check_r && $check_r->num_rows > 0) {
            $response['msg'] = 'Asset with this serial number already exists. Choose a different serial number.';
        } else {
            // Insert new asset
            $insert_q = "INSERT INTO `assets`(`asset_name`, `asset_type`, `serial_number`, `purchase_date`, `purchase_cost`, `status`, `location`, `created_by`, `created_at`, `modified_by`, `modified_at`) 
                        VALUES ('".$conn->real_escape_string($asset_name)."', '".$conn->real_escape_string($asset_type)."', '".$conn->real_escape_string($serial_number)."', '$purchase_date', $purchase_cost, $status, $location_id, 1, NOW(), 1, NOW())";
            $insert_r = $conn->query($insert_q);

            if ($insert_r) {
                $response['msg'] = 'Asset successfully inserted.';
                $response['msg_code'] = 0; // Success
            } else {
                $response['msg'] = 'Error inserting asset: ' . $conn->error;
            }
        }
    }

    // Show response message
    echo "<script>alert('".$response['msg']."');</script>";
}

// Fetch locations for dropdown
$locations = [];
$location_q = "SELECT id, location_name FROM location"; // Adjust the table name if necessary
$location_r = $conn->query($location_q);
if ($location_r) {
    while ($row = $location_r->fetch_assoc()) {
        $locations[] = $row;
    }
}

// Fetch assets for display
$query = "SELECT * FROM assets";
$result = $conn->query($query);

if (!$result) {
    // Handle query error
    echo "<script>alert('Error fetching assets: " . $conn->error . "');</script>";
    $assets = []; // Set to an empty array to avoid warnings
} else {
    $assets = $result->fetch_all(MYSQLI_ASSOC); // Fetch all assets as an associative array
}
?>

<!-- Page Wrapper -->
<div class="page-wrapper">
    
    <!-- Page Content -->
    <div class="content container-fluid pb-0">
    
        <!-- Page Header -->
        <div class="page-header">
            <div class="row">
                <div class="col-sm-12">
                    <h3 class="page-title">Assets</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- /Page Header -->

        <!-- Display messages -->
        <?php if ($message): ?>
            <div class="alert alert-info">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-12 d-flex">
                <div class="card card-table flex-fill">
                    <div class="card-header">
                        <h3 class="card-title mb-0">Assets</h3>
                        <button id="addAssetBtn" class="btn btn-primary float-right">+ Add New Asset</button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-nowrap custom-table mb-0">
                                <thead>
                                    <tr>
                                        <th>S#</th>
                                        <th>Asset Name</th>
                                        <th>Asset Type</th>
                                        <th>Serial Number</th>
                                        <th>Purchase Date</th>
                                        <th>Purchase Cost</th>
                                        <th>Status</th>
                                        <th>Location</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach ($assets as $key => $asset) {
                                        ?>
                                        <tr>
                                            <td><?php echo $key + 1; ?></td>
                                            <td>
                                                <form method="POST" action="assets.php">
                                                    <input type="hidden" name="save_id" value="<?php echo $asset['asset_id']; ?>">
                                                    <input type="text" name="asset_name" value="<?php echo htmlspecialchars($asset['asset_name']); ?>" required>
                                            </td>
                                            <td>
                                                <input type="text" name="asset_type" value="<?php echo htmlspecialchars($asset['asset_type']); ?>" required>
                                            </td>
                                            <td>
                                                <input type="text" name="serial_number" value="<?php echo htmlspecialchars($asset['serial_number']); ?>" required>
                                            </td>
                                            <td>
                                                <input type="datetime-local" name="purchase_date" value="<?php echo date('Y-m-d\TH:i', strtotime($asset['purchase_date'])); ?>" required>
                                            </td>
                                            <td>
                                                <input type="number" name="purchase_cost" value="<?php echo htmlspecialchars($asset['purchase_cost']); ?>" required>
                                            </td>
                                            <td>
                                                <input type="number" name="status" value="<?php echo htmlspecialchars($asset['status']); ?>" required>
                                            </td>
                                            <td>
                                                <input type="number" name="location" value="<?php echo htmlspecialchars($asset['location']); ?>" required>
                                            </td>
                                            <td>
                                                <input type="submit" value="Update" class="btn btn-primary">
                                                <a href="assets.php?delete_id=<?php echo $asset['asset_id']; ?>" class="btn btn-danger" onclick="return confirm('Delete this asset?')">Delete</a>
                                            </td>
                                                </form>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add New Asset Form (Initially Hidden) -->
        <div class="row" id="addAssetForm" style="display: none;">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Add New Asset</h3>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="assets.php">
                            <input type="hidden" name="add_asset" value="1">
                            <div class="form-group">
                                <label>Asset Name:</label>
                                <input type="text" name="asset_name" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Asset Type:</label>
                                <input type="text" name="asset_type" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Serial Number:</label>
                                <input type="text" name="serial_number" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Purchase Date:</label>
                                <input type="datetime-local" name="purchase_date" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Purchase Cost:</label>
                                <input type="number" step="0.01" name="purchase_cost" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Status:</label>
                                <select name="status" class="form-control" required>
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Select Location:</label>
                                <select name="location" class="form-control" required>
                                    <option value="">-- Select --</option>
                                    <?php foreach ($locations as $loc): ?>
                                        <option value="<?= $loc['id'] ?>"><?= htmlspecialchars($loc['location_name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <input type="submit" value="Add Asset" class="btn btn-primary">
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <!-- /Page Content -->

</div>
<!-- /Page Wrapper -->

<script>
    document.getElementById('addAssetBtn').addEventListener('click', function() {
        var form = document.getElementById('addAssetForm');
        if (form.style.display === 'none' || form.style.display === '') {
            form.style.display = 'block';
        } else {
            form.style.display = 'none';
        }
    });
</script>

<?php
include('footer.php');
?>
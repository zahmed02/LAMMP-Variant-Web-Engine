<?php
include('header.php');
include('sidebar.php');
include('connect.php');

// Initialize message variable
$message = '';

// DELETE logic
if (isset($_GET['delete_id'])) {
    $deleteId = intval($_GET['delete_id']);
    $deleteQuery = "DELETE FROM assetassignment WHERE id = $deleteId"; // Change asset_id to id
    if ($conn->query($deleteQuery) === TRUE) {
        $message = "Asset assignment deleted successfully!";
    } else {
        $message = "Error deleting asset assignment: " . $conn->error;
    }
}

// ADD logic
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $asset_id = $_POST['asset_id'];
    $user_id = $_POST['user_id'];
    $created_by = 1; // Assuming the creator's ID is known (e.g., from session)
    $created_at = date('Y-m-d H:i:s');

    // Check if the asset is already assigned to the user
    $checkQuery = "SELECT * FROM assetassignment WHERE asset_id = '$asset_id' AND user_id = '$user_id' AND is_active = 1";
    $checkResult = mysqli_query($conn, $checkQuery);

    if (mysqli_num_rows($checkResult) > 0) {
        $message = "This asset is already assigned to the selected user.";
    } else {
        // Insert the asset assignment into the database
        $insertQuery = "INSERT INTO assetassignment (asset_id, user_id, is_active, created_at, created_by) 
                        VALUES ('$asset_id', '$user_id', 1, '$created_at', '$created_by')";
        
        if (mysqli_query($conn, $insertQuery)) {
            $message = "Asset assigned successfully!";
        } else {
            $message = "Error: " . mysqli_error($conn);
        }
    }
}

// Fetch assigned assets
$assignmentsQuery = "SELECT aa.id, aa.asset_id, aa.user_id, u.full_name, u.employee_code, a.asset_name, a.serial_number 
                     FROM assetassignment aa 
                     JOIN users u ON aa.user_id = u.id 
                     JOIN assets a ON aa.asset_id = a.asset_id 
                     WHERE aa.is_active = 1";
$assignmentsResult = mysqli_query($conn, $assignmentsQuery);

// Fetch assets from the database
$assetsQuery = "SELECT asset_id, asset_name FROM assets WHERE status = 1"; // Assuming status 1 means available
$assetsResult = mysqli_query($conn, $assetsQuery);

// Fetch users from the database
$usersQuery = "SELECT id, full_name, employee_code FROM users WHERE is_active = 1"; // Assuming is_active = 1 means active users
$usersResult = mysqli_query($conn, $usersQuery);
?>

<!-- Page Wrapper -->
<div class="page-wrapper">
    
    <!-- Page Content -->
    <div class="content container-fluid pb-0">
    
        <!-- Page Header -->
        <div class="page-header">
            <div class="row">
                <div class="col-sm-12">
                    <h3 class="page-title">Asset Assignments</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- /Page Header -->

        <!-- Display message -->
        <?php if ($message): ?>
            <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-12 d-flex">
                <div class="card card-table flex-fill">
                    <div class="card-header">
                        <h3 class="card-title mb-0">Assigned Assets</h3>
                        <button id="addAssignmentBtn" class="btn btn-primary float-right">+ Assign Asset</button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-nowrap custom-table mb-0">
                                <thead>
                                    <tr>
                                        <th>S#</th>
                                        <th>Asset Name</th>
                                        <th>Serial Number</th>
                                        <th>User Name</th>
                                        <th>Employee Code</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach ($assignmentsResult as $key => $assignment) {
                                        ?>
                                        <tr>
                                            <td><?php echo $key + 1; ?></td>
                                            <td><?php echo htmlspecialchars($assignment['asset_name']); ?></td>
                                            <td><?php echo htmlspecialchars($assignment['serial_number']); ?></td>
                                            <td><?php echo htmlspecialchars($assignment['full_name']); ?></td>
                                            <td><?php echo htmlspecialchars($assignment['employee_code']); ?></td>
                                            <td>
                                                <a href="assetassignment.php?delete_id=<?php echo $assignment['id']; ?>" class="btn btn-danger" onclick="return confirm('Delete this assignment?')">Delete</a>
                                            </td>
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

        <!-- Add New Assignment Form (Initially Hidden) -->
        <div class="row" id="addAssignmentForm" style="display: none;">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Assign New Asset</h3>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="assetassignment.php">
                            <div class="form-group">
                                <label>Select Asset:</label>
                                <select name="asset_id" class="form-control" required>
                                    <option value="">-- Select Asset --</option>
                                    <?php while ($asset = mysqli_fetch_assoc($assetsResult)): ?>
                                        <option value="<?php echo $asset['asset_id']; ?>"><?php echo htmlspecialchars($asset['asset_name']); ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Select User:</label>
                                <select name="user_id" class="form-control" required>
                                    <option value="">-- Select User --</option>
                                    <?php while ($user = mysqli_fetch_assoc($usersResult)): ?>
                                        <option value="<?php echo $user['id']; ?>"><?php echo htmlspecialchars($user['full_name']); ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <input type="submit" value="Assign Asset" class="btn btn-primary">
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
    document.getElementById('addAssignmentBtn').addEventListener('click', function() {
        var form = document.getElementById('addAssignmentForm');
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

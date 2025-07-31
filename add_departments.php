<?php 
//ob_start(); // Start output buffering
include('header.php');
include('sidebar.php');
include('connect.php');

// Fetch channels for dropdown
$channels = [];
$channel_q = "SELECT id, channel_name FROM channel";
$channel_r = $conn->query($channel_q);
if ($channel_r && $channel_r->num_rows > 0) {
    while ($row = $channel_r->fetch_assoc()) {
        $channels[] = $row;
    }
}

// Handle form submission for adding/updating department
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $department_name = $conn->real_escape_string($_POST['department_name']);
    $channel_id = intval($_POST['channel_id']);
    $deptId = intval($_POST['department']);

    if ($deptId > 0) {
        // Update existing department
        $updateQuery = "UPDATE departments SET department_name = '$department_name', channel_id = $channel_id WHERE id = $deptId";
        $conn->query($updateQuery);
    } else {
        // Add new department
        $insertQuery = "INSERT INTO departments (department_name, channel_id) VALUES ('$department_name', $channel_id)";
        $conn->query($insertQuery);
    }

    // Redirect to departments.php after adding/updating
    header("Location: departments.php");
    exit();
}

// Determine add vs update
if (isset($_GET['department_id'])) {
    $deptId = intval($_GET['department_id']);
    $ds = $conn->query("SELECT * FROM departments WHERE id = $deptId")->fetch_assoc();
    $department_name = $ds['department_name'];
    $channel_id      = intval($ds['channel_id']);
    $btn_text        = 'Update';
} else {
    $deptId = 0;
    $department_name = '';
    $channel_id      = 0;
    $btn_text        = 'Add';
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
                    <h3 class="page-title">Add / Update Department</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- /Page Header -->

        <form id="departmentForm" method="post">
            <div class="form-group">
                <label>Department Name:</label>
                <input type="text" name="department_name" required value="<?= htmlspecialchars($department_name) ?>" class="form-control">
            </div>

            <div class="form-group">
                <label>Select Channel:</label>
                <select name="channel_id" required class="form-control">
                    <option value="">-- Select --</option>
                    <?php foreach ($channels as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= $c['id'] == $channel_id ? 'selected' : '' ?>>
                            <?= htmlspecialchars($c['channel_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <input type="hidden" name="department" value="<?= $deptId ?>">
            <input type="submit" value="<?= $btn_text ?>" class="btn btn-primary">
        </form>

    </div>
    <!-- /Page Content -->

</div>
<!-- /Page Wrapper -->

<?php
include('footer.php');
//ob_end_flush(); // Flush output buffer
?>
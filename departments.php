<?php
include('header.php');
include('sidebar.php');
include('connect.php');

// Initialize message variable
$message = '';

// DELETE logic
if (isset($_GET['delete_id'])) {
    $deleteId = intval($_GET['delete_id']);
    $deleteQuery = "DELETE FROM departments WHERE id = $deleteId";
    if ($conn->query($deleteQuery)) {
        $message = 'Department successfully deleted.';
    } else {
        $message = 'Error deleting department: ' . $conn->error;
    }
}

// UPDATE logic (inline)
if (isset($_POST['save_id'])) {
    $id = intval($_POST['save_id']);
    $department_name = $conn->real_escape_string($_POST['department_name']);
    $channel_id = intval($_POST['channel_id']);
    $modified_by = 1; // Set a default value or retrieve from session
    $modified_at = date('Y-m-d H:i:s');

    $updateQuery = "UPDATE departments SET
        department_name = '$department_name',
        channel_id = $channel_id,
        modified_by = $modified_by,
        modified_at = '$modified_at'
      WHERE id = $id";
    
    if ($conn->query($updateQuery)) {
        $message = 'Department successfully updated.';
    } else {
        $message = 'Error updating department: ' . $conn->error;
    }
}

// ADD logic
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_department"])) {
    $department_name = htmlspecialchars(trim($_POST["department_name"]));
    $channel_id = isset($_POST["channel_id"]) ? intval($_POST["channel_id"]) : 0;

    // Initialize response array
    $response = ['msg' => '', 'msg_code' => 1];

    // Validate input
    if (empty($department_name) || $channel_id === 0) {
        $response['msg'] = 'All fields are required.';
    } else {
        // Insert new department
        $insert_q = "INSERT INTO `departments`(`department_name`, `channel_id`, `created_by`, `created_at`, `modified_by`, `modified_at`) 
                    VALUES ('".$conn->real_escape_string($department_name)."', $channel_id, 1, NOW(), 1, NOW())";
        $insert_r = $conn->query($insert_q);

        if ($insert_r) {
            $response['msg'] = 'Department successfully inserted.';
            $response['msg_code'] = 0; // Success
        } else {
            $response['msg'] = 'Error inserting department: ' . $conn->error;
        }
    }

    // Show response message
    echo "<script>alert('".$response['msg']."');</script>";
}

// Fetch channels for dropdown
$channels = [];
$channel_q = "SELECT id, channel_name FROM channel"; // Adjust the table name if necessary
$channel_r = $conn->query($channel_q);
if ($channel_r) {
    while ($row = $channel_r->fetch_assoc()) {
        $channels[] = $row;
    }
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
                    <h3 class="page-title">Departments</h3>
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
                <?php
                echo $message;
                ?>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-12 d-flex">
                <div class="card card-table flex-fill">
                    <div class="card-header">
                        <h3 class="card-title mb-0">Departments</h3>
                        <button id="addDepartmentBtn" class="btn btn-primary float-right">+ Add New Department</button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-nowrap custom-table mb-0">
                                <thead>
                                    <tr>
                                        <th>S#</th>
                                        <th>Department Name</th>
                                        <th>Channel ID</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $query = "SELECT * FROM departments";
                                    $result = $conn->query($query);

                                    foreach ($result as $key => $department) {
                                        ?>
                                        <tr>
                                            <td><?php echo $key + 1; ?></td>
                                            <td>
                                                <form method="POST" action="departments.php">
                                                    <input type="hidden" name="save_id" value="<?php echo $department['id']; ?>">
                                                    <input type="text" name="department_name" value="<?php echo htmlspecialchars($department['department_name']); ?>" required>
                                            </td>
                                            <td>
                                                <input type="number" name="channel_id" value="<?php echo htmlspecialchars($department['channel_id']); ?>" required>
                                            </td>
                                            <td>
                                                <input type="submit" value="Update" class="btn btn-primary">
                                                <a href="departments.php?delete_id=<?php echo $department['id']; ?>" class="btn btn-danger" onclick="return confirm('Delete this department?')">Delete</a>
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

        <!-- Add New Department Form (Initially Hidden) -->
        <div class="row" id="addDepartmentForm" style="display: none;">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Add New Department</h3>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="departments.php">
                            <input type="hidden" name="add_department" value="1">
                            <div class="form-group">
                                <label>Department Name:</label>
                                <input type="text" name="department_name" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Channel:</label>
                                <select name="channel_id" class="form-control" required>
                                    <option value="">-- Select Channel --</option>
                                    <?php foreach ($channels as $channel): ?>
                                        <option value="<?= $channel['id'] ?>"><?= htmlspecialchars($channel['channel_name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <input type="submit" value="Add Department" class="btn btn-primary">
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
    document.getElementById('addDepartmentBtn').addEventListener('click', function() {
        var form = document.getElementById('addDepartmentForm');
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

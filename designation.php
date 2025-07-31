<?php
include('header.php');
include('sidebar.php');
include('connect.php');

// Initialize message variable
$message = '';

// DELETE logic
if (isset($_GET['delete_id'])) {
    $deleteId = intval($_GET['delete_id']);
    $deleteQuery = "DELETE FROM designation WHERE id = $deleteId";
    if ($conn->query($deleteQuery)) {
        $message = 'Designation successfully deleted.';
    } else {
        $message = 'Error deleting designation: ' . $conn->error;
    }
}

// UPDATE logic (inline)
if (isset($_POST['save_id'])) {
    $id = intval($_POST['save_id']);
    $designation_name = $conn->real_escape_string($_POST['designation_name']);
    $modified_by = 1; // Set a default value or retrieve from session
    $modified_at = date('Y-m-d H:i:s');

    $updateQuery = "UPDATE designation SET
        designation_name = '$designation_name',
        modified_by = $modified_by,
        modified_at = '$modified_at'
      WHERE id = $id";
    
    if ($conn->query($updateQuery)) {
        $message = 'Designation successfully updated.';
    } else {
        $message = 'Error updating designation: ' . $conn->error;
    }
}

// ADD logic
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_designation"])) {
    $designation_name = htmlspecialchars(trim($_POST["designation_name"]));

    // Initialize response array
    $response = ['msg' => '', 'msg_code' => 1];

    // Validate input
    if (empty($designation_name)) {
        $response['msg'] = 'Designation name is required.';
    } else {
        // Insert new designation
        $insert_q = "INSERT INTO `designation`(`designation_name`, `created_by`, `created_at`, `modified_by`, `modified_at`) 
                    VALUES ('".$conn->real_escape_string($designation_name)."', 1, NOW(), 1, NOW())";
        $insert_r = $conn->query($insert_q);

        if ($insert_r) {
            $response['msg'] = 'Designation successfully inserted.';
            $response['msg_code'] = 0; // Success
        } else {
            $response['msg'] = 'Error inserting designation: ' . $conn->error;
        }
    }

    // Show response message
    echo "<script>alert('".$response['msg']."');</script>";
}

// Fetch designations
$query = "SELECT * FROM designation";
$result = $conn->query($query);

if (!$result) {
    // Handle query error
    echo "<script>alert('Error fetching designations: " . $conn->error . "');</script>";
    $designations = []; // Set to an empty array to avoid warnings
} else {
    $designations = $result->fetch_all(MYSQLI_ASSOC); // Fetch all designations as an associative array
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
                    <h3 class="page-title">Designations</h3>
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
                        <h3 class="card-title mb-0">Designations</h3>
                        <button id="addDesignationBtn" class="btn btn-primary float-right">+ Add New Designation</button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-nowrap custom-table mb-0">
                                <thead>
                                    <tr>
                                        <th>S#</th>
                                        <th>Designation Name</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($designations as $key => $designation): ?>
                                        <tr>
                                            <td><?php echo $key + 1; ?></td>
                                            <td>
                                                <form method="POST" action="designation.php">
                                                    <input type="hidden" name="save_id" value="<?php echo $designation['id']; ?>">
                                                    <input type="text" name="designation_name" value="<?php echo htmlspecialchars($designation['designation_name']); ?>" required>
                                            </td>
                                            <td>
                                                <input type="submit" value="Update" class="btn btn-primary">
                                                <a href="designation.php?delete_id=<?php echo $designation['id']; ?>" class="btn btn-danger" onclick="return confirm('Delete this designation?')">Delete</a>
                                            </td>
                                                </form>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add New Designation Form (Initially Hidden) -->
        <div class="row" id="addDesignationForm" style="display: none;">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Add New Designation</h3>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="designation.php">
                            <input type="hidden" name="add_designation" value="1">
                            <div class="form-group">
                                <label>Designation Name:</label>
                                <input type="text" name="designation_name" class="form-control" required>
                            </div>
                            <input type="submit" value="Add Designation" class="btn btn-primary">
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
    document.getElementById('addDesignationBtn').addEventListener('click', function() {
        var form = document.getElementById('addDesignationForm');
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

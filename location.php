<?php
include('header.php');
include('sidebar.php');
include('connect.php');

// Initialize message variable
$message = '';

// DELETE logic
if (isset($_GET['delete_id'])) {
    $deleteId = intval($_GET['delete_id']);
    $deleteQuery = "DELETE FROM location WHERE id = $deleteId";
    if ($conn->query($deleteQuery)) {
        $message = 'Location successfully deleted.';
    } else {
        $message = 'Error deleting location: ' . $conn->error;
    }
}

// UPDATE logic (inline)
if (isset($_POST['save_id'])) {
    $id = intval($_POST['save_id']);
    $location_name = $conn->real_escape_string($_POST['location_name']);
    $modified_by = 1; // Set a default value or retrieve from session
    $modified_at = date('Y-m-d H:i:s');

    $updateQuery = "UPDATE location SET
        location_name = '$location_name',
        modified_by = $modified_by,
        modified_at = '$modified_at'
      WHERE id = $id";
    
    if ($conn->query($updateQuery)) {
        $message = 'Location successfully updated.';
    } else {
        $message = 'Error updating location: ' . $conn->error;
    }
}

// ADD logic
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_location"])) {
    $location_name = htmlspecialchars(trim($_POST["location_name"]));

    // Initialize response array
    $response = ['msg' => '', 'msg_code' => 1];

    // Validate input
    if (empty($location_name)) {
        $response['msg'] = 'Location name is required.';
    } else {
        // Insert new location
        $insert_q = "INSERT INTO `location`(`location_name`, `created_by`, `created_at`, `modified_by`, `modified_at`) 
                    VALUES ('".$conn->real_escape_string($location_name)."', 1, NOW(), 1, NOW())";
        $insert_r = $conn->query($insert_q);

        if ($insert_r) {
            $response['msg'] = 'Location successfully inserted.';
            $response['msg_code'] = 0; // Success
        } else {
            $response['msg'] = 'Error inserting location: ' . $conn->error;
        }
    }

    // Show response message
    echo "<script>alert('".$response['msg']."');</script>";
}

// Fetch locations
$query = "SELECT * FROM location";
$result = $conn->query($query);

if (!$result) {
    // Handle query error
    echo "<script>alert('Error fetching locations: " . $conn->error . "');</script>";
    $locations = []; // Set to an empty array to avoid warnings
} else {
    $locations = $result->fetch_all(MYSQLI_ASSOC); // Fetch all locations as an associative array
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
                    <h3 class="page-title">Locations</h3>
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
                        <h3 class="card-title mb-0">Locations</h3>
                        <button id="addLocationBtn" class="btn btn-primary float-right">+ Add New Location</button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-nowrap custom-table mb-0">
                                <thead>
                                    <tr>
                                        <th>S#</th>
                                        <th>Location Name</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($locations as $key => $location): ?>
                                        <tr>
                                            <td><?php echo $key + 1; ?></td>
                                            <td>
                                                <form method="POST" action="location.php">
                                                    <input type="hidden" name="save_id" value="<?php echo $location['id']; ?>">
                                                    <input type="text" name="location_name" value="<?php echo htmlspecialchars($location['location_name']); ?>" required>
                                            </td>
                                            <td>
                                                <input type="submit" value="Update" class="btn btn-primary">
                                                <a href="location.php?delete_id=<?php echo $location['id']; ?>" class="btn btn-danger" onclick="return confirm('Delete this location?')">Delete</a>
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

        <!-- Add New Location Form (Initially Hidden) -->
        <div class="row" id="addLocationForm" style="display: none;">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Add New Location</h3>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="location.php">
                            <input type="hidden" name="add_location" value="1">
                            <div class="form-group">
                                <label>Location Name:</label>
                                <input type="text" name="location_name" class="form-control" required>
                            </div>
                            <input type="submit" value="Add Location" class="btn btn-primary">
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
    document.getElementById('addLocationBtn').addEventListener('click', function() {
        var form = document.getElementById('addLocationForm');
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

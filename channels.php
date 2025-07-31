<?php
include('header.php');
include('sidebar.php');
include('connect.php');

// Initialize message variable
$message = '';

// DELETE logic
if (isset($_GET['delete_id'])) {
    $deleteId = intval($_GET['delete_id']);
    $deleteQuery = "DELETE FROM channel WHERE id = $deleteId";
    if ($conn->query($deleteQuery)) {
        $message = 'Channel successfully deleted.';
    } else {
        $message = 'Error deleting channel: ' . $conn->error;
    }
}

// UPDATE logic (inline)
if (isset($_POST['save_id'])) {
    $id = intval($_POST['save_id']);
    $channel_name = $conn->real_escape_string($_POST['channel_name']);
    $modified_by = 1; // Set a default value or retrieve from session
    $modified_at = date('Y-m-d H:i:s');

    $updateQuery = "UPDATE channel SET
        channel_name = '$channel_name',
        modified_by = $modified_by,
        modified_at = '$modified_at'
      WHERE id = $id";
    
    if ($conn->query($updateQuery)) {
        $message = 'Channel successfully updated.';
    } else {
        $message = 'Error updating channel: ' . $conn->error;
    }
}

// ADD logic
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_channel"])) {
    $channel_name = htmlspecialchars(trim($_POST["channel_name"]));

    // Initialize response array
    $response = ['msg' => '', 'msg_code' => 1];

    // Validate input
    if (empty($channel_name)) {
        $response['msg'] = 'Channel name is required.';
    } else {
        // Insert new channel
        $insert_q = "INSERT INTO `channel`(`channel_name`, `created_by`, `created_at`, `modified_by`, `modified_at`) 
                    VALUES ('".$conn->real_escape_string($channel_name)."', 1, NOW(), 1, NOW())";
        $insert_r = $conn->query($insert_q);

        if ($insert_r) {
            $response['msg'] = 'Channel successfully inserted.';
            $response['msg_code'] = 0; // Success
        } else {
            $response['msg'] = 'Error inserting channel: ' . $conn->error;
        }
    }

    // Show response message
    echo "<script>alert('".$response['msg']."');</script>";
}

// Fetch channels
$query = "SELECT * FROM channel";
$result = $conn->query($query);

if (!$result) {
    // Handle query error
    echo "<script>alert('Error fetching channels: " . $conn->error . "');</script>";
    $channels = []; // Set to an empty array to avoid warnings
} else {
    $channels = $result->fetch_all(MYSQLI_ASSOC); // Fetch all channels as an associative array
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
                    <h3 class="page-title">Channels</h3>
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
                        <h3 class="card-title mb-0">Channels</h3>
                        <button id="addChannelBtn" class="btn btn-primary float-right">+ Add New Channel</button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-nowrap custom-table mb-0">
                                <thead>
                                    <tr>
                                        <th>S#</th>
                                        <th>Channel Name</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($channels as $key => $channel): ?>
                                        <tr>
                                            <td><?php echo $key + 1; ?></td>
                                            <td>
                                                <form method="POST" action="channels.php">
                                                    <input type="hidden" name="save_id" value="<?php echo $channel['id']; ?>">
                                                    <input type="text" name="channel_name" value="<?php echo htmlspecialchars($channel['channel_name']); ?>" required>
                                            </td>
                                            <td>
                                                <input type="submit" value="Update" class="btn btn-primary">
                                                <a href="channels.php?delete_id=<?php echo $channel['id']; ?>" class="btn btn-danger" onclick="return confirm('Delete this channel?')">Delete</a>
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

        <!-- Add New Channel Form (Initially Hidden) -->
        <div class="row" id="addChannelForm" style="display: none;">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Add New Channel</h3>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="channels.php">
                            <input type="hidden" name="add_channel" value="1">
                            <div class="form-group">
                                <label>Channel Name:</label>
                                <input type="text" name="channel_name" class="form-control" required>
                            </div>
                            <input type="submit" value="Add Channel" class="btn btn-primary">
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
    document.getElementById('addChannelBtn').addEventListener('click', function() {
        var form = document.getElementById('addChannelForm');
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

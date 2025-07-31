<?php
include('header.php');
include('sidebar.php');
include('connect.php');

// Initialize message variable
$message = '';

// DELETE logic
if (isset($_GET['delete_id'])) {
    $deleteId = intval($_GET['delete_id']);
    $deleteQuery = "DELETE FROM users WHERE id = $deleteId";
    if ($conn->query($deleteQuery)) {
        $message = 'User  successfully deleted.';
    } else {
        $message = 'Error deleting user: ' . $conn->error;
    }
}

// UPDATE logic (inline)
if (isset($_POST['save_id'])) {
    $id = intval($_POST['save_id']);
    $username = $conn->real_escape_string($_POST['username']);
    $password = $conn->real_escape_string($_POST['password']);
    $employee_code = intval($_POST['employee_code']);
    $email = $conn->real_escape_string($_POST['email']);
    $full_name = $conn->real_escape_string($_POST['full_name']);
    $designation_id = intval($_POST['designation_id']);
    $department_id = intval($_POST['department_id']);
    $desk_id = intval($_POST['desk_id']);
    $line_manager = intval($_POST['line_manager']);
    $hod = intval($_POST['hod']);
    $channel_id = intval($_POST['channel_id']);
    $location_id = intval($_POST['location_id']);
    $user_role = intval($_POST['user_role']);
    $is_active = isset($_POST['is_active']) ? intval($_POST['is_active']) : 0;
    $modified_by = isset($_POST['modified_by']) ? intval($_POST['modified_by']) : 1; // Default to 1 if not set
    $modified_at = date('Y-m-d H:i:s');

    $updateQuery = "UPDATE users SET
        username = '$username',
        password = '$password',
        employee_code = $employee_code,
        email = '$email',
        full_name = '$full_name',
        designation_id = $designation_id,
        department_id = $department_id,
        desk_id = $desk_id,
        line_manager = $line_manager,
        hod = $hod,
        channel_id = $channel_id,
        location_id = $location_id,
        user_role = $user_role,
        is_active = $is_active,
        modified_by = $modified_by,
        modified_at = '$modified_at'
      WHERE id = $id";
    
    if ($conn->query($updateQuery)) {
        $message = 'User  successfully updated.';
    } else {
        $message = 'Error updating user: ' . $conn->error;
    }
}

// ADD logic
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_user"])) {
    $username = htmlspecialchars(trim($_POST["username"]));
    $password = htmlspecialchars(trim($_POST["password"]));
    $employee_code = intval($_POST["employee_code"]);
    $email = htmlspecialchars(trim($_POST["email"]));
    $full_name = htmlspecialchars(trim($_POST["full_name"]));
    $designation_id = intval($_POST["designation_id"]);
    $department_id = intval($_POST["department_id"]);
    $desk_id = intval($_POST["desk_id"]);
    $line_manager = intval($_POST["line_manager"]);
    $hod = intval($_POST["hod"]);
    $channel_id = intval($_POST["channel_id"]);
    $location_id = intval($_POST["location_id"]);
    $user_role = intval($_POST["user_role"]);
    $is_active = intval($_POST["is_active"]);

    // Initialize response array
    $response = ['msg' => '', 'msg_code' => 1];

    // Validate input
    if (empty($username) || empty($password) || empty($email) || empty($full_name)) {
        $response['msg'] = 'Username, Password, Email, and Full Name are required.';
    } else {
        // Insert new user
        $insert_q = "INSERT INTO `users`(`username`, `password`, `employee_code`, `email`, `full_name`, `designation_id`, `department_id`, `desk_id`, `line_manager`, `hod`, `channel_id`, `location_id`, `user_role`, `is_active`, `created_by`, `created_at`, `modified_by`, `modified_at`) 
                    VALUES ('".$conn->real_escape_string($username)."', '".$conn->real_escape_string($password)."', $employee_code, '".$conn->real_escape_string($email)."', '".$conn->real_escape_string($full_name)."', $designation_id, $department_id, $desk_id, $line_manager, $hod, $channel_id, $location_id, $user_role, $is_active, 1, NOW(), 1, NOW())";
        $insert_r = $conn->query($insert_q);

        if ($insert_r) {
            $response['msg'] = 'User  successfully inserted.';
            $response['msg_code'] = 0; // Success
        } else {
            $response['msg'] = 'Error inserting user: ' . $conn->error;
        }
    }

    // Show response message
    echo "<script>alert('".$response['msg']."');</script>";
}

// Fetch users
$query = "SELECT * FROM users";
$result = $conn->query($query);

if (!$result) {
    // Handle query error
    echo "<script>alert('Error fetching users: " . $conn->error . "');</script>";
    $users = []; // Set to an empty array to avoid warnings
} else {
    $users = $result->fetch_all(MYSQLI_ASSOC); // Fetch all users as an associative array
}

// Fetch additional data for dropdowns (e.g., designations, departments, desks, etc.)
$designations = [];
$departments = [];
$desks = [];
$channels = [];
$locations = [];

// Fetch designations
$designation_q = "SELECT id, designation_name FROM designation"; // Adjust the table name if necessary
$designation_r = $conn->query($designation_q);
if ($designation_r) {
    while ($row = $designation_r->fetch_assoc()) {
        $designations[] = $row;
    }
}

// Fetch departments
$department_q = "SELECT id, department_name FROM departments"; // Adjust the table name if necessary
$department_r = $conn->query($department_q);
if ($department_r) {
    while ($row = $department_r->fetch_assoc()) {
        $departments[] = $row;
    }
}

// Fetch desks
$desk_q = "SELECT id, desk_name FROM desks"; // Adjust the table name if necessary
$desk_r = $conn->query($desk_q);
if ($desk_r) {
    while ($row = $desk_r->fetch_assoc()) {
        $desks[] = $row;
    }
}

// Fetch channels
$channel_q = "SELECT id, channel_name FROM channel"; // Adjust the table name if necessary
$channel_r = $conn->query($channel_q);
if ($channel_r) {
    while ($row = $channel_r->fetch_assoc()) {
        $channels[] = $row;
    }
}

// Fetch locations
$location_q = "SELECT id, location_name FROM location"; // Adjust the table name if necessary
$location_r = $conn->query($location_q);
if ($location_r) {
    while ($row = $location_r->fetch_assoc()) {
        $locations[] = $row;
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
                    <h3 class="page-title">Users</h3>
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
                        <h3 class="card-title mb-0">Users</h3>
                        <!-- Corrected ID: Removed extra space -->
                        <button id="addUserBtn" class="btn btn-primary float-right">+ Add New User</button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-nowrap custom-table mb-0">
                                <thead>
                                    <tr>
                                        <th>S#</th>
                                        <th>Username</th>
                                        <th>Email</th>
                                        <th>Full Name</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($users as $key => $user): ?>
                                        <tr>
                                            <td><?php echo $key + 1; ?></td>
                                            <td>
                                                <form method="POST" action="users.php">
                                                    <input type="hidden" name="save_id" value="<?php echo $user['id']; ?>">
                                                    <input type="hidden" name="password" value="<?php echo htmlspecialchars($user['password']); ?>">
                                                    <input type="hidden" name="employee_code" value="<?php echo $user['employee_code']; ?>">
                                                    <input type="hidden" name="designation_id" value="<?php echo $user['designation_id']; ?>">
                                                    <input type="hidden" name="department_id" value="<?php echo $user['department_id']; ?>">
                                                    <input type="hidden" name="desk_id" value="<?php echo $user['desk_id']; ?>">
                                                    <input type="hidden" name="line_manager" value="<?php echo $user['line_manager']; ?>">
                                                    <input type="hidden" name="hod" value="<?php echo $user['hod']; ?>">
                                                    <input type="hidden" name="channel_id" value="<?php echo $user['channel_id']; ?>">
                                                    <input type="hidden" name="location_id" value="<?php echo $user['location_id']; ?>">
                                                    <input type="hidden" name="user_role" value="<?php echo $user['user_role']; ?>">
                                                    <input type="hidden" name="is_active" value="<?php echo $user['is_active']; ?>">
                                                    <!-- Add modified_by field with session value or default -->
                                                    <input type="hidden" name="modified_by" value="1"> <!-- Change to your session variable if using sessions -->
                                                    <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                                            </td>
                                            <td>
                                                <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                                            </td>
                                            <td>
                                                <input type="text" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                                            </td>
                                            <td>
                                                <input type="submit" value="Update" class="btn btn-primary">
                                                <a href="users.php?delete_id=<?php echo $user['id']; ?>" class="btn btn-danger" onclick="return confirm('Delete this user?')">Delete</a>
                                                <button type="button" class="btn btn-info" onclick="viewUser (<?php echo htmlspecialchars(json_encode($user)); ?>)">View</button>
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

        <!-- Add New User Form (Initially Hidden) -->
        <!-- Corrected ID: Removed extra space -->
        <div class="row" id="addUserForm" style="display: none;">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Add New User</h3>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="users.php">
                            <input type="hidden" name="add_user" value="1">
                            <div class="form-group">
                                <label>Username:</label>
                                <input type="text" name="username" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Password:</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Employee Code:</label>
                                <input type="number" name="employee_code" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Email:</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Full Name:</label>
                                <input type="text" name="full_name" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Designation:</label>
                                <select name="designation_id" class="form-control" required>
                                    <option value="">-- Select Designation --</option>
                                    <?php foreach ($designations as $designation): ?>
                                        <option value="<?= $designation['id'] ?>"><?= htmlspecialchars($designation['designation_name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Department:</label>
                                <select name="department_id" class="form-control">
                                    <option value="">-- Select Department --</option>
                                    <?php foreach ($departments as $department): ?>
                                        <option value="<?= $department['id'] ?>"><?= htmlspecialchars($department['department_name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Desk:</label>
                                <select name="desk_id" class="form-control">
                                    <option value="">-- Select Desk --</option>
                                    <?php foreach ($desks as $desk): ?>
                                        <option value="<?= $desk['id'] ?>"><?= htmlspecialchars($desk['desk_name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Line Manager:</label>
                                <input type="number" name="line_manager" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>HOD:</label>
                                <input type="number" name="hod" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Channel:</label>
                                <select name="channel_id" class="form-control">
                                    <option value="">-- Select Channel --</option>
                                    <?php foreach ($channels as $channel): ?>
                                        <option value="<?= $channel['id'] ?>"><?= htmlspecialchars($channel['channel_name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Location:</label>
                                <select name="location_id" class="form-control">
                                    <option value="">-- Select Location --</option>
                                    <?php foreach ($locations as $location): ?>
                                        <option value="<?= $location['id'] ?>"><?= htmlspecialchars($location['location_name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>User Role:</label>
                                <input type="number" name="user_role" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Is Active:</label>
                                <select name="is_active" class="form-control" required>
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                            <input type="submit" value="Add User" class="btn btn-primary">
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <!-- /Page Content -->

</div>
<!-- /Page Wrapper -->

<!-- User Details Modal -->
<div id="userDetailsModal" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">User Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" style="color:white;">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="userDetailsContent" class="user-details-container d-flex justify-content-between gap-3">
                    <!-- Tables will be injected here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
// Existing viewUser function
function viewUser(user) {
    const leftSideFields = [
        ["User ID", user.id],
        ["Username", user.username],
        ["Password", user.password],
        ["Employee Code", user.employee_code],
        ["Email", user.email],
        ["Full Name", user.full_name],
        ["Designation ID", user.designation_id],
        ["Department ID", user.department_id],
        ["Desk ID", user.desk_id]
    ];

    const rightSideFields = [
        ["Line Manager", user.line_manager],
        ["HOD", user.hod],
        ["Channel ID", user.channel_id],
        ["Location ID", user.location_id],
        ["User Role", user.user_role],
        ["Is Active", user.is_active ? 'Yes' : 'No'],
        ["Created By", user.created_by],
        ["Created At", user.created_at],
        ["Modified By", user.modified_by],
        ["Modified At", user.modified_at]
    ];

    function generateTable(rows) {
        let table = `<table class="user-details-table"><tbody>`;
        rows.forEach(([label, value]) => {
            table += `
                <tr>
                    <td>${label}:</td>
                    <td>${value}</td>
                </tr>
            `;
        });
        table += `</tbody></table>`;
        return table;
    }

    const leftTable = generateTable(leftSideFields);
    const rightTable = generateTable(rightSideFields);

    document.getElementById('userDetailsContent').innerHTML = `
        ${leftTable}
        ${rightTable}
    `;

    $('#userDetailsModal').modal('show');
}

// New JavaScript to handle the "Add New User" button click
document.addEventListener('DOMContentLoaded', function() {
    const addUserBtn = document.getElementById('addUserBtn'); // Corrected ID
    const addUserForm = document.getElementById('addUserForm'); // Corrected ID

    if (addUserBtn && addUserForm) {
        addUserBtn.addEventListener('click', function() {
            if (addUserForm.style.display === 'none' || addUserForm.style.display === '') {
                addUserForm.style.display = 'block';
            } else {
                addUserForm.style.display = 'none';
            }
        });
    }
});
</script>


<style>
.modal-dialog {
    max-width: 900px;
}

.modal-content {
    border-radius: 12px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
}

.user-details-container {
    display: flex;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 20px;
}

.user-details-table {
    width: 100%;
    max-width: 48%;
    border-collapse: collapse;
    background-color: #fff;
    flex: 1;
}

.user-details-table td {
    padding: 10px 12px;
    vertical-align: top;
    border: 1px solid #e9ecef;
}

.user-details-table td:first-child {
    font-weight: 600;
    background-color: #f8f9fa;
    width: 40%;
    color: #343a40;
}

.user-details-table td:nth-child(2) {
    color: #dc3545;
    font-weight: bold;
}

.modal-header {
    background: #007bff;
    color: white;
    border-bottom: none;
    border-radius: 12px 12px 0 0;
}

.modal-title {
    font-weight: 600;
    font-size: 1.25rem;
}

.modal-footer {
    border-top: none;
    padding: 10px 20px;
    border-radius: 0 0 12px 12px;
}

.btn-secondary {
    background-color: #6c757d;
    border-color: #6c757d;
    padding: 6px 16px;
}
</style>


<?php 
    include('footer.php'); 
?>
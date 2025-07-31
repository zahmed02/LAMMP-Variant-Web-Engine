<?php
include('connect.php');


// Fetch dropdown options
function getOptions($conn, $table, $id, $name) {
    $opts = '';
    $r = $conn->query("SELECT $id, $name FROM $table");
    while ($o = $r->fetch_assoc()) {
        $opts .= "<option value='{$o[$id]}'>" . htmlspecialchars($o[$name]) . "</option>";
    }
    return $opts;
}

// Handle form submission
/*
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $employee_code = (int)$_POST['employee_code'];
    $email = $conn->real_escape_string(trim($_POST['email']));
    $full_name = $conn->real_escape_string(trim($_POST['full_name']));
    $designation_id = (int)$_POST['designation_id'];
    $department_id = (int)$_POST['department_id'];
    $desk_id = (int)$_POST['desk_id'];
    $line_manager = (int)$_POST['line_manager'];
    $hod = (int)$_POST['hod'];
    $channel_id = (int)$_POST['channel_id'];
    $location_id = (int)$_POST['location_id'];
    $user_role = (int)$_POST['user_role'];
    $is_active = (int)$_POST['is_active'];

    // Check uniqueness
    $check_q = "SELECT * FROM users WHERE employee_code = $employee_code OR email = '$email'";
    $check_r = $conn->query($check_q);

    if ($check_r->num_rows > 0) {
        echo "<p style='color:red;'>Error: Employee code or email already exists.</p>";
    } else {
        // Insert user
        $insert_q = "INSERT INTO users (
            employee_code, email, full_name, designation_id, department_id, desk_id,
            line_manager, hod, channel_id, location_id, user_role, is_active
        ) VALUES (
            $employee_code, '$email', '$full_name', $designation_id, $department_id, $desk_id,
            $line_manager, $hod, $channel_id, $location_id, $user_role, $is_active
        )";

        if ($conn->query($insert_q)) {
            echo "<p style='color:green;'>User  successfully inserted.</p>";
        } else {
            echo "<p style='color:red;'>Database error: " . $conn->error . "</p>";
        }
    }
}
*/

?>

<!DOCTYPE html>
<html>
<head>
    <title>Add / Update User</title>
    <script src="https://portal.humnetwork.tv/assets/js/jquery-3.7.1.min.js"></script>
</head>
<body>

<h2>Add New User</h2>
<?php
if (isset($_GET['user_id'])) {
    $uid = intval($_GET['user_id']);
    $u = $conn->query("SELECT * FROM users WHERE id=$uid")->fetch_assoc();
    $btn = 'Update';
} else {
    $uid = 0;
    $btn = 'Add';
    $u = [];    
}
?>
<form id="userForm">
    <input type="hidden" name="users" value="<?= $uid ?>">
    
    Username: <input type="text" name="username" required value="<?= htmlspecialchars($u['username'] ?? '') ?>"><br>

    Password: <input type="password" name="password" required><br>

    Employee Code: <input type="number" name="employee_code" required value="<?= intval($u['employee_code'] ?? 0) ?>"><br>

    Email: <input type="email" name="email" required value="<?= htmlspecialchars($u['email'] ?? '') ?>"><br>

    Full Name: <input type="text" name="full_name" required value="<?= htmlspecialchars($u['full_name'] ?? '') ?>"><br>

    Designation: <select name="designation_id" required>
        <option>--</option>
        <?= getOptions($conn, 'designation', 'id', 'designation_name') ?>
    </select><br>

    Department: <select name="department_id" required>
        <option>--</option>
        <?= getOptions($conn, 'departments', 'id', 'department_name') ?>
    </select><br>

    Desk: <select name="desk_id" required>
        <option>--</option>
        <?= getOptions($conn, 'desks', 'id', 'desk_name') ?>
    </select><br>

    HOD: <select name="hod" required>
        <option>--</option>
        <?php
        $hr = $conn->query("SELECT employee_code, full_name FROM users WHERE user_role=2");
        while ($o = $hr->fetch_assoc()) {
            echo "<option value='{$o['employee_code']}'>" . htmlspecialchars($o['full_name']) . "</option>";
        }
        ?>
    </select><br>

    Line Manager: <select name="line_manager" required>
        <option>--</option>
        <?php
        $lr = $conn->query("SELECT employee_code, full_name FROM users WHERE user_role IN(2,3)");
        while ($o = $lr->fetch_assoc()) {
            echo "<option value='{$o['employee_code']}'>" . htmlspecialchars($o['full_name']) . "</option>";
        }
        ?>
    </select><br>

    Channel: <select name="channel_id" required>
        <option>--</option>
        <?= getOptions($conn, 'channel', 'id', 'channel_name') ?>
    </select><br>

    Location: <select name="location_id" required>
        <option>--</option>
        <?= getOptions($conn, 'location', 'id', 'location_name') ?>
    </select><br>

    Role: <select name="user_role" required>
        <option>--</option>
        <option value="1">1. Super Admin</option>
        <option value="2">2. HOD</option>
        <option value="3">3. Line Mgr</option>
        <option value="4">4. Employee</option>
    </select><br>

    Active: <select name="is_active" required>
        <option>--</option>
        <option value="1">Active</option>
        <option value="0">Inactive</option>
    </select><br>
    <button type="submit"><?= $btn ?></button>
</form>

<script>
$('#userForm').on('submit', function(e) {
    e.preventDefault();
    $.ajax({
        url: 'inc/process.php',
        type: 'POST',
        data: new FormData(this),
        contentType: false,
        processData: false,
        dataType: 'json'
    }).done(function(d) {
        alert(d.msg);
        if (d.msg_code === 0) {
            $('#userForm')[0].reset();
        }
    });
});
</script>

</body>
</html>
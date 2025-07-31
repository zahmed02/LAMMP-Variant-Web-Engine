<?php 
include('connect.php');

?>

<!DOCTYPE html>
<html>
<head>
    <title>Add / Update Designation</title>
    <script src="https://portal.humnetwork.tv/assets/js/jquery-3.7.1.min.js"></script>
</head>
<body>

<?php
// Uncomment the following block to handle form submission
/*
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $designation_name = htmlspecialchars(trim($_POST["designation_name"]));

    // Check if designation already exists
    $check_q = "SELECT * FROM designation WHERE designation_name = '".$conn->real_escape_string($designation_name)."'";
    $check_r = $conn->query($check_q);

    if ($check_r->num_rows > 0) {
        echo "<p style='color:red;'>Designation name already exists. Choose a different name.</p>";
    } else {
        // Insert new designation
        $insert_q = "INSERT INTO `designation`(`designation_name`, `created_by`, `created_at`, `modified_by`, `modified_at`) 
                     VALUES ('".$conn->real_escape_string($designation_name)."', 1, '$date_time', 1, '$date_time')";
        $insert_r = $conn->query($insert_q);

        if ($insert_r) {
            echo "<p style='color:green;'>Designation successfully inserted.</p>";
        } else {
            echo "<p style='color:red;'>Error inserting designation.</p>";
        }
    }
}
*/

if (isset($_GET['designation_id'])) {
    $did = intval($_GET['designation_id']);
    $d = $conn->query("SELECT * FROM designation WHERE id=$did")->fetch_assoc();
    $name = $d['designation_name'];
    $btn = 'Update';
} else {
    $did = 0; 
    $name = ''; 
    $btn = 'Add'; 
}
?>

<form id="desForm">
    <label>Designation Name:</label><br>
    <input type="text" name="designation_name" required value="<?= htmlspecialchars($name) ?>"><br><br>
    <input type="hidden" name="designation" value="<?= $did ?>">
    <input type="submit" value="<?= $btn ?>">
</form>

<script>
$('#desForm').on('submit', function(e) {
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
            $('#desForm')[0].reset();
        }
    });
});
</script>

</body>
</html>
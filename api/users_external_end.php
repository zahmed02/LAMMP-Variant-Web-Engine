<!--NOTE: This file is for INFORMATION PURPOSE ONLY, as seen in the database, the attribute columns and names are differernt from MY database in phpMyAdmin-->

<!--NOTE: REMEMBER This file is done on Chrome, you can say a (Live Server) online; that's how the "other code on the other end" fetches the data from here-->

<!--NOTE: REMEMBER This file was made from the other end (not my/this laptop), and I could view it's data on my laptop at the URL http://localhost/hr/api/users.php-->
<?php
include('../connect.php');

header('Access-Control-Allow-Origin: *');
header("Content-type: application/json; charset=utf-8");
// ini_set('display_errors', '1');
// ini_set('display_startup_errors', '1');
// error_reporting(E_ALL);

function get_department_name($depart_id) {
    global $conn;
    $q = "SELECT * FROM department WHERE id = '".$depart_id."'";
    $r = $conn->query($q);
    
    foreach($r as $d) {
        $department_name = $d['department_name'];
    }
    
    return $department_name;
}

function get_designation_name($designation_id) {
    global $conn;
    $q = "SELECT * FROM designation WHERE id = '".$designation_id."'";
    $r = $conn->query($q);
    
    foreach($r as $d) {
        $designation_name = $d['designation_name'];
    }
    
    return $designation_name;
}

function get_location_name($location_id) {
    global $conn;
    $q = "SELECT * FROM location WHERE id = '".$location_id."'";
    $r = $conn->query($q);
    
    foreach($r as $d) {
        $location_name = $d['location_name'];
    }
    
    return $location_name;
}


function get_desk_name($desk_id) {
    global $conn;
    $q = "SELECT * FROM desk WHERE id = '".$desk_id."'";
    $r = $conn->query($q);
    
    foreach($r as $d) {
        $desk_name = $d['desk_name'];
    }
    
    return $desk_name;
}


function get_lm_name($lm_code) {
    global $conn;
    $q = "SELECT * FROM users WHERE emp_code = '".$lm_code."'";
    $r = $conn->query($q);
    
    foreach($r as $d) {
        $lm_name = $d['full_name'];
    }
    
    return $lm_name;
}

function get_hod_name($hod_code) {
    global $conn;
    $q = "SELECT * FROM users WHERE emp_code = '".$hod_code."'";
    $r = $conn->query($q);
    
    foreach($r as $d) {
        $hod_name = $d['full_name'];
    }
    
    return $hod_name;
}


if(isset($_POST['auth_key'])) {
    
    $auth_key = $_POST['auth_key'];
    $channel_id = $_POST['channel_id'];
    $count = $_POST['count'];
    
    
    if($auth_key == 'fayyazaslam') {
        $q = "SELECT * FROM users WHERE is_active = 1 Limit $count";
        $r = $conn->query($q);
        
        foreach($r as $key => $val) {
            // echo $val['line_manager'];
            $id = $val['id'];
            $username = $val['username'];
            $email = $val['email'];
            $emp_code = $val['emp_code'];
            $full_name = $val['full_name'];
            $gender = $val['gender'];
            $cnic = $val['cnic'];
            $dob = $val['date_of_birth'];
            $department_name = get_department_name($val['department_id']);
            $designation_name = get_designation_name($val['designation_id']);
            $desk_name = get_desk_name($val['desk_id']);
            $location_name = get_location_name($val['location_id']);
            $doj = $val['date_of_joining'];
            $lm = get_lm_name($val['line_manager']);
            $hod = get_hod_name($val['hod']);
            $user_role = $val['user_role'];
            
            $data[] = array(
                "user_id"   =>  $id,
                "username"  =>  $username,
                "email"     =>  $email,
                "emp_code"  =>  $emp_code,
                "full_name" =>  $full_name,
                "gender"    =>  $gender,
                "cnic"      =>  $cnic,
                "dob"       =>  $dob,
                "department_name"    =>  $department_name,
                "designation_name"  =>  $designation_name,
                "desk_name" =>  $desk_name,
                "location"  =>  $location_name,
                "doj"       =>  $doj,
                "lm"        =>  $lm,
                "hod"       =>  $hod,
                "user_role" =>  $user_role
            );
            
        }
        
        // echo "<pre>";
        // print_r($data);
        // echo "</pre>";
        
        echo json_encode($data, TRUE);   
    } else {
        $data[] = array("msg"   => "Auth Key invalid");
        echo json_encode($data, TRUE);   
    }
} else {
    $data[] = array("msg"   => "Please with auth key");
    echo json_encode($data, TRUE);   
}
?>
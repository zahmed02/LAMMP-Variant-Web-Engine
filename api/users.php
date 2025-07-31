<!--This file was made here in this very laptop, in this very folder-->
<?php

$url = "https://portalstg.humnetwork.tv/api/users.php";
$post_data = array('auth_key' => 'fayyazaslam', "count" =>  50);


$ch = curl_init($url);
// return the response instead of sending it to stdout:
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// set the POST data, corresponding method and headers:
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
// send the request and get the response
$server_output = curl_exec($ch);

$data = json_decode($server_output, TRUE);

foreach($data as $key => $val) {
  echo $val['emp_code'];
  echo "<br />";
}
?>
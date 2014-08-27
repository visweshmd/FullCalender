<?php include("db.php");?>
<?php

$id = $_POST['id'];
$query = "DELETE FROM EVENTS WHERE CALENDER <> 'calendar' AND ID = '" . $id . "'";
$con = mysqli_connect(SERVER, USERNAME, PASSWORD);
mysqli_select_db($con,DB);
$result = mysqli_query($con,$query);
mysqli_close($con);
echo $result;
?>

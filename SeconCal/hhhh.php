<?php include("db.php");?>
<?php
$title = $_POST['title'];
$desc_str = $_POST['desc'];
$cal = $_POST['cal'];
$end = $_POST['end'];
$start = $_POST['start'];
$id = $_POST['id'];
$status = $_POST['status'];
$query = "INSERT INTO EVENTS VALUES ('".$cal."','".$id."','".$start."','".$end."','".$title."','".$desc_str."','".$status."', 'First Insert', 1)";
$con = mysqli_connect(SERVER, USERNAME, PASSWORD);
mysqli_select_db($con,DB);
$result = mysqli_query($con,$query);
mysqli_close($con);
echo $result;
?>
<?php include("db.php");?>
<?php
$title = $_POST['title'];
$desc_str = $_POST['desc'];
$cal = $_POST['cal'];
$end = $_POST['end'];
$start = $_POST['start'];
$id = $_POST['id'];
$status = $_POST['status'];
$newComments = $_POST['comment'];
//$query = "INSERT INTO EVENTS VALUES ('".$cal."','".$id."','".$start."','".$end."','".$title."','".$desc_str."','".$status."')";
$query2 = "UPDATE EVENTS SET Start = '".$start."', End = '".$end."', Title = '".$title."', Descrip = '".$desc_str."', Status = '".$status."', ExtraComments = concat(ifnull(ExtraComments,''), '  ".$newComments."') where ID ='".$id."' AND Calender ='".$cal."'";
$con = mysqli_connect(SERVER, USERNAME, PASSWORD);
mysqli_select_db($con,DB);
$result = mysqli_query($con,$query2);
mysqli_close($con);
echo $result;
?>

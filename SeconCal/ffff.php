<?php
  $query = 'SELECT * FROM PLATFORMs';

  // Make a MySQL Connection
  $con = mysqli_connect("localhost", 'root', 'root');
  mysqli_select_db($con,'calender');
  $result = mysqli_query($con,$query);
  while($row = mysqli_fetch_array($result)){
     $rows[] = $row;  
    
  }
   $output = json_encode($rows);
   echo $output;
    mysqli_free_result($result);
    mysqli_close($con);
    echo 'done';
?>
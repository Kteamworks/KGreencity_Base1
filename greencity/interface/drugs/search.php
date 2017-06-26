<?php
    $key=$_GET['key'];
    $array = array();
    $con=mysqli_connect("localhost","root","","openemr");
   // $db=mysql_select_db("demos",$con);
    $query=mysqli_query($con,"select * from `medicine_master` where Medicine_Name LIKE '{$key}%'");
    while($row=mysqli_fetch_assoc($query))
    {
      $array[] = $row['Medicine_Name'];
    }
    echo json_encode($array);
?>

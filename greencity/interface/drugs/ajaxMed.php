<?php

include_once('dbconnect.php');

if($_POST['id'] && $_POST['action']=='med')
{
$id=$_POST['id'];
 $id1=mysqli_real_escape_string($conn,$id);
$sql=mysqli_query($conn,"SELECT Medicine_Type FROM medicine_master WHERE Medicine_Name = '$id1'");

while($row=mysqli_fetch_array($sql))
{
$id=$row['Medicine_Type'];
$data=$row['Medicine_Type'];
echo '<option value="'.$id.'">'.$data.'</option>';

}
}




if($_POST['id'] && $_POST['action']=='manu')
{
$id=$_POST['id'];
$id1=mysqli_real_escape_string($conn,$id);
$sql=mysqli_query($conn,"SELECT Medicine_Manufacturer FROM medicine_master WHERE Medicine_Name = '$id1'");

while($row=mysqli_fetch_array($sql))
{
$id=$row['Medicine_Manufacturer'];
$data=$row['Medicine_Manufacturer'];
echo '<option value="'.$id.'">'.$data.'</option>';

}
}


if($_POST['id'] && $_POST['action']=='vat')
{
$id=$_POST['id'];
$id1=mysqli_real_escape_string($conn,$id);
$sql=mysqli_query($conn,"SELECT Medicine_Tax FROM medicine_master WHERE Medicine_Name = '$id1'");

while($row=mysqli_fetch_array($sql))
{
$id=$row['Medicine_Tax'];
$data=$row['Medicine_Tax'];
echo '<option value="'.$id.'">'.$data.'</option>';

}
}
 	
?>
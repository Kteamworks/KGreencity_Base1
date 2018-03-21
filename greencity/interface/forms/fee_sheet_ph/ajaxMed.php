<?php

 
  include_once('dbconnect.php');
 //$con=mysqli_connect("localhost","root","","greencity");

 if($_POST['id'] && $_POST['action']=='detail')
{
$id=$_POST['id'];
$sql=mysqli_query($conn,"select fname,e.pid as ID,e.date, e.encounter  from patient_data p Join form_encounter e on p.pid=e.pid 
where genericname1='$id' order by e.date desc limit 1");
//$sql=mysqli_query($con,"SELECT batch FROM drugs WHERE drug_id = '$id'");

while($row=mysqli_fetch_array($sql))
{
$id=$row['Visit'];
$data=$row['encounter'];
//echo '<input type="text" value="'.$id.'">';
//echo '<option value="'.$id.'">'.$id.'</option>';
echo $data;

}
}

 if($_POST['id'] && $_POST['action']=='pid')
{
$id=$_POST['id'];
$sql=mysqli_query($conn,"select e.pid from patient_data p Join form_encounter e on p.pid=e.pid 
where genericname1='$id' order by e.date desc limit 1");
//$sql=mysqli_query($con,"SELECT batch FROM drugs WHERE drug_id = '$id'");

while($row=mysqli_fetch_array($sql))
{
$id=$row['pid'];
$data=$row['pid'];
//echo '<input type="text" value="'.$id.'">';
//echo '<option value="'.$id.'">'.$id.'</option>';
echo $data;

}
}



if($_POST['id'] && $_POST['action']=='detail1')
{
$id=$_POST['id'];
$sql=mysqli_query($conn,"select fname from patient_data 
where genericname1='$id'");
//$sql=mysqli_query($con,"SELECT batch FROM drugs WHERE drug_id = '$id'");

while($row=mysqli_fetch_array($sql))
{
$id=$row['fname'];
$data=$row['fname'];
//echo '<input type="text" value="'.$id.'">';
//echo '<option value="'.$id.'">'.$id.'</option>';
echo $data;

}
}
 
 
 
 
 
if($_POST['id'] && $_POST['action']=='med')
{
	 $exp = date('Y-m-d', strtotime('+1 month'));
$id=$_POST['id'];
$sql=mysqli_query($conn,"SELECT batch FROM drugs WHERE name = (select name from drugs where drug_id = '$id') and expdate > '$exp'");
//$sql=mysqli_query($con,"SELECT batch FROM drugs WHERE drug_id = '$id'");

while($row=mysqli_fetch_array($sql))
{
$id=$row['batch'];
$data=$row['PricePerUnit'];
//echo '<input type="text" value="'.$id.'">';
echo '<option value="'.$id.'">'.$id.'</option>';
//echo $data;

}
}
if($_POST['id'] && $_POST['action']=='medPrice')
{
$id=$_POST['id'];
$sql=mysqli_query($conn,"SELECT PricePerUnit FROM drugs WHERE drug_id = '$id' LIMIT 1");

while($row=mysqli_fetch_array($sql))
{
//$id=$row['batch'];
$data=$row['PricePerUnit'];
//echo '<input type="text" value="'.$id.'">';
//echo '<option value="'.$data.'">'.$data.'</option>';
echo $data;

}
}

if($_POST['id'] && $_POST['action']=='expdate')
{
	 $exp = date('Y-m-d', strtotime('+1 month'));
$id=$_POST['id'];
$sql=mysqli_query($conn,"SELECT expdate FROM drugs WHERE name = (select name from drugs where drug_id = '$id')limit 1");
//$sql=mysqli_query($con,"SELECT batch FROM drugs WHERE drug_id = '$id'");

while($row=mysqli_fetch_array($sql))
{

$data=$row['expdate'];
$data1= substr($data,0,7);
//echo '<input type="text" value="'.$id.'">';
echo $data1;
//echo $data;

}
}


if($_POST['id'] && $_POST['action']=='schedule_h')
{
	
$id=$_POST['id'];

$sql=mysqli_query($conn,"SELECT schedule_h FROM drugs WHERE name = (select name from drugs where drug_id = '$id')limit 1");
//$sql=mysqli_query($con,"SELECT batch FROM drugs WHERE drug_id = '$id'");

while($row=mysqli_fetch_array($sql))
{

$data=$row['schedule_h'];
if($data==0)
{ 
$data = 'NO';
}
else {
	  $data = 'Yes';
     } 

//$data1= substr($data,0,7);
//echo '<input type="text" value="'.$id.'">';
echo $data;
//echo $data;

}
}






if($_POST['id'] && $_POST['action']=='medRate')
{
$id=$_POST['id'];
$sql=mysqli_query($conn,"SELECT PricePerUnit FROM drugs WHERE batch = '$id' LIMIT 1");

while($row=mysqli_fetch_array($sql))
{
//$id=$row['batch'];
$data=$row['PricePerUnit'];
//echo '<input type="text" value="'.$id.'">';
//echo '<option value="'.$data.'">'.$data.'</option>';
echo $data;

}
}




if($_POST['id'] && $_POST['action']=='manu')
{
$id=$_POST['id'];
$sql=mysqli_query($conn,"SELECT Medicine_Manufacturer FROM medicine_master WHERE Medicine_Name = '$id'");

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
$sql=mysqli_query($conn,"SELECT Medicine_Tax FROM medicine_master WHERE Medicine_Name = '$id'");

while($row=mysqli_fetch_array($sql))
{
$id=$row['Medicine_Tax'];
$data=$row['Medicine_Tax'];
echo '<option value="'.$id.'">'.$data.'</option>';

}
}
 	
?>
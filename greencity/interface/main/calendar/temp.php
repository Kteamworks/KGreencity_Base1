<?php

 include_once("../../globals.php");
include_once("$srcdir/api.inc");
include_once("$srcdir/forms.inc");
include_once("$srcdir/encounter.inc");
require_once("$srcdir/formdata.inc.php");
require_once("$srcdir/patient.inc");
require_once("$srcdir/acl.inc");
require_once("$srcdir/options.inc.php");
require_once("$srcdir/formatting.inc.php");

 if($_POST['id'] && $_POST['action']=='detail')
{
$id=$_POST['id'];
$sql=sqlStatement("SELECT id, username, fname, lname FROM users WHERE authorized != 0 AND active = 1 and username like 'DR%' and specialty ='Gyneacology' ORDER BY fname, lname");
//$sql=mysqli_query($con,"SELECT batch FROM drugs WHERE drug_id = '$id'");

while($row=sqlFetchArray($sql))
{
$id=$row['id'];
$data=$row['username'];
//echo '<input type="text" value="'.$id.'">';
echo '<option value="'.$id.'">'.$data.'</option>';


}
}
 if($_POST['id'] && $_POST['action']=='opd')
{
$id=$_POST['id'];
$sql=SqlStatement("SELECT id, username, fname, lname FROM users WHERE authorized != 0 AND active = 1 and username like 'DR%' ORDER BY fname, lname");
//$sql=mysqli_query($con,"SELECT batch FROM drugs WHERE drug_id = '$id'");

while($row=sqlFetchArray($sql))
{
$id=$row['id'];
$data=$row['username'];
//echo '<input type="text" value="'.$id.'">';
echo '<option value="'.$id.'">'.$data.'</option>';


}
}

  	
?>
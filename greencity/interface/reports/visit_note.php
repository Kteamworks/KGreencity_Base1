<?php 
/**
 * 
 * Superbill Report
 *
 * LICENSE: This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://opensource.org/licenses/gpl-license.php>;.
 *
 * @package OpenEMR
 * @author  Brady Miller <brady@sparmy.com>
 * @link    http://www.open-emr.org
 */

$fake_register_globals=false;
$sanitize_all_escapes=true;

require_once(dirname(__file__)."/../globals.php");
require_once("$srcdir/forms.inc");
require_once("$srcdir/billing.inc");
require_once("$srcdir/patient.inc");
require_once("$srcdir/report.inc");
require_once("$srcdir/classes/Document.class.php");
require_once("$srcdir/classes/Note.class.php");
require_once("$srcdir/formatting.inc.php");

$startdate = $enddate = "";
$dated = date('Y-m-d');
if(empty($_POST['start']) || empty($_POST['end'])) {
    // set some default dates
    $startdate = date('Y-m-d', (time() - 10*24*60*60));
    $enddate = date('Y-m-d', time());
}
else {
    // set dates
    $startdate = $_POST['start'];
    $enddate = $_POST['end'];
}
//Patient related stuff
if ($_POST["form_patient"])
$form_patient = isset($_POST['form_patient']) ? $_POST['form_patient'] : '';
//$form_pid = isset($_POST['form_pid']) ? $_POST['form_pid'] : '';
$form_pid= $_SESSION["pid"];
$form_encounter= $_SESSION["encounter"];
if ($form_patient == '' ) $form_pid =  $_SESSION["pid"];
if ($form_patient == '' ) $form_pid = '';


if($form_encounter==""){
	echo ("<script>alert('Please select visit')
window.location.href='../main/finder/p_dynamic_finder.php';
</script>"); 
	
}
$user = $_SESSION['authUser'];
$patient=sqlQuery("select reason from form_encounter where pid=$pid and encounter=$form_encounter");
$pname=sqlQuery("select fname,sex,age from patient_data where pid=$pid");





if(isset($_POST['submit'])){
	$reason = $_POST['reason']; 
	
	/*echo "insert into pnotes(date,body,pid,user,groupname,activity,authorized,title,assigned_to,message_status) 
		          values('$dated','Admit the Patient','$pid','$user','Default','1','1','Admit','Receptionist','New')"; exit; */
	 $clinical = sqlQuery("UPDATE form_encounter set reason ='$reason' where encounter='$encounter'");
	// echo "select count(activity) as count from pnotes where pid = '$pid' and activity ='1"; exit;
	 $check = sqlQuery("select count(activity) as count from pnotes where pid = '$pid' and activity ='1' ");
	  if($check['count']<1){
	  $msg = sqlInsert("insert into pnotes(date,body,pid,user,groupname,activity,authorized,title,assigned_to,message_status) 
		         values('$dated','Admit the Patient','$pid','$user','Default','1','1','Admit','Receptionist','New')");
	  }
	 header('location:../main/finder/p_dynamic_finder.php');
}



 ?>


<!DOCTYPE html>
<html lang="en">
<head>
  <title>Bootstrap Example</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.js"></script>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
  
  
</head>
<body>
<form name='' method='POST' action=''>
<div class="container col-md-3 col-lg-3 well">

 
  <div class="form-group">
    <label for="exampleFormControlSelect1">Patient Name</label>
	<input type='text' class='form-control' value='<?php echo $pname['fname'];?>' disabled>
	
    
  </div>
  <div class="form-group">
    <label for="exampleFormControlSelect2">Gender</label>
	<input type='text' class='form-control' value='<?php echo $pname['sex'];?>' disabled>    
  </div>
  
  <div class="form-group">
    <label for="exampleFormControlSelect2">Age</label>
	<input type='text' class='form-control' value='<?php echo $pname['age'];?>' disabled>    
  </div>
  
  <div class="form-group">
    <label for="exampleFormControlTextarea1">Note</label>
    <textarea class="form-control" name='reason'  rows="4"><?php echo $patient['reason']; ?></textarea>
  </div>
  
  <button type="submit" name='submit' class="btn btn-default">Save</button>




</form>
</div>
</body>
</html>




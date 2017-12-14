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
if ($form_patient == '' ) $form_pid =  $_SESSION["pid"];
//if ($form_patient == '' ) $form_pid = '';
if(($encounter==0)||($encounter=='')){ 
	
 echo ("<script>alert('Please select visit')
window.location.href='../main/finder/p_dynamic_finder_ip.php';
</script>"); 
}

$patient=sqlQuery("select * from t_form_admit where pid=$pid and encounter=$encounter");
$pname=sqlQuery("select fname,sex,age from patient_data where pid=$pid");
$Info=sqlStatement("select * from medication_schduling where pid=$pid and encounter=$encounter");
$count=sqlQuery("select count(*) as count from medication_schduling where pid=$pid and encounter=$encounter");


$listResult = sqlStatement("SELECT  * FROM `ipschdule` where pid=$pid and  result!=''");



//$list1 = sqlStatement("SELECT  * FROM `nurseIP`");

 $ward = $patient['admit_to_ward'];
 $bed = $patient['admit_to_bed'];

  


if(isset($_POST['submit'])){
	$hours = $_POST['hours'];
	 $days = $_POST['days'];
	 $newDays = date('Y-m-d', strtotime($time. " + {$days} days"));  

	 $tym = $_POST['service_time'];
	 $service= $_POST['service'];
	
 	  $noOfHrs = $days*24 ;
	  $times= $noOfHrs/$hours;
	  $dt = date('Y-m-d');
	  
    $dateTime =  $dt.' '.$tym;
	  
	  
	    date_default_timezone_set('Asia/Kolkata');
        $time = date('H:i');
		
		
  $medication = sqlInsert("insert into medication_schduling(pid,encounter,ward,bed,service,dated,frequency,days) 
		            values('$pid','$encounter','$ward','$bed','$service','$dt','$hours','$days')");		
		
	
	
	$j=1;

	while($j<=$times){
		
		if($newTime==''){
			$newTime = $dateTime;
		}
		
		
		$time = $newTime;
		$t = substr($time,11);
		
		
		
		 $date= substr($time,0,10);
		 if($date >= $newDays)
		   break;
	
	
		 
		
		
         $addedTime=$hours;

        $newTime = date('Y-m-d H:i', strtotime($time. " + {$addedTime} hours"));
		
		
		
		$Nurse = sqlInsert("insert into ipschdule(pid,encounter,ward,bed,tym,service,dated) 
		            values('$pid','$encounter','$ward','$bed','$t','$service','$date')");
					
		 
	
		
	$j++;}
	
	header('location:timeSchduler.php');
	
	      
 
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
  
  <script>
    function printContent(el){
	var restorePage = document.body.innerHTML;
	var printcontent = document.getElementById(el).innerHTML;
	document.body.innerHTML = printcontent;
	window.print();
	document.body.innerHTML = restorePage;
	location.reload(true);
	//location.href = "timeSchduler.php";
	
	
	}
	
	
	


	
	
  
  </script>
  
  
  
  

</head>
<body>
<form name='' method='POST' action=''>

<div class="table-responsive">
<table class="table table-striped table-bordered table-responsive">

      <tr class='active'>
        <th>Patient Name</th>
		<th>Gender</th>
		<th>Age</th>
        <th>Ward</th>
        <th>Bed</th>
		<th>Detail</th>
		</tr>
		
		<tr class='info'>
        <td><?php echo $pname['fname'];?></td>
        <td><?php echo $pname['sex'];?></td>
		<td><?php echo $pname['age'];?></td>
		<td><?php echo $patient['admit_to_ward'];?></td>
        <td><?php echo $patient['admit_to_bed'];?></td>
		<td><button type="button"  data-toggle="modal" data-target="#myModal">Click Here</button> </td>
		</tr>
		
	
</table>
</div>



<div class="container col-md-offset-10">
 
  <!-- Trigger the modal with a button -->
  

  <!-- Modal -->
  <div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
	  <div id='div1'>
        <div class="modal-header">
         
          <h4 class="modal-title">Medication Summary of <?php echo $pname['fname'];  ?></h4>
        </div>
        <div  class="modal-body">
		
		
		
		<table class="table table-striped  table-condensed table-responsive">
    <thead>
      <tr class='active'>
	    
		<th>Services</th>
        <th>Date</th>
        <th>Time</th>
        
		<th>Result</th>
		
		
      </tr>
    </thead>
	<tbody>
	
		
         
		 <?php $i=1;
	 while($listResult1=sqlFetchArray($listResult))  { 
	  
	
	 ?>
	 
      <tr>
	  <?php  $dated=date('d-M-y',strtotime($listResult1['updatedTime'])); 
	         $tym=date('h:i:s A',strtotime($listResult1['updatedTime'])); 
                 
	  ?>
	  
	  
       
	   <td class="table-active"><?php echo $listResult1['service']; ?></td>
		<td class="table-active"><?php echo $dated; ?></td>
		<td class="table-active"><?php echo $tym; ?></td>
		
		<td class="table-active"><?php echo $listResult1['result'];   ?></td>
		
		
        
	
      </tr>  
	<?php $i++; } ?> 
	</tbody>
	</table>
	
		 
        </div> </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default"  data-dismiss="modal">Close</button>
		  <button type="button" class="btn btn-default" onclick="printContent('div1')">Print</button>
        </div>
      </div>
      
    </div>
  </div>
  
</div>
















 
<div class="container col-md-3 col-lg-3 well">


<!--------------------------------------------------------------------------------------------------------->

 <div class="form-group">
      <label for="email">Service</label>
	  <div class="form-group">
      <input type="text" class="form-control"  placeholder="Service" name="service">
	  </div>
    </div>
    <div class="form-group">
      <label for="pwd">Time</label>
      <input type="time" class="form-control" id="pwd" placeholder="Enter password" name="service_time">
    </div>
	
	<div class="form-group">
      <label for="pwd">Repetition Time</label>
      <input type="number" class="form-control"  min='0' max='24' placeholder="Hours" name="hours">
	  
	  </div>
	
	
	<div class="form-group">
      <label for="pwd">Days</label>
      <input type="number" class="form-control"  placeholder="No. of days " name="days">
    </div>
    
    <button type="submit" name='submit' class="btn btn-default">Add</button>




<!--------------------------------------------------------------------------------------------------------->	 

  
</div>

<!-----------------------------------------------right side ------------------------------------------------->
<?php
   if($count['count']>=1){
	   
     ?>


<div class="container col-md-offset-3 col-md-5 well">
<table class="table table-striped  table-condensed">
    <thead>
      <tr class='active'>
        <th>Service</th>
        <th>Date</th>
		<th>Repetition Time</th>
        <th>Days</th>
		
		
      </tr>
    </thead>
    <tbody>
	
	<?php $i=1;
	 while($infoResult=sqlFetchArray($Info))  {  ?>
	 <tr>
	     <td class="table-active"><?php echo $infoResult['service']; ?></td>
         <td class="table-active"><?php echo $infoResult['dated']; ?></td>
	     <td class="table-active"><?php echo $infoResult['frequency'].' '.'Hours'; ?></td>
         <td class="table-active"><?php echo $infoResult['days']; ?></td>
	 </tr>
	<?php  $i++;  
	 }  ?>
	</tbody>
	</table>
	 


</div>
<?php 
   }
?>
<!-------------------------------------------------end-------------------------------------------------------->

</form>

</body>
</html>




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

$patient=sqlQuery("select * from t_form_admit where pid=$pid and encounter=$encounter");
$pname=sqlQuery("select fname,sex,age from patient_data where pid=$pid");

$list1 = sqlStatement("SELECT  * FROM `nurseIP`");




 ?>


<!DOCTYPE html>
<html lang="en">
<head>
  <title>Bootstrap Example</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="<?php echo $css_header;?>" type="text/css">
</head>
<body>
<div class="container">
  <h2>Nurse Station</h2>
  
  
 
  
<table class="table table-striped table-bordered">

      <tr class='active'>
        <th>Patient Name</th>
		<th>Gender</th>
		<th>Age</th>
        <th>Ward</th>
        <th>Bed</th>
		</tr>
		
		<tr class='info'>
        <td><?php echo $pname['fname'];?></td>
        <td><?php echo $pname['sex'];?></td>
		<td><?php echo $pname['age'];?></td>
		<td><?php echo $patient['admit_to_ward'];?></td>
        <td><?php echo $patient['admit_to_bed'];?></td>
		</tr>
		
	
</table>


<table class="table table-striped table-bordered">
    <thead>
      <tr class='active'>
        <th>Date & Time</th>
        <th>Medicine Given</th>
        <th>B.P.</th>
		<th>Pulse (bpm)</th>
		<th>Temperature</th>
      </tr>
    </thead>
    <tbody>
	<?php $i=1;
	 while($list2=sqlFetchArray($list1))  { ?>
      <tr>
	  <?php  $dated=date('d-M-y h:i:s A',strtotime($list2['date']));  ?>
	  
	  
        <td class="table-active"><?php echo $dated; ?></td>
	    <td class="table-active"><?php echo $list2['medicine']; ?></td>
		<td class="table-active"><?php echo $list2['bp']; ?></td>
		<td class="table-active"><?php echo $list2['pulse']; ?></td>
		<td class="table-active"><?php echo $list2['temp']; ?></td>
        
	
      </tr>  
	<?php $i++; } ?> 
    </tbody>
  </table>





</div>
</body>
</html>




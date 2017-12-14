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




include_once('../../sites/default/dbconnect.php');

if(isset($_POST['lock'])){
	echo "<script>";
	echo "var txt;";
    echo "var r = confirm('It will prevent any futher edits on this form. Do you want to Lock?');";
    echo "if (r == true) {";
      echo $lock=sqlQuery("update clinicalHistory set active=1 where pid=$pid and encounter=$encounter ");
   echo "} ";
    
	echo "</script>";
	

}
$active=sqlQuery("select * from clinicalHistory where pid=$pid and encounter=$encounter");



if(isset($_POST['submit'])){
	$complaint1 = $_POST['complaint'];
	$complaint=mysqli_real_escape_string($conn,$complaint1);
	
	$illness1 = $_POST['illness'];
	$illness=mysqli_real_escape_string($conn,$illness1);
	
	$history1 = $_POST['history'];
	$history=mysqli_real_escape_string($conn,$history1);
	
	$family1 = $_POST['family'];
	$family=mysqli_real_escape_string($conn,$history1);
	
	$general1 = $_POST['general'];
	$general=mysqli_real_escape_string($conn,$general1);
	
	$local1 = $_POST['local'];
	$local=mysqli_real_escape_string($conn,$local1);
	
	
	if($active['pid']!=''){
	
	$clinical=sqlQuery("update clinicalHistory set complaint='$complaint' , illness='$illness', history='$history', family='$family', general='$general',local='$local' ");
	
}
	
	else{
	
	$clinical = sqlInsert("insert into clinicalHistory(pid,encounter,complaint,illness,history,family,general,local) values('$pid','$encounter','$complaint','$illness','$history','$family','$general','$local')");
	}
}


$active=sqlQuery("select * from clinicalHistory where pid=$pid and encounter=$encounter");



?>






<html>

<head>
<?php html_header_show();?>
<link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="<?php echo $css_header;?>" type="text/css">
<style  type="text/css">
table {
  border-collapse: collapse;

  
  }


@media print {
	.title {
		visibility: hidden;
	}
	#margindiv{
		margin:0px;
		width:0px;
		}
    .pagebreak {
        page-break-after: always;
        border: none;
        visibility: hidden;
    }

	#superbill_description {
		visibility: hidden;
	}

	#report_parameters {
		visibility: hidden;
	}
    #superbill_results {
       margin-top: 0px;
    }
}

@media screen {
	.title {
		visibility: visible;
	}
	#superbill_description {
		visibility: visible;
	}
    .pagebreak {
        width: 100%;
        border: 2px dashed black;
    }
	#report_parameters {
		visibility: visible;
	}
}
#superbill_description {
   margin: 10px;
}
#superbill_startingdate {
    margin: 0px;
}
#superbill_endingdate {
    margin: 0px;
}

#superbill_patientdata {
}
#superbill_patientdata h1 {
    font-weight: bold;
    font-size: 0.8em;
    margin: 0px;
    padding: 5px;
    width: 100%;
    background-color: #eee;
    border: 1px solid black;
}
#superbill_insurancedata {
    margin-top: 10px;
}
#superbill_insurancedata h1 {
    font-weight: bold;
    font-size: 0.8em;
    margin: 0px;
    padding: 5px;
    width: 100%;
    background-color: #eee;
    border: 1px solid black;
}
#superbill_insurancedata h2 {
    font-weight: bold;
    font-size: 0.8em;
    margin: 0px;
    padding: 0px;
    width: 100%;
    background-color: #eee;
}
#superbill_billingdata {
    margin-top: 3px;
}
#superbill_billingdata h1 {
    font-weight: bold;
    font-size: 0.8em;
    margin: 0px;
    padding: 5px;
    width: 100%;
    background-color: #eee;
    border: 1px solid black;
}
#superbill_signature {
}
#superbill_logo {
}

@page  
{ 
    size: auto;   /* auto is the initial value */ 

    /* this affects the margin in the printer settings */ 
    margin: 3mm 5mm 10mm 10mm;  
} 

body  
{ 
    /* this affects the margin on the content before sending to printer */ 
    margin: 0px;  
} 
</style>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/dialog.js"></script>
<script language="Javascript">
// CapMinds :: invokes  find-patient popup.
 function sel_patient() {
  dlgopen('../main/calendar/find_patient_popup.php?pflag=0', '_blank', 500, 400);
 }

// CapMinds :: callback by the find-patient popup.
 function setpatient(pid, lname, fname, dob) {
  var f = document.theform;
  f.form_patient.value = lname + ', ' + fname;
  f.form_pid.value = pid;

 }

</script>
</head>

<body class="body_top">
<img src=" <?php echo $GLOBALS['webroot']?>/interface/pic/medii.jpg" />
<hr>




<div id="superbill_results">

<?php
if( 1) {
    $sql = "select * from facility where billing_location = 1";
    $db = $GLOBALS['adodb']['db'];
    $results = $db->Execute($sql);
    $facility = array();
    if (!$results->EOF) {
        $facility = $results->fields;
?>


<?php
    }
		$sqlBindArray = array();
		$e=$_POST["encounter"];
		$res_query = 	"select * from forms where " .
                        "form_name = 'New Patient Encounter'";
                array_push($sqlBindArray);
		if($form_pid) {
		$e=$_SESSION["encounter"];
		$res_query.= " and pid=?";	
		array_push($sqlBindArray,$form_pid);
		}
        $res_query.=     " order by date DESC" ;
		$res =sqlStatement($res_query,$sqlBindArray);
	
    while($result = sqlFetchArray($res)) {
        if ($result{"form_name"} == "New Patient Encounter" and $result["encounter"]== $e) {
            $newpatient[] = $result{"form_id"}.":".$result{"encounter"};
			
			$pids[] = $result{"pid"};	
        }
    }
    $N = 6;

    function postToGet($newpatient, $pids) {
        $getstring="";
        $serialnewpatient = serialize($newpatient);
        $serialpids = serialize($pids);
        $getstring = "newpatient=".urlencode($serialnewpatient)."&pids=".urlencode($serialpids);

        return $getstring;
    }

    $iCounter = 0;
    if(empty($newpatient)){ $newpatient = array(); }
    foreach($newpatient as $patient){
        /*
        $inclookupres = sqlStatement("select distinct formdir from forms where pid='".$pids[$iCounter]."'");
        while($result = sqlFetchArray($inclookupres)) {
            include_once("{$GLOBALS['incdir']}/forms/" . $result{"formdir"} . "/report.php");
        }
        */

        print "<div id='superbill_patientdata'>";
        //print "<h1>".xlt('Patient Data').":</h1>";
               $patdata = getPatientData($pids[$iCounter], 'phone_cell,title,age,age_days,age_months,rateplan,date,sex,DOB,genericname1,fname,mname,lname,pubpid,street,city,state,postal_code,providerID');
        function ageCalculator($dob){
	if(!empty($dob)){
		$birthdate = new DateTime($dob);
		$today   = new DateTime('today');
		$age = $birthdate->diff($today)->y;
		return $age;
	}else{
		return 0;
	}
}

$dob =text($patdata['DOB']) ;
$enc=sqlStatement("select * from form_encounter where encounter='".$encounter."'");
$enc1=sqlFetchArray($enc);
$provider=$enc1['provider_id'];
$row1 = sqlStatement("SELECT * from users where id='".$provider."'");
$row2=  sqlFetchArray($row1);
$billing=sqlStatement("select * from billing  where encounter='".$encounter."'");
$billid=sqlFetchArray($billing);

$billingdate=sqlStatement("select max(date) as d from billing  where encounter='".$encounter."' and activity=1");
$billdate=sqlFetchArray($billingdate);

$admit=sqlStatement("select * from t_form_admit  where encounter='".$encounter."'");
$admit1=sqlFetchArray($admit);
$row32=sqlStatement("select * from insurance_data where pid='".$form_pid."'");
$row3=sqlFetchArray($row32);
$provider1=$row3['provider'];
$insurance=sqlStatement("select * from insurance_companies where id='".$provider1."'");
$insurance1=sqlFetchArray($insurance);
$age=$patdata['age'];
$age_months=$patdata['age_months'];
$age_days=$patdata['age_days'];
$rateplan=$patdata['rateplan'];

    echo "<center><h3>".xlt("Clinical History")."</h3></center>";
	echo "<table border=1 rules=cols style='width:100%'>";
	
	echo "<tr><td  style='padding-right: 100px;' >" . xlt('Name') . ": <b>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp".text($patdata['title']) ."  ". text($patdata['fname']) . "  " . text($patdata['mname'])."  ".text($patdata['lname']) . "</b></td>";
	echo "<td style='padding-right: '  >" . xlt('Bill No') . ":&nbsp&nbsp&nbsp&nbsp&nbsp " . text($billid['bill_id']) . "</td>";
	if($age!=0)
	{
	echo "<tr><td  style='padding-right: 100px;' >" . xlt('Age/Gender') . ": " . text($patdata['age']) ." ".xlt('Years')." , ".text($patdata['sex']). "</td>";
	}else
	if($age_months!=0)
	{
	echo "<tr><td  style='padding-right: 100px;' >" . xlt('Age/Gender') . ": " . text($patdata['age_months']) ." ".xlt('Months')." , ".text($patdata['sex']). "</td>";
	}else
	{
		echo "<tr><td  style='padding-right: 100px;' >" . xlt('Age/Gender') . ": " . text($patdata['age_days']) ." ".xlt('Days')." , ".text($patdata['sex']). "</td>";
	}
	
	echo "<td  style='padding-right: 10px;' >" . xlt('Bill Date') . ":&nbsp&nbsp ". text(date('d/M/y h:i:s A',strtotime($billdate['d'])))."</td>";
	echo "<tr><td  style='padding-right: 100px;' >" . xlt('Address:') . " &nbsp&nbsp&nbsp&nbsp&nbsp" . "".text($patdata['street']). "</td>";
	echo "<td  style='padding-right: 10px;'>" . xlt('MR No') . ": &nbsp&nbsp&nbsp&nbsp&nbsp" . text($patdata['genericname1']) . "</td></tr>";
	echo "<tr><td  style='padding-right: 100px;' >" . xlt('Location:') . " &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp" . "".text($patdata['city']).", ".text($patdata['state']) ."</td>";
	echo "<td  style='padding-right: 10px;' >" . xlt('Visit ID') . ":&nbsp&nbsp&nbsp&nbsp&nbsp " . text($enc1['encounter_ipop']) . "</td></tr>";
	//echo "<tr><td style='padding-right: 100px;'  >" . xlt('Doctor') . ":&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp &nbsp&nbsp" . text($row2['username']). "</td>";
	//echo "<td  style='padding-right: 10px;' >" . xlt('Department') . ": " . text($row2['specialty']) . "</td></tr>";
	if($admit1['admit_to_ward']!=NULL)
	{
	echo "<tr><td  style='padding-right: 100px;' >" . xlt('ADM Date') . ": ". text(date('d/M/y h:i:s A',strtotime($admit1['admit_date'])))."</td>";
	echo "<td style='padding-right: 10px;'  >" . xlt('Ward/Bed') . ":&nbsp " . text($admit1['admit_to_ward']) ." , ".text($admit1['admit_to_bed']). "</td></tr>";
	echo "<tr><td   style='padding-right: 100px;'>" . xlt('DCH Date') . ":&nbsp". text(date('d/M/y h:i:s A',strtotime($admit1['discharge_date'])))."</td>";
    echo "<td  style='padding-right: 10px;' >" . xlt('Rate Plan') . ": &nbsp&nbsp&nbsp" . text($patdata['rateplan']) . "</td></tr>";
	}
	if($rateplan=="TPAInsurance")
	{
	echo "<tr><td  style='padding-right: 100px;' >" . xlt('TPA Insurance') . ": ".$insurance1['name'] ."</td>";
	echo "<td  style='padding-right: 10px;' >" .xlt(''). "</td></tr>";
	}
	/* echo "<tr><td  style='padding-right: 10px;' >" . xlt('Date') . ":&nbsp&nbsp ". text(date('d/M/y',strtotime($patdata['date'])))."</td>";
	echo "<td  style='padding-right: 10px;' >" . xlt('Bill Date') . ":&nbsp&nbsp ". text(date(' d/M/y'))."</td></tr>";
	echo "<tr><td  style='padding-right: 10px;' >" . xlt('Name') . ": <b>&nbsp&nbsp" . text($patdata['fname']) . "  " . text($patdata['lname']) . "</b></td>";
	echo "<td  style='padding-right: 10px;'>" . xlt('Patient ID') . ": &nbsp&nbsp" . text($patdata['genericname1']) . "</td></tr>";
	echo "<tr><td  style='padding-right: 10px;' >" . xlt('Age/Gender') . ": &nbsp&nbsp" . ageCalculator($dob) ." , ".text($patdata['sex']). "</td>";
    
	echo "<tr><td  style='padding-right: 10px;' >" . xlt('Department') . ": &nbsp&nbsp" . text($row2['specialty']) . "</td>";
	echo "<td  style='padding-right: 10px;' >" . xlt('Encounter') . ":&nbsp&nbsp " . text($enc1['encounter_ipop']) . "</td></tr>";
	echo "<tr><td style='padding-right: 10px;'  >" . xlt('Bill No') . ":&nbsp&nbsp " . text($billid['bill_id']) . "</td>";
	echo "<td style='padding-right: 10px;'  >" . xlt('Ward/Bed') . ":&nbsp&nbsp " . text($admit1['admit_to_ward']) ." , ".text($admit1['admit_to_bed']). "</td></tr>";
	echo "<tr><td  style='padding-right: 10px;' >" . xlt('Admission Date') . ":&nbsp&nbsp ". text(date('d/M/y',strtotime($admit1['admit_date'])))."</td>";
	echo "<td   style='padding-right: 10px;'>" . xlt('Discharge Date') . ":&nbsp&nbsp ". text(date(' d/M/y',strtotime($admit1['discharge_date'])))."</td></tr>";
	 */echo "</table>";
		print "</div>";
    
  

        print "<div id='superbill_billingdata'>";
        //print "<h1>".xlt('Billing Information').":</h1>";
        
        echo "</div>";

        ++$iCounter;

		
    }
}
    ?>
	
</div>

<br>


 <form action='' method='POST'>   
		
<table class="table table-bordered table-fixed" id="tab_logic">
<tr><th>Chief Complaint: </th>
<td>
<textarea rows="3" cols="120" name='complaint'  style="height:4em;border:1px solid white;" id='myText1' required><?php  echo $active['complaint']; ?></textarea>
</td>
</tr>
<tr>
<th>History of present illness</th>
<td><textarea rows="3" cols="120" name='illness' style="height:4em;border:1px solid white;" id='myText2' required><?php  echo $active['illness']; ?></textarea>
</td>
</tr>

<tr>
<th>Past History</th>
<td><textarea rows="3" cols="120" name='history' style="height:4em;border:1px solid white;" id='myText3' required><?php  echo $active['history']; ?></textarea>
</td>
</tr>

<tr>
<th>Family History</th>
<td><textarea rows="3" cols="120" name='family' style="height:4em;border:1px solid white;" id='myText4' required ><?php  echo $active['family']; ?></textarea>
</td>
</tr>

<tr>
<th>General Examination</th>
<td><textarea rows="3" cols="120" name='general' style="height:3em;border:1px solid white;" id='myText5' required ><?php  echo $active['general']; ?></textarea>
</td>
</tr>


<tr>
<th>Local Examination</th>
<td><textarea rows="3" cols="120" name='local' style="height:3em;border:1px solid white;" id='myText6' required ><?php  echo $active['local']; ?></textarea>
</td>

</tr>

</table>


<div id="report_parameters">


<input type='hidden' name='form_refresh' id='form_refresh' value=''/>
<table>
 <tr>
  <td width='0px'>
	<div style='float:left'>

	<table class='text'>
		<tr>
			<td class='label'>
			   <?php //echo xlt('Start Date'); ?>
			</td>
			<td>
			   <input type='hidden' name='start' id="form_from_date" size='10' value='<?php echo attr($startdate) ?>'
				onkeyup='datekeyup(this,mypcc)' onblur='dateblur(this,mypcc)' title='yyyy-mm-dd'>
			  <!--<img src='../pic/show_calendar.gif' align='absbottom' width='24' height='22'
				id='img_from_date' border='0' alt='[?]' style='cursor:pointer'
				title='<?php //echo xla('Click here to choose a date'); ?>'> -->
			</td>
			<td class='label'>
			   <?php //echo xlt('End Date'); ?>
			</td>
			<td>
			   <input type='hidden' name='end' id="form_to_date" size='10' value='<?php echo attr($enddate) ?>'
				onkeyup='datekeyup(this,mypcc)' onblur='dateblur(this,mypcc)' title='yyyy-mm-dd'>
			  <!-- <img src='../pic/show_calendar.gif' align='absbottom' width='24' height='22'
				id='img_to_date' border='0' alt='[?]' style='cursor:pointer'
				title='<?php //echo xla('Click here to choose a date'); ?>'>-->
			</td>

			<td>
			&nbsp;&nbsp;<span class='hidden'><?php // echo xlt('Patient'); ?> </span>
			</td>
			<td>
			<input type='hidden' size='20' name='form_patient' style='width:100%;cursor:pointer;cursor:hand' value='<?php echo attr($form_patient) ? attr($form_patient) : xla('Click To Select'); ?>' onclick='sel_patient()' title='<?php echo xla('Click to select patient'); ?>' />
			<input type='hidden' name='form_pid' value='<?php echo attr($form_pid); ?>' />
			</td>
			</tr>
			<tr><td>
		</tr>
	</table>

	</div>

  </td>
  <td align='left' valign='middle' height="100%">
	<table style='border-left:0px solid; width:20%; height:100%' >
		<tr>
		<td><input type='submit' class='css_button' value='Save' name='submit'></td>
		<td><input type='submit' class='css_button' value='Lock' name='lock'></td>
			<td>
				<div style='margin-left:15px'>
					

					<?php if (1) { ?>
					<a href='#' class='css_button' onclick='window.print()'>
						<span>
							<?php echo xlt('Print'); ?>
						</span>
					</a>
					<?php } ?>
				</div>
			</td>
		</tr>
	</table>
  </td>
 </tr>
</table>
</div> <!-- end of parameters -->

</form>

    </body>

	<script>
    var val = "<?php echo $active['active']; ?>";
	if(val==1){
    document.getElementById("myText1").readOnly = true;
	document.getElementById("myText2").readOnly = true;
	document.getElementById("myText3").readOnly = true;
	document.getElementById("myText4").readOnly = true;
	document.getElementById("myText5").readOnly = true;
	document.getElementById("myText6").readOnly = true;
	}
</script>
	
	
	
	
	
	
<!-- stuff for the popup calendar -->
<style type="text/css">@import url(../../library/dynarch_calendar.css);</style>
<script type="text/javascript" src="../../library/dynarch_calendar.js"></script>
<?php include_once("{$GLOBALS['srcdir']}/dynarch_calendar_en.inc.php"); ?>
<script type="text/javascript" src="../../library/dynarch_calendar_setup.js"></script>
<script type="text/javascript" src="../../library/js/jquery.1.3.2.js"></script>

<script language="Javascript">
 /* Calendar.setup({inputField:"form_from_date", ifFormat:"%Y-%m-%d", button:"img_from_date"});
 Calendar.setup({inputField:"form_to_date", ifFormat:"%Y-%m-%d", button:"img_to_date"});
 */
</script>
</html>
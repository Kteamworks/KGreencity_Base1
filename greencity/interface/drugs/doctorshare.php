<?php
 // Copyright (C) 2006-2011 Rod Roark <rod@sunsetsystems.com>
 //
 // This program is free software; you can redistribute it and/or
 // modify it under the terms of the GNU General Public License
 // as published by the Free Software Foundation; either version 2
 // of the License, or (at your option) any later version.

$sanitize_all_escapes  = true;
$fake_register_globals = false;

 require_once("../globals.php");
 require_once("$srcdir/acl.inc");
 require_once("drugs.inc.php");
 require_once("$srcdir/options.inc.php");
 require_once("$srcdir/formdata.inc.php");
 require_once("$srcdir/htmlspecialchars.inc.php");

 $alertmsg = '';
 $drug_id = $_REQUEST['drug'];
 $info_msg = "";
 $tmpl_line_no = 0;

 if (!acl_check('admin', 'drugs')) die(xlt('Not authorized'));

// Format dollars for display.
//
function bucks($amount) {
  if ($amount) {
    $amount = sprintf("%.2f", $amount);
    if ($amount != 0.00) return $amount;
  }
  return '';
}

// Write a line of data for one template to the form.
//
function writeTemplateLine($selector, $dosage, $period, $quantity, $refills, $prices, $taxrates) {
  global $tmpl_line_no;
  ++$tmpl_line_no;

  echo " <tr>\n";
  echo "  <td class='tmplcell drugsonly'>";
  echo "<input type='text' name='form_tmpl[$tmpl_line_no][selector]' value='" . attr($selector) . "' size='8' maxlength='100'>";
  echo "</td>\n";
  echo "  <td class='tmplcell drugsonly'>";
  echo "<input type='text' name='form_tmpl[$tmpl_line_no][dosage]' value='" . attr($dosage) . "' size='6' maxlength='10'>";
  echo "</td>\n";
  echo "  <td class='tmplcell drugsonly'>";
  generate_form_field(array(
    'data_type'   => 1,
    'field_id'    => 'tmpl[' . $tmpl_line_no . '][period]',
    'list_id'     => 'drug_interval',
    'empty_title' => 'SKIP'
    ), $period);
  echo "</td>\n";
  echo "  <td class='tmplcell drugsonly'>";
  echo "<input type='text' name='form_tmpl[$tmpl_line_no][quantity]' value='" . attr($quantity) . "' size='3' maxlength='7'>";
  echo "</td>\n";
  echo "  <td class='tmplcell drugsonly'>";
  echo "<input type='text' name='form_tmpl[$tmpl_line_no][refills]' value='" . attr($refills) . "' size='3' maxlength='5'>";
  echo "</td>\n";
  foreach ($prices as $pricelevel => $price) {
    echo "  <td class='tmplcell'>";
    echo "<input type='text' name='form_tmpl[$tmpl_line_no][price][" . attr($pricelevel) . "]' value='" . attr($price) . "' size='6' maxlength='12'>";
    echo "</td>\n";
  }
  $pres = sqlStatement("SELECT option_id FROM list_options " .
    "WHERE list_id = 'taxrate' ORDER BY seq");
  while ($prow = sqlFetchArray($pres)) {
    echo "  <td class='tmplcell'>";
    echo "<input type='checkbox' name='form_tmpl[$tmpl_line_no][taxrate][" . attr($prow['option_id']) . "]' value='1'";
    if (strpos(":$taxrates", $prow['option_id']) !== false) echo " checked";
    echo " /></td>\n";
  }
  echo " </tr>\n";
}

// Translation for form fields used in SQL queries.
//
function escapedff($name) {
  return add_escape_custom(trim($_POST[$name]));
}
function numericff($name) {
  $field = trim($_POST[$name]) + 0;
  return add_escape_custom($field);
}
 
//session_start();
if(isset($_REQUEST['id']))
	$id=$_REQUEST['id'];
else
	$id="";


$pname= sqlQuery("select p.fname,p.genericname1,p.phone_cell from billing b join patient_data p
on b.pid=p.pid
where b.bill_id='$id'");


//$name=$_REQUEST['date'];
		$list = sqlStatement("select distinct b.provider_id, b.fee,b.code_text,u.username from billing b
                join users u on b.provider_id=u.id 
                where bill_id='$id' and activity=1");
				
				

	 if (isset($_POST['submit_form'])) {

 $j=0;
	foreach($_POST['code'] as $selected){
   
   //echo $patient= $_POST['patient'][$j];
    $code1= $selected;
   	
    $payout= $_POST['payout'][$j];
	if(empty($payout)){
		$payout=0;
		
	}
    
	
   
 sqlQuery("Update billing set payout= $payout where bill_id='$id' and code_text='$code1' "); 
   
   
	$j++;
	}
	}








?>

















<!DOCTYPE html>

<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<title>Selectize.js Demo</title>
		<meta name="description" content="">
		<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1">
		<link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
		<link rel="stylesheet" type="text/css" media="all" href="jsDatePick_ltr.min.css" />
		
       
        
	</head>
  

		<table class="table table-bordered table-fixed table-responsive">
		<tr>
		    <th>Patient Name</th>
		    <th>GCH ID</th>
			<th>Mobile No.</th>
		</tr>
		<tr>
			<td><?php echo $pname['fname']; ?> </td>
			<td><?php echo $pname['genericname1']; ?> </td>
			<td><?php echo $pname['phone_cell']; ?> </td>
		</tr>	
		
		
	
		
		
		
		
		
		</table>
		
		
		
		
<form method="post" action="">
		
			<table class="table table-bordered table-fixed table-responsive" id="tab_logic">
				<thead>
					<tr class="danger">
						<th class="text-left col-sm-1">
							S.No.
						</th>
						<th class="text-left col-sm-2">
							Doctor Name
						</th>
						
						<th class="text-left col-sm-2">
							Services
						</th>
						
						<th class="text-left col-sm-2">
							Fee
						</th>
						
						<th class="text-left col-sm-2">
							Doctor's Share
						</th>
						
						
						
						
						
					</tr>
				</thead>
				
				<tbody>

			<?php 
			
			$i=1;
			
            $rowCount = count($rows);
  
            
			
			while($list1 = sqlFetchArray($list)){ 
			
			// $patId = $list1['pid'];
			
			//$patient = sqlQuery("select title,fname from patient_data where pid= $patId");
			 
			?>
					<tr>
						<td>
						<?php echo $i; ?>
						</td>
						<td>
                           <input type='text' name='doctor[]' value='<?php echo $list1['username'];   ?>' style="height:2em;border:1px solid white;" readonly>
			
					
				
					</td>
					
					<?php   
					$code=$list1['code_text'];
					
					if (strpos($code,DR) !== false) {
					$code= 'Consultation';
					}
					?>
					
					
				<td>
                           <input type='text' name='service[]' value='<?php echo $code;   ?>' style="height:2em;border:1px solid white;" readonly>
			
					
				
					</td>

<td>
                           <input type='text' name='fee[]' value='<?php echo $list1['fee'];   ?>' style="height:2em;border:1px solid white;" readonly>
			
					
				
					</td>					
					
					<?php 
					$code_text=$list1['code_text'];
					//echo "select payout,voucherpaid_YN from billing where bill_id='$id' and code_text='$code_text'"."</br>";
	                $share = sqlQuery("select payout,voucherpaid_YN from billing where bill_id='$id' and code_text='$code_text'");
					//echo $share['voucherpaid_YN'];
					if($share['voucherpaid_YN']==1){ ?>
					 <td>
                        <input type='number' name='payout[]' max='<?php echo $list1['fee'];   ?>' value="<?php echo $share['payout']; ?>" style="height:2em;text-align:right;background-color:#E5E4E2;" readonly>
                    </td>
						
					<?php }
					else {
					?>
					 <td>
                        <input type='number' name='payout[]' max='<?php echo $list1['fee']; ?>'  value="<?php echo $share['payout']; ?>" style="height:2em;text-align:right">
 </td>
					<?php }  ?>
 
 
					
                        
						
						<input type='hidden' name='code[]' value='<?php echo $list1['code_text'];  ?>'>
						
						
						
					</tr>
					<?php $i++; }  ?>
					
					<tr><td colspan='5' align='center'><input type='submit' name='submit_form' value='Save'></td></tr>
                  
				</tbody>
			</table>
			</form>
		</div>
 </div></div>

 </html>













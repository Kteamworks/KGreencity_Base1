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


if (isset($_POST['submit'])) {
	
	$_SESSION['from']= $from = $_POST['FromDate'];
	$_SESSION['to'] =$to = $_POST['toDate'];
	$cat=$_POST['category'];
	/*$list = sqlStatement("SELECT b.date,b.fee, b.pid, b.encounter, b.code_type, b.code, b.units,
        b.code_text, fe.date, fe.facility_id, fe.invoice_refno,b.payout,substring(fe.encounter_ipop,1,2)IPOP
        FROM billing AS b 
        JOIN code_types AS ct ON ct.ct_key = b.code_type 
        JOIN form_encounter AS fe ON fe.pid = b.pid AND fe.encounter = b.encounter 
        
        WHERE b.servicegrp_id=8 and b.code not in ('INSURANCE DIFFERENCE AMOUNT','INSURANCE CO PAYMENT')
        AND b.activity = 1 AND b.fee > 0 AND substring(fe.encounter_ipop,1,2) = 'IP' AND
        b.date >= '$from 00:00:00' AND b.date <= '$to 23:59:59' order by b.pid");
		*/
		
		
		if($cat=='IP'){
		
		$list = sqlStatement("select  b.fee,b.code_text,b.provider_id,b.payout,b.voucherpaid_YN, b.bill_id,d.discharge_date dc,b.shared, fe.* from form_encounter fe join billing b
        on fe.encounter=b.encounter
        join t_form_admit d
        on  fe.encounter=d.encounter
        where d.discharge_date>='$from 00:00:00' and d.discharge_date<='$to 23:59:59' and b.activity=1 and b.billed=1 and substring(encounter_ipop,1,2) = '$cat'
		order by d.discharge_date");
		}
		if($cat=='OP'){
			
			
			
			 $list = sqlStatement("select  b.payout,b.voucherpaid_YN,b.bill_id,fe.date dc,b.shared,fe.pid,fe.encounter,b.provider_id, b.fee,b.code_text from form_encounter fe join billing b
        on fe.encounter=b.encounter
        where fe.date>='$from 00:00:00' and fe.date<='$to 23:59:59' and b.activity=1 and b.billed=1 and substring(encounter_ipop,1,2) ='OP'
		order by fe.date"); 
		

			
		}
		
		
		
}



if (isset($_POST['submit_form'])) {

 $j=0;
	foreach($_POST['code'] as $selected){
   
   //echo $patient= $_POST['patient'][$j];
    $code1= $selected;
   	//$fee= $_POST['fee'][$j];
    $payout= $_POST['payout'][$j]; 
   // $hospital= $_POST['hospital'][$j];
    $pid1= $_POST['pid'][$j];
    $encounter1= $_POST['encounter'][$j];
	
	//echo "Update billing set payout= $payout where pid='$pid1' and encounter='$encounter1' and code_text='$code' "; exit;
  sqlQuery("Update billing set payout= $payout where pid='$pid1' and encounter='$encounter1' and code_text='$code1' "); 
   
   
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
  

<form method="post" action="">
    <div class="container col-sm-10">
    <div class="row">
		<div class="col-md-10">
		<table class="table table-bordered table-fixed" id="tab_logic">
		<tr><th>From</th><th>To</th><th>Category</th><th></th><tr>
		<tr><td><input type="text" style="text-align:left;" id="inputField1" name="FromDate"  value="<?php echo $_SESSION['from']; ?>" class="form-control" autocomplete='off' />
		
        </td>
		<td><input type="text" id="inputField2"  name="toDate" value="<?php echo $_SESSION['to']; ?>" class="form-control" autocomplete='off'/></td>
		
		
		<td><select name='category' class='form-control'>
		     <option value="IP">IP</option>
			 <option value="OP">OP</option>
		</select>
		</td>
		<td><input type="submit" style="text-align:left;"  name='submit' class="form-control"/></td>
		</tr>
		</table>
		
		
		</form>
		
		
		<script type="text/javascript" src="jsDatePick.min.1.3.js"></script>
		<script type="text/javascript">
		window.onload = function(){
		new JsDatePick({
			useMode:2,
			target:"inputField1",
			dateFormat:"%Y-%m-%d"
		
		});
		
		new JsDatePick({
			useMode:2,
			target:"inputField2",
			dateFormat:"%Y-%m-%d"
		
		});
	};

	</script>
	
		
		
<form method="post" action="">
		
			<table class="table table-responsive" id="tab_logic">
				<thead>
					<tr class="danger">
						<th class="text-left " style='border:0px solid'>
							S.No.
						</th>
						<th class="text-left"style='border:0px solid' >
							Patient Name
						</th>
					
						<th class="text-left" style='border:0px solid'>
							Bill No
						</th>
						<th class="text-left" style='border:0px solid'>
							Service
						</th>
						<th class="text-left" style='border:0px solid'>
							Doctor
						</th>
						<th class="text-left" style='border:0px solid'>
							Fees
						</th>
						<th class="text-left" style='border:0px solid'>
							Doctor's Share
						</th>
						
					</tr>
				</thead>
				
				<tbody>

			<?php 
			
			//$j=1;
			
          //  $doc_info = sqlStatement("select distinct b.provider_id, b.fee,b.code_text,u.username from billing b
            //    join users u on b.provider_id=u.id 
            //    where bill_id='$id' and activity=1");
  
                
			while($list1 = sqlFetchArray($list)){ 
			$rows[] = $list1;
			}
			 $rowCount = count($rows);
			 
			 
			 for ($i = 0; $i < $rowCount; $i++){
			 
			 $patId = $rows[$i]['pid'];
			 $share_done = $rows[$i]['shared'];
			 $id = $rows[$i]['encounter']; 
			 
			 if( $id != $rows[$i+1]['encounter']){
				 $id = $rows[$i+1]['encounter'];
				 $style = 'border-bottom:1px solid grey';
				
				 
			 }
			 else {
				 $style = 'border-bottom:0px solid grey';
			 }
			 
			 
			 
			
			$patient = sqlQuery("select title,fname,genericname1 from patient_data where pid= $patId");
			
			 
			?>
			
					<tr style='<?php echo $style;  ?>'>
					
						<td style='border:0px'>
						<?php// echo $hr;   ?>
						<?php echo $i+1; ?>
						
						
						</td>
						
						<td style='border:0px solid'><font color='grey'>
                           <input type='text' name='patient[]' value='<?php echo $patient['title'].' '.$patient['fname']; ?>' style="height:2em;border:1px solid white;" readonly></font>
			
					
				
					</td>
					
					
		            <td style='border:0px solid'>
                       <a href="" target="popup" onclick="window.open('doctorshare.php?id=<?php echo $rows[$i]['bill_id']; ?>','popup','width=900,height=600'); return false;"><input type='text' name='cat[]' value='<?php echo $rows[$i]['bill_id'];   ?>'
						style="height:2em;border:1px solid white;" readonly></a>
                    </td>
 
                   
				  <?php   
					$code=$rows[$i]['code_text'];
					
					if (strpos($code,DR) !== false) {
					$code= 'Consultation';
					}
					
					 $uid = $rows[$i]['provider_id'];
					$provider = sqlQuery("select username from users where id='$uid'");
					
					
					
					?>
				  
				
 
 
                       <td style='border:0px solid'>
                           <input type='text' name='patient[]' value='<?php echo $code; ?>' style="height:2em;border:1px solid white;" readonly>
			          </td>
					  
					  <td style='border:0px solid'>
                           <input type='text' name='patient[]' value="<?php echo $provider['username']; ?>" style="height:2em;border:1px solid white;" readonly>
			          </td>
					  
					  <td style='border:0px solid'>
                           <input type='text' name='fee[]' value="<?php echo $rows[$i]['fee']; ?>" style="height:2em;border:1px solid white;" readonly>
			          </td>
					
                         <td style='border:0px solid'>
                        <input type='number' name='payout[]' max="<?php echo $rows[$i]['fee']; ?>" value="<?php echo $rows[$i]['payout']; ?>" style="height:2em;text-align:right">
                         </td>
						
						<input type='hidden' name='pid[]' value="<?php echo $rows[$i]['pid'];  ?>">
						<input type='hidden' name='encounter[]' value="<?php echo $rows[$i]['encounter'];  ?>">
						
						<input type='hidden' name='code[]' value="<?php echo $rows[$i]['code_text'];  ?>">
						
						
					</tr>
					
					<?php //$i++;
					}  ?>
					
					<tr><td colspan='7' align='center'><input type='submit' name='submit_form' value='Save'></td></tr>
                  
				</tbody>
			</table>
			</form>
		</div>
 </div></div>

 </html>

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
	
		$list = sqlStatement("select * from drugs where create_date>='$from 00:00:00' and create_date<='$to 23:59:59' group by invoice");
		
		
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
    <div class="container col-sm-12">
    <div class="row">
		<div class="col-md-10">
		<table class="table table-bordered table-fixed" id="tab_logic">
		<tr><th>From</th><th>To</th><th></th><tr>
		<tr><td><input type="text" style="text-align:left;" id="inputField1" name="FromDate"  value="<?php echo $_SESSION['from']; ?>" class="form-control"  />
		
        </td>
		<td><input type="text" id="inputField2"  name="toDate" value="<?php echo $_SESSION['to']; ?>" class="form-control"/></td>
		
		
		
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
		
			<table class="table table-bordered table-fixed" id="tab_logic">
				<thead>
					<tr class="danger">
						<th class="text-left col-sm-1">
							S.No.
						</th>
						<th class="text-left col-sm-2">
							Invoice No
						</th>
						
						<th class="text-left col-sm-2">
							Supplier
						</th>
						
						<th class="text-left col-sm-2">
							Date
						</th>
						
						
						
						
						
					</tr>
				</thead>
				
				<tbody>

			<?php 
			
			$i=1;
			
            $rowCount = count($rows);
  
                
			while($list1 = sqlFetchArray($list)){ 
			 $dtime = $list1['create_date'];
			 $newDate = date("d-m-Y", strtotime($dtime));
			 $newDate1= date("Y-m-d", strtotime($dtime));
			 //$patId = $list1['pid'];
			
			//$patient = sqlQuery("select title,fname from patient_data where pid= $patId");
			 
			?>
					<tr>
						<td>
						<?php echo $i; ?>
						</td>
						<!--<td> <a href="invoice_bill.php?id=<?php //echo $list1['invoice']; ?>&dtime=<?php //echo $newDate1; ?>">
						 <?php// echo $list1['invoice'];   ?></a>
			            </td>-->
					
					     <td>
                       <a href="" target="popup" onclick="window.open('invoice_bill.php?id=<?php echo $list1['invoice']; ?>&dtime=<?php echo $newDate1; ?>','popup','width=900,height=600'); return false;"> <?php echo $list1['invoice'];   ?></a>
                    </td>
					   
					
					    <td>
                          <input type='text' name='amnt' value='<?php echo $list1['supplier'];   ?>' style="height:2em;border:1px solid white;" readonly>
                        </td>
 
 
  <td class='text-left'>
                        <input type='text' name='amnt' value='<?php echo $newDate;   ?>' style="height:2em;border:1px solid white;" readonly>
 </td>
 
 
					
                      
						
						
						
						
					</tr>
					<?php $i++; }  ?>
			
								
                  
				</tbody>
			</table>
			</form>
		</div>
 </div></div>

 </html>

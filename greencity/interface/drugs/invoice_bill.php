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

 
if(isset($_REQUEST['id']))
	 $id=$_REQUEST['id'];
else
	$id="";

if(isset($_REQUEST['dtime']))
	 $date=$_REQUEST['dtime'];
else
	$date="";




$list = sqlStatement("select * from drugs where invoice='$id' and create_date>='$date 00:00:00' and create_date<='$date 23:59:59'");



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
							Medicine 
						</th>
						
						<th class="text-left col-sm-2">
							Date
						</th>
						
						<th class="text-right col-sm-2">
							M.R.P.
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
						<td> 
						 <?php echo $list1['invoice'];   ?>
			            </td>
					
					    <td>
                          <input type='text' name='amnt' value='<?php echo $list1['name'];   ?>' style="height:2em;border:1px solid white;" readonly>
                        </td>
 
 
                     <td class='text-left'>
                        <input type='text' name='amnt' value='<?php echo $newDate;   ?>' style="height:2em;border:1px solid white;" readonly>
                       </td>
					   
					    <td class='text-right'>
                          <input type='text' name='amnt' value='<?php echo $list1['totalValue'];   ?>' style="text-align:right;height:2em;border:1px solid white;" readonly>
                        </td>
 
					</tr>
					<?php $i++; }  ?>
			<?php $Total_value = sqlQuery("select sum(totalValue) as sum from drugs where create_date>='$date 00:00:00' and create_date<='$date 23:59:59' and invoice = '$id'"); 
                    
				  
			?>
				<tr><th colspan='5' class='text-right'> Total = <?php echo $Total_value['sum'];   ?>  </th>
                </tr>				
                  
				</tbody>
			</table>
			</form>
		</div>
 </div></div>

 </html>

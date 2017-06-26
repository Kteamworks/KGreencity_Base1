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
?>
<html>
<head>
        
        <link rel="stylesheet" href="public/css/default.css" type="text/css">
        <link rel="stylesheet" href="datepicker/public/css/style.css" type="text/css">
		<link type="text/css" rel="stylesheet" href="datepicker/libraries/syntaxhighlighter/public/css/shCoreDefault.css">

<?php html_header_show(); ?>
<title><?php echo $drug_id ? xlt("Edit") : xlt("Add New"); echo ' ' . xlt('Drug'); ?></title>
<link rel="stylesheet" href='<?php echo $css_header ?>' type='text/css'>

<style>

 



input[class=rgt] { text-align:right }

td { font-size:10pt; }


input[type="number"]::-webkit-outer-spin-button,
input[type="number"]::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}
input[type="number"] {
    -moz-appearance: textfield;
     text-align:right;
}


<?php if ($GLOBALS['sell_non_drug_products'] == 2) { ?>
.drugsonly { display:none; }
<?php } else { ?>
.drugsonly { }
<?php } ?>

<?php if (empty($GLOBALS['ippf_specific'])) { ?>
.ippfonly { display:none; }
<?php } else { ?>
.ippfonly { }
<?php } ?>




</style>



</head>

<body class="body_top">
<?php
// If we are saving, then save and close the window.
// First check for duplicates.




if (($_POST['form_save'] || $_POST['form_delete']) && !$alertmsg) {
  $new_drug = false;
  if ($drug_id) {
   if ($_POST['form_save']) { // updating an existing drug
    sqlStatement("UPDATE drugs SET " .
     "name = '"           . escapedff('name')          . "', " .
     "mfr = '"     . escapedff('mfr')    . "', " .
     "quantity = '"       . escapedff('qty')      . "', " .
     "batch = '"  . escapedff('batch') . "', " .
     "pack = '"      . escapedff('pack')     . "', " .
     "expdate = '"           . escapedff('date')          . "', " .
     "mrp = '"           . escapedff('mrp')          . "', " .
     "tradePrice = '"           . escapedff('trade')          . "', " .
     "discount = '"          . escapedff('discount')         . "', " .
     "vat = '"     . numericff('vat')    . "', " .
     "totalValue = '"   . escapedff('total')  . "', " .
	  "free = '"   . escapedff('free')  . "', " .
	   "instock = '"   . escapedff('instock')  . "', " .
     "allow_multiple = "  . (empty($_POST['form_allow_multiple' ]) ? 0 : 1) . ", " .
     "allow_combining = " . (empty($_POST['form_allow_combining']) ? 0 : 1) . ", " .
     "active = "          . (empty($_POST['form_active']) ? 0 : 1) . " " .
     "WHERE drug_id = ?", array($drug_id));
    sqlStatement("DELETE FROM drug_templates WHERE drug_id = ?", array($drug_id));
   }
   else { // deleting
    if (acl_check('admin', 'super')) {
     sqlStatement("DELETE FROM drug_inventory WHERE drug_id = ?", array($drug_id));
     sqlStatement("DELETE FROM drug_templates WHERE drug_id = ?", array($drug_id));
     sqlStatement("DELETE FROM drugs WHERE drug_id = ?", array($drug_id));
     sqlStatement("DELETE FROM prices WHERE pr_id = ? AND pr_selector != ''", array($drug_id));
	 sqlStatement("DELETE FROM product_warehouse WHERE pw_drug_id = ?", array($drug_id));
	 sqlStatement("DELETE FROM drug_sales WHERE drug_id = ?", array($drug_id));
    } 
   }
  }
  else if ($_POST['form_save']) { // saving a new drug
   $new_drug = true;
   
        // if(isset($_POST['supplier'])&& $_POST['supplier']!=""){
			 
			
			 
			  
			  $j=0;
	foreach($_POST['date'] as $selected){
		
		
		 
		$com= $_POST['com'][$j];

		$hb= $_POST['hb'][$j];
		$pallor= $_POST['pallor'][$j];
		$weight= $_POST['weight'][$j];
		$bp= $_POST['bp'][$j];
		$oed= $_POST['oed'][$j];
		$PA= $_POST['PA'][$j];
		$pv= $_POST['pv'][$j];
		
		$exam= $_POST['exam'][$j];
		$advise= $_POST['advise'][$j];
		
	  
		
        if(empty($selected))
			continue;
	
	
	  
//-------------------------------------------------------------------------------//		
		
		 
		
			 $drug_id = sqlInsert("INSERT INTO form_vitals ( " .
    "date,bps,weight,note" .
    
    ") VALUES ( " .
    "'" . $selected       . "', " .
    "'" . $bp          . "', " .
    "'" . $weight          . "', " .
	 "'" .$exam. "' " .
    
    
    ")");
	
	$j++;
		}  
	
  header('location:treatment.php');
	
  //}
		
   

	
  }

  if ($_POST['form_save'] && $drug_id) {
	  
    $tmpl = $_POST['form_tmpl']; 
   // If using the simplified drug form, then force the one and only
   // selector name to be the same as the product name.
   if ($GLOBALS['sell_non_drug_products'] == 2) {
    $tmpl["1"]['selector'] = $_POST['form_name'];
   }
   
   sqlStatement("DELETE FROM prices WHERE pr_id = ? AND pr_selector != ''", array($drug_id));
   for ($lino = 1; isset($tmpl["$lino"]['selector']); ++$lino) {
    $iter = $tmpl["$lino"];
    $selector = trim($iter['selector']);
    if ($selector) {
     $taxrates = "";
     if (!empty($iter['taxrate'])) {
      foreach ($iter['taxrate'] as $key => $value) {
       $taxrates .= "$key:";
      }
     }
     

     // Add prices for this drug ID and selector.
     foreach ($iter['price'] as $key => $value) {
      $value = $value + 0;
   
     } // end foreach price
    } // end if selector is present
   } // end for each selector
   // Save warehouse-specific mins and maxes for this drug.
  // sqlStatement("DELETE FROM product_warehouse WHERE pw_drug_id = ?", array($drug_id));
   foreach ($_POST['form_wh_min'] as $whid => $whmin) {
    $whmin = 0 + $whmin;
    $whmax = 0 + $_POST['form_wh_max'][$whid];
   
   }
  } // end if saving a drug

  // Close this window and redisplay the updated list of drugs.
  //
  echo "<script language='JavaScript'>\n";
  if ($info_msg) echo " alert('$info_msg');\n";
  echo " if (opener.refreshme) opener.refreshme();\n";
  if ($new_drug) {
	  
	 // echo " window.location.href='add_edit_drug.php?drug=$drug_id&lot=0'\n";
	  echo " window.close();\n";
  } else {
   echo " window.close();\n";
  }
  echo "</script></body></html>\n";
  exit();
}

if ($drug_id) {
  $row = sqlQuery("SELECT * FROM drugs WHERE drug_id = ?", array($drug_id));
  $tres = sqlStatement("SELECT * FROM drug_templates WHERE " .
   "drug_id = ? ORDER BY selector", array($drug_id));
}
else {
  $row = array(
    'name' => '',
    'active' => '1',
    'allow_multiple' => '1',
    'allow_combining' => '',
    'ndc_number' => '',
    'on_order' => '0',
    'reorder_point' => '0',
    'max_level' => '0',
    'form' => '',
    'size' => '',
    'unit' => '',
    'route' => '',
	'supplier' => '',
    'cyp_factor' => '',
    'related_code' => '',
  );
}
?>





<form method='post' name='theform' action=''>
<center>


	

 
 <br><br><br><br><br><br>
 
 <table border='0' width='100%'   id="dataTable" style=" border: 1px solid black;"> 
 <tr>
  <th nowrap>S.No.</th>
  <th nowrap>Date</th>
  <th nowrap>Complaints</th>
   <th nowrap>HB </br>% </br>Unire RE</th>
   
   <th nowrap>Pallor</th>
    <th nowrap>Weight</th>
   <!--<th nowrap>New</br>Medicine</th>-->
   <th nowrap>BP</th>
   <th  nowrap>Oedema </br>Number</th>
  <th  nowrap>PA</th>
  <th  nowrap>PV</th>
  <th  nowrap>Examination Findings</th>
  <th  nowrap>Treatment And Advise </br>Type</th>
  
 </tr>
 
 
 
 <?php
 include_once('dbconnect.php');
 $list = "SELECT * FROM `form_vitals`";
 $rid=mysqli_query($conn,$list);
 echo $num=mysqli_num_rows($rid);
 $j=1;
   while($result=mysqli_fetch_array($rid)) 

   {  
   ?>
	   <tr>
	   <td>
	    <?php echo $j;  ?>
	   </td>
	   
  <td>
   <input type='text' size="10"  maxlength='80' value='<?php echo $result['date'] ?>'  style='width:100%'/>
  </td>
  
    <td>
   <input type='text'  maxlength='80' value='' style='width:100%'  />
  </td>
  

  <td>
   <input type='text' size="10"  maxlength='80' value=''  style='width:100%' />
  </td>
  
  <td>
   <input type='text' size="10"  maxlength='80' value=''  style='width:100%' />
  </td> 
  
  <td>
   <input type='text' size="10"  maxlength='80' value='<?php echo $result['weight']; ?>'  style='width:100%' />
  </td>
  
  <td>
   <input type='text' size="10"  maxlength='80'   value='<?php echo $result['bps']; ?>' style='width:100%'  />
  </td>
  
   <td>
 <input type='text' size="10"  maxlength='80'  style='width:100%'  />
 </td>
  

  <td>
  <input type='text' size="10" maxlength='80' value=''  style='width:100%' />
  </td>
  
  
  
  <td>
  <input type='text' size="10"  maxlength='80' value=''  style='width:100%' />
   
  </td>
  
 
  
  <td>
 
   <input type='text' size="10" maxlength='80' value=''  style='width:100%' />
 
  </td>
  
   <td>
  <input type='text' size="10"   value='<?php echo $result['note']; ?>'  style='width:100%'/>
  </td>
  
	   
	   </tr>
	   
	   
<?php 
$j++;
  } 

 
 
 
 $i=$num;
  while($i<=$num+5) 
  {
  
     
?> 
 
 <tr>
 
   <td>
   <?php echo $i ?>
  </td> 
  



  <td>
   <input type='text' size="10" name='date[]' maxlength='80' value=''  style='width:100%'placeholder='yyyy-mm-dd'/>
  </td>
  
    <td>
   <input type='text' name='com[]' maxlength='80' value='' style='width:100%' />
  </td>
  

  <td>
   <input type='text' size="10" name='hb[]' maxlength='80' value=''  style='width:100%'  />
  </td>
  
  <td>
   <input type='text' size="10" name='pallor[]' maxlength='80' value=''  style='width:100%'  />
  </td> 
  
  <td>
   <input type='text' size="10" name='weight[]' maxlength='80' value=''  style='width:100%'  />
  </td>
  
  <td>
   <input type='text' size="10" name='bp[]' maxlength='80'   style='width:100%'  />
  </td>
  
   <td>
 <input type='text' size="10" name='oed[]' maxlength='80'  style='width:100%' />
 </td>
  

  <td>
  <input type='text' size="10" name='PA[]' maxlength='80' value=''  style='width:100%'   />
  </td>
  
  
  
  <td>
  <input type='text' size="10" name='pv[]' maxlength='80' value=''  style='width:100%' />
   
  </td>
  
 
  
  <td>
 
   <input type='text' size="10" name='exam[]' maxlength='80' value=''  style='width:100%'  />
 
  </td>
  
   <td>
  <input type='text' size="10" name='advise[]'  value=''  id="<?php echo 'd'.$i; ?>" style='width:100%' />
  </td>
  
  
  
  </tr>
  
  <?php
   

 $i++;  

         
}
 

?>


 
  
  

   </table>
 

<p>
<!--
<--INPUT type="button" value="Add Row" onclick="addRow('dataTable')" /> -->
<input type='submit' name='form_save' value='<?php echo xla('Save'); ?>' />

<?php if (acl_check('admin', 'super')) { ?>
&nbsp;
<input type='submit' name='form_delete' value='<?php echo xla('Delete'); ?>' style='color:red' />
<?php } ?>

&nbsp;
<input type='button' value='<?php echo xla('Cancel'); ?>' onclick='window.close()' />

</p>

</center>
</form>

        <script type="text/javascript" src="datepicker/public/javascript/jquery-1.12.0.js"></script>
        <script type="text/javascript" src="public/javascript/zebra_datepicker.js"></script>
        <script type="text/javascript" src="datepicker/public/javascript/core.js"></script>



 <script type="text/javascript" src="datepicker/libraries/syntaxhighlighter/public/javascript/XRegExp.js"></script>
        <script type="text/javascript" src="datepicker/libraries/syntaxhighlighter/public/javascript/shCore.js"></script>
        <script type="text/javascript" src="datepicker/libraries/syntaxhighlighter/public/javascript/shLegacy.js"></script>
        <script type="text/javascript" src="datepicker/libraries/syntaxhighlighter/public/javascript/shBrushJScript.js"></script>
        <script type="text/javascript" src="datepicker/libraries/syntaxhighlighter/public/javascript/shBrushXML.js"></script>

        <script type="text/javascript">
            SyntaxHighlighter.defaults['toolbar'] = false;
            SyntaxHighlighter.all();
        </script>
		
		
		
		

  
    <script src="typeahead.min.js"></script>
    <script>
    $(document).ready(function(){
    $('input.typeahead').typeahead({
        name: 'typeahead',
        remote:'search.php?key=%QUERY',
        limit : 3
    });
});
    </script>
    <style type="text/css">


.typeahead {
	background-color: #FFFFFF;
}
.typeahead:focus {
	border: 2px solid #0097CF;
}
.tt-query {
	box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset;
}
.tt-hint {
	color: #999999;
}
.tt-dropdown-menu {
	background-color: #FFFFFF;
	border: 1px solid rgba(0, 0, 0, 0.2);
	border-radius: 8px;
	box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
	margin-top: 12px;
	padding: 8px 0;
	width: 422px;
}
.tt-suggestion {
	font-size: 24px;
	line-height: 24px;
	padding: 3px 20px;
}
.tt-suggestion.tt-is-under-cursor {
	background-color: #0097CF;
	color: #FFFFFF;
}
.tt-suggestion p {
	margin: 0;
}
</style>		
		
		



<div width="100%">		
<script language="JavaScript">



<?php
 if ($alertmsg) {
  echo "alert('" . htmlentities($alertmsg) . "');\n";
 }
?>
</script>
</div>
</body>
</html>

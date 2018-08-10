<?php   
$fake_register_globals=false;
$sanitize_all_escapes=true;

require_once("../../globals.php");
require_once("$srcdir/acl.inc");
require_once("$srcdir/api.inc");
require_once("codes.php");
require_once("../../../custom/code_types.inc.php");
require_once("../../drugs/drugs.inc.php");
require_once("$srcdir/formatting.inc.php");
require_once("$srcdir/options.inc.php");
require_once("$srcdir/formdata.inc.php");

 //setpid($_GET['set_pid']);
   $gchid = $_GET['set_pid']; 
   $user=$_SESSION['authUser'];
  
   $inventory_id=111;
   
   
  $detail=sqlQuery("select fname,e.pid as ID,e.date, e.encounter as visit from patient_data p Join form_encounter e on p.pid=e.pid 
                    where genericname1='$gchid' order by e.date desc limit 1");
   //$encounter = $detail['visit'];
   $prescription_id=$encounter;
   $pid = $detail['ID'];
   $_SESSION['patId']= $pid ; 
   $encounter=$_SESSION['visit']=$detail['visit'];
   $tmp1 = sqlQuery("SELECT id from users WHERE username='$user'");
   $user_id=$tmp1['id'];
   
   
   $bill = sqlQuery("select bill from billing where pid = $pid and encounter='$encounter' order by id desc limit 1");
   $checkBill = $bill['bill']; 
   $checkBill = $checkBill + 1; 
   
    $tmpid=sqlQuery("select max(id) as id from billing "); 
    $tmpid1=sqlQuery("select max(sequence_no) as id1 from ar_activity "); 
    $_SESSION['maxId1']=$tmpid1['id1']; 
    $_SESSION['maxId']=$tmpid['id'];




   if(isset($_POST['submit']))
   {
	  
	   $sum_total  = $_POST['sum_total'];
	  
	   
	sqlQuery("insert into payments(pid,encounter,amount1,dtime,user,towards,stage,bill)
            values('$pid','$encounter','$sum_total',NOW(),'$user',2,'pharm','$checkBill')");    
	   
	   
	   
	$j=0;
    foreach($_POST['list'] as $selected){
		
	$drugs = sqlQuery("SELECT drug_id FROM drugs WHERE name ='$selected'");
	$drug_idd = $drugs['drug_id'];
	
	$batch = $_POST['batch'][$j];
	 $qty = $_POST['qty'][$j];
    $price = $_POST['price'][$j];
	$a = $qty + 1; 
	$ar_activity =  $a * $price ; 
		
   
	$fee = $price * $qty ;
	
	 $res =sqlQuery("SELECT * FROM `codes` WHERE `code_type`=11 and code='$selected'");
	  //echo "SELECT * FROM `codes` WHERE `code_type`=11 and code='$selected'"; 	
	  $servicegrp_id = $res['code_type'];
	  $cod = $res['code']; 
	  $code=str_replace("'", "", $cod);
     //$code=mysqli_real_escape_string($conn,$res['code']); 
	  $service_id=$res['service_id'];
	  $cod_text = $res['code_text'];
	  $code_text=str_replace("'", "", $cod_text);
	
	 $bil = sqlInsert("insert into billing (date,encounter,servicegrp_id,service_id, code_type, code, code_text, pid, authorized, user, groupname,units,fee,activity,modifier,schedule_h,bill)
     values
    (NOW(),'$encounter', '$servicegrp_id', '$service_id', 'Pharmacy Charge', '$code' ,'$code_text', '$pid','1','$user_id','Default','$qty','$fee',1,1,'    	$schedule_h','$checkBill')");
	
	sqlQuery("insert into ar_activity(pid,encounter,code_type,post_time,pay_amount,code)values('$pid','$encounter','Pharmacy Charge',NOW(),'$ar_activity','$selected')");
	
	 $drug_id = sqlInsert("INSERT INTO drug_sales(drug_id, inventory_id, prescription_id, pid, user, sale_date, quantity, fee,encounter,billed)
 values
 ('$drug_idd', '$inventory_id', '$prescription_id', '$pid', '$user', Now(),'$qty', '$price','$encounter','1')");
	
	
	 sqlQuery("Update drugs set totalStock= totalStock - ? where drug_id=?",array($qty,$drug_idd));   
	  sqlQuery("Update drug_templates set quantity= quantity - ? where drug_id=?",array($qty,$drug_idd));   
	  sqlQuery("Update drug_inventory set on_hand= on_hand - ? where drug_id=?",array($qty,$drug_idd));  
	  sqlQuery("Update billing_main_copy set total_charges=total_charges + ? where encounter=?",array($price,$encounter));  
	
   // $sql  = sqlInsert("INSERT INTO dynamicrow (list, name, email) VALUES ('$selected', '$batch', '$price')");
	
	
    $j++ ; 
   }
	
	header('location:../../patient_file/front_payment_pharmacy.php');
	
   }



?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>jQuery Add / Remove Table Rows Dynamically</title>
<style type="text/css">
    form{
        margin: 20px 0;
    }
    form input, button{
        padding: 5px;
    }
    table{
        width: 100%;
        margin-bottom: 20px;
		border-collapse: collapse;
    }
    table, th, td{
        border: 1px solid #cdcdcd;
    }
    table th, table td{
        padding: 10px;
        text-align: left;
    }
</style>
<meta name="description" content="">
		<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1">
		<link rel="stylesheet" href="css/normalize.css">
		<link rel="stylesheet" href="css/stylesheet.css">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
		<script src="js/jquery.js"></script>
		<script src="../dist/js/standalone/selectize.js"></script>
		<script src="js/index.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
		<link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
		
		
<script type="text/javascript">
    $(document).ready(function(){
        $(".add-row").click(function(){
            var price = $("#price").val();
            var batch = $("#batch").val();
			var list = $("#select-tools").val();
			var qty = $("#qty").val();
			var amt = $("#sum").val();
			$("#t").css("display",'');
			
			 
			 var sum = +amt;
              $(".sum").each(function(){
              sum += +$(this).val();
		
               });
               $(".total").val(sum.toFixed(2));
			 
             
			 
            //var markup = "<tr><td><input type='checkbox' name='record[]'></td><td>" + name + "</td><td>" + email + "</td><td>" + list + "</td></tr>";
			
			var markup = "<tr><td><input type='checkbox' name='record[]'></td>  <td><input type='text' name='list[]' style='border:1px solid white;' value='"+ list +"' readonly ></td><td><input type='text' name='batch[]' value='"+ batch +"' style='border:1px solid white;'></td>  <td><input type='text' name='price[]' value='"+ price +"' readonly style='border:1px solid white;text-align:right;'></td><td><input type='text' name='qty[]'style='border:1px solid white;text-align:right'  value="+qty+"  ></td><td><input type='text' name='amt[]' class='sum'  style='border:1px solid white;text-align:right;' value="+amt+"></></tr>";
            $("#t").append(markup);
        });
        
        // Find and remove selected table rows
        $(".delete-row").click(function(){
            $("table tbody").find('input[name="record[]"]').each(function(){
            	if($(this).is(":checked")){
                    $(this).parents("tr").remove();
                }
			
            });
			
		
			var sum = 0;
			 $(".sum").each(function(){
              sum += +$(this).val();
		
               });
               $(".total").val(sum.toFixed(2));
			
			
			
        });
    });    
</script>
</head>


<body>
    
	
	  <?php $result =  sqlStatement("SELECT name, drug_id, expdate  FROM drugs where expdate > '$exp' and totalStock > 1");    
	  
	  $i=1;
			while ($jarray = sqlFetchArray($result))
             {
              $rows[] = $jarray;
	
             }
 
            $rowCount = count($rows);
  
                 for($k=0;$k<$rowCount;$k++){
                            $id=$rows[$k]['drug_id'];
						    $id1=str_replace("'", "", $id);
						    $title=$rows[$k]['name'];
						    $title1=str_replace("'", "", $title);
			
                      }
	  
				?>	
		<div class="row">
		<div class="col-md-9">		
				
				<table class="table table-bordered table-fixed" id="tab_logic">
		<tr><th>ID</th><th>Patient Name</th><th>Visit ID</th><th>Pid</th><tr>
		<tr><td><input type="text" style="text-align:left;" id='gchid' name='gch'  value="<?php echo $gchid ?>" class="form-control" readonly required/></td>
		<td><input type="text" style="text-align:left;" id='pname' name='patname'  value="<?php echo $detail['fname']; ?>" class="form-control" readonly required/></td>
		<td><input type="text" style="text-align:left;" id='visitid' name='visit'  value="<?php echo $detail['visit']; ?>" class="form-control" readonly required/></td>
		<td><input type="text" style="text-align:left;" id='pid' name='pid'  value="<?php echo $detail['ID']; ?>" class="form-control" readonly required/></td>
		
		</tr>
		</table>
				
				
				<table class="table table-bordered table-fixed"  id="tab_logic">
				<tr class="danger"><th class="col-md-3">Medicine</th><th>Batch</th><th>Price</th><th>Quantity</th><th>Amount</th><th>#</th><tr>
				<tr>
				<td><select id="select-tools" name='list' placeholder="Pick a medicine..."></select></td>
				<td><input type="text" id="batch" name='batch' placeholder="" class="form-control"></td>
				<td><input type="text" name='price' id="price" value='' placeholder="" class="form-control" readonly style="background-color:white"></td>
				<td><input type="text" name='qty' id="qty" placeholder="" class="form-control "></td>
				<td><input type="text" name='amt' id="sum" placeholder="" class="form-control text-right"></td>
				<td><input type="button" class="add-row" value="Add Row"></td></tr>
				
				</table>
		    </div>
				</div>
				<script>
				$('#select-tools').selectize({
					maxItems: 1,
					valueField: 'id',
					labelField: 'title',
					searchField: 'title',
					options: [
						 <?php
        
                            for($k=0;$k<$rowCount;$k++){
                            $id=$rows[$k]['drug_id'];
						    $id1=str_replace("'", "", $id);
						    $title=$rows[$k]['name'];
						    $title1=str_replace("'", "", $title);
				  
                        ?>  
						{id: '<?php echo $title1; ?>', title: '<?php echo $title1; ?>'},
					<?php } ?>
					],
					create: false
				});
				
				
				
				
				</script>
		
	<form action='' method='POST'>
	
	<div class='row'>
    <div class='col-md-9'>
	
    <table id='t' class="table table-bordered table-fixed" style='display:none'>
        <thead>
            <tr>
                <th>Select</th>
                <th>Name</th>
                <th>Batch</th>
				<th class="text-right">Price</th>
				<th class="text-right">Quantity</th>
				<th class='text-right'>Amount</th>
            </tr>
        </thead>
        <tbody>
            
        </tbody>
		<tfoot>
  <tr>
     <th colspan='6'>Total:<input type='text' name='sum_total' style="float:right;width:18%;text-align:right" class='total'></th>
     
  </tr>
 </tfoot>
    </table>
	
	
	
    <button type="button" class="delete-row">Delete Row</button>
	<input type='submit' name='submit' value='Take Payment'>
	<!--<input type='text' name='subtotal' value='' style="float:right;">-->
	
	</div>

	</div>
	
	</form>
	
	
	
</body> 

<script>

 $("#select-tools").change(function()
{	
var id=$(this).val();

var dataString = 'id='+ id;
var tmp = 1;
$("#qty").val(tmp);
$("#sum").val('0');
$.ajax
({
type: "POST",

url: "ajaxMedDynamic.php",
data: dataString+"&action=med",
cache: false,
success: function(html)
{
$("#batch").val(html);

} 
});

$.ajax
({
type: "POST",

url: "ajaxMedDynamic.php",
data: dataString+"&action=medPrice",
cache: false,
success: function(html)
{
$("#price").val(html);

} 
});
$.ajax
({
type: "POST",

url: "ajaxMedDynamic.php",
data: dataString+"&action=expdate",
cache: false,
success: function(html)
{
$("#expdate").val(html);

} 
});

});

$(document).on("focus", "#sum", function() {
 
   
    var p = $("#price").val();
	var q = $("#qty").val();
	var sum = (+p)*(+q);
	$("#sum").val(sum.toFixed(2));
	
});

</script>



</html>                            
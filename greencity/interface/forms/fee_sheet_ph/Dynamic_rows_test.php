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







   if(isset($_POST['submit']))
   {
	$j=0;
    foreach($_POST['list'] as $selected){
	
	$batch = $_POST['batch'][$j];
	
	$price = $_POST['price'][$j];
	
	
    $sql  = sqlInsert("INSERT INTO dynamicrow (list, name, email) VALUES ('$selected', '$batch', '$price')");
	
	
    $j++ ; 
   }
	
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
<script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
<link rel="stylesheet" href="css/normalize.css">
		<link rel="stylesheet" href="css/stylesheet.css">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>	
		<script src="js/jquery.min.js"></script>
		<script src="selectize.js"></script>
		<script src="js/index.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        $(".add-row").click(function(){
            var price = $("#price").val();
            var batch = $("#batch").val();
			var list = $("#select-tools").val();
            //var markup = "<tr><td><input type='checkbox' name='record[]'></td><td>" + name + "</td><td>" + email + "</td><td>" + list + "</td></tr>";
			var markup = "<tr><td><input type='checkbox' name='record[]'></td>  <td><input type='text' name='list[]' style='border:1px solid white;' value='"+ list +"' readonly ></td><td><input type='text' name='batch[]' value='"+ batch +"' style='border:1px solid white;'></td>  <td><input type='text' name='price[]' value='"+ price +"' readonly style='border:1px solid white;'></td></tr>";
            $("table tbody").append(markup);
        });
        
        // Find and remove selected table rows
        $(".delete-row").click(function(){
            $("table tbody").find('input[name="record[]"]').each(function(){
            	if($(this).is(":checked")){
                    $(this).parents("tr").remove();
                }
            });
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
				   
                     
					 Medicines: </br><select id="select-tools" name='list' placeholder="Pick a medicine..."></select>
					 Batch:<input type="text" id="batch" name='batch' placeholder="">
		             Price:<input type="text" name='price' id="price" value='' placeholder="">
		             Quantity:<input type="text" name='qty' id="qty" placeholder="">
					 Amount:<input type="text" name='amt' id="amt" placeholder="">
					 
		             
    	             <input type="button" class="add-row" value="Add Row">
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
	
	                 
       
        
    
    <table>
        <thead>
            <tr>
                <th>Select</th>
                <th>Name</th>
                <th>Batch</th>
				<th>M.R.P</th>
            </tr>
        </thead>
        <tbody>
            
        </tbody>
    </table>
    <button type="button" class="delete-row">Delete Row</button>
	<input type='submit' name='submit'>
	
	</form>
</body> 

<script>

 $("#select-tools").change(function()
{	
var id=$(this).val();

var dataString = 'id='+ id;
var tmp = 1;
$("#qty").val(tmp);
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
</script>



</html>                            
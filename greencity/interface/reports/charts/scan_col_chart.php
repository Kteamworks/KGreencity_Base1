<?php
include('../../../sites/default/sqlconf.php');
$data[] = array('Category','Rupees');
$sql = "select code as subject,sum(fee) as number from billing where activity = 1 and code_type = 'Scans' group by code";
$query = mysql_query($sql);
while($result = mysql_fetch_array($query))
{
$data[] = array($result['subject'],(int)$result['number']);
  
}




//	$data = array($data);			
echo json_encode($data);
?>

<?php
include('../../../sites/default/sqlconf.php');

$data[] = array('Category','Number');
$sql = "select code_type as subject,sum(fee) as number from billing where  activity=1 and code!='' group by code_type order by number";
$query = mysql_query($sql);
while($result = mysql_fetch_array($query))
{
	if($result['subject']=='Doctor Charges')
	 {$result['subject'] = 'Consultation';}
     if($result['subject']=='Pharmacy Charge')
		 {$result['subject'] = 'Pharmacy';}
	  if($result['subject']=='Ward Charges')
		 {$result['subject'] = 'In Patient';}
$data[] = array($result['subject'],(int)$result['number']);
  
}




//	$data = array($data);			
echo json_encode($data);
?>

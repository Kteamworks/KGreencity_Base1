<?php

include('../../../sites/default/sqlconf.php');





$sql = "select code_type as subject,count(*) as number from billing where  activity=1 and code!=''	 group by code_type";
$query = mysql_query($sql);
while($result = mysql_fetch_array($query))
{
	 if($result['subject']=='Doctor Charges')
	 {$result['subject'] = 'Consultation';}
     if($result['subject']=='Pharmacy Charge')
		 {$result['subject'] = 'Pharmacy';}
	  if($result['subject']=='Ward Charges')
		 {$result['subject'] = 'In Patient';}
  $rows[]=array("c"=>array
                          ("0"=>array("v"=>$result['subject'],"f"=>NULL),
						   "1"=>array("v"=>(int)$result['number'],"f" =>NULL))
			   );
  
}

echo $format = '{
"cols":
[
{"id":"",
"label":"Subject",
"pattern":"",
"type":"string"
},

{"id":"",
"label":"Number",
"pattern":"",
"type":"number"}
],
"rows":'.json_encode($rows).'}';

	

?>









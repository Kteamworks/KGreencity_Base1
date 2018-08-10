<?php

include('../../../sites/default/sqlconf.php');




$sql = "select code as subject,count(*) as number from billing where activity = 1 and code_type = 'Scans' group by code";
$query = mysql_query($sql);
while($result = mysql_fetch_array($query))
{
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









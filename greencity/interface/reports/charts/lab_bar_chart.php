<?php
//include('db.php');
$sql = mysql_connect("localhost","root","");
if(!$sql)
{
	echo "Connection Not Created";
}
$con = mysql_select_db("greencity");
if(!$sql)
{
	echo "Database Not Connected";
}


$data[] = array('Category','Number');
$sql = "select code as subject,count(*) as number from billing where activity = 1 and code_type = 'Scans' group by code";
$query = mysql_query($sql);
while($result = mysql_fetch_array($query))
{
$data[] = array($result['subject'],(int)$result['number']);
  
}




//	$data = array($data);			
echo json_encode($data);
?>

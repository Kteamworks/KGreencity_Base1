<html>
  <head>
    <!--Load the AJAX API-->
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <script type="text/javascript">
    
    // Load the Visualization API and the piechart package.
    google.charts.load('current', {'packages':['corechart']});
      
    // Set a callback to run when the Google Visualization API is loaded.
    google.charts.setOnLoadCallback(pie_chart);
    google.charts.setOnLoadCallback(column_chart);
    google.charts.setOnLoadCallback(bar_chart);
    google.charts.setOnLoadCallback(line_chart);
      
    function pie_chart() {
      var jsonData = $.ajax({
          url: "cat_rev_pie.php",
          dataType: "json",
          async: false
          }).responseText;
          
      // Create our data table out of JSON data loaded from server.
	 // alert(jsonData);return false;
      var data = new google.visualization.DataTable(jsonData);

      // Instantiate and draw our chart, passing in some options.
      var chart = new google.visualization.PieChart(document.getElementById('piechart_div'));
      chart.draw(data, {width: 1500, height: 540,is3D: true});
    }

   function column_chart() {
		
		var jsonData = $.ajax({
			url: 'cat_rev_col.php',
    		dataType:"json",
    		async: false,
			success: function(jsonData)
				{
					var data = new google.visualization.arrayToDataTable(jsonData);	
        			var chart = new google.visualization.ColumnChart(document.getElementById('columnchart_values'));
					chart.draw(data);
					
				}	
			}).responseText;
  }
     
 /* function line_chart() 
  {
	  	var jsonData = $.ajax({
			url: 'column_chart.php',
    		dataType:"json",
    		async: false,
			success: function(jsonData)
				{
					var options = 
					{
						legend: 'none',
						hAxis: { minValue: 0, maxValue: 9 },
						curveType: 'function',
						pointSize: 7,
						dataOpacity: 0.3
					};
					var data = new google.visualization.arrayToDataTable(jsonData);	
        			 var chart = new google.visualization.LineChart(document.getElementById('line_chart'));
                     chart.draw(data, options);
					
				}	
			}).responseText;
	  
    } 
	*/
    </script>
  </head>

  <body>
    <!--Div that will hold the pie chart-->
	<h1 align='center'>Revenue Based on Visits </h1>
	<div style="font: 21px arial; padding: 10px 0 0 100px;"></div>
   <div id="piechart_div"></div>
   <div style="font: 21px arial; padding: 10px 0 0 100px;"></div>
	<div id="columnchart_values" style="width: 900px; height: 300px;"></div><br>
	<div style="font: 21px arial; padding: 10px 0 0 100px;"></div>
	<div id="bar_chart" style="width: 900px; height: 300px;"></div>
	<!--<div style="font: 21px arial; padding: 10px 0 0 100px;">Line Chart</div>
	<div id="line_chart" style="width: 900px; height: 300px;"></div>-->
  </body>
</html>
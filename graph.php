<!doctype html>
<?php

    // First we execute our common code to connection to the database and start the session 
    require("common.php"); 
     
    // At the top of the page we check to see whether the user is logged in or not 
    if(empty($_SESSION['user'])) 
    { 
        // If they are not, we redirect them to the login page. 
        header("Location: login.php"); 
         
        // Remember that this die statement is absolutely critical.  Without it, 
        // people can view your members-only content without logging in. 
        die("Redirecting to login.php"); 
    } 

$userid = $_SESSION['user']['id'];

// 1st query builds the bar chart of books per year
$sql = 'Select read, count(read) as qty from books where statusid = 1 and userid = ' . $userid . ' group by read';

try {
  $dbh = new PDO("sqlite:data/datastore.db");
} catch (PDOException $e) {
  echo 'Connection failed: ' . $e->getMessage();
}

// $db = new MyDB();
// $result = $dbh->query($sql);
$arr = '';

//	while ($row = $result->fetchArray()) {
	foreach ($dbh->query($sql) as $row) {
		$arr = $arr . '[\'' . $row['read'] . '\', ' . $row['qty'] . '],';
	}	
	
// 2nd query builds pie chart of genres
$sql = 'Select count(books.genreID) as qty, genre.genre as gname from books join genre on books.genreID = genre.genreID where userid = ' . $userid . ' and statusid = 1 group by books.genreID';	


// $db = new MyDB();
// $result = $dbh->query($sql);
$brr = '';
//	while ($row = $result->fetchArray()) {
	foreach ($dbh->query($sql) as $row) {
		$brr = $brr . '[\'' . $row['gname'] . '\', ' . $row['qty'] . '],';
	}
	
// 3rd query builds pie chart of mediums
$sql = 'Select count(books.formatID) as qty, format.format as fname from books join format on books.formatID = format.formatID where userid = ' . $userid . ' and statusid = 1 group by books.formatID';

// $db = new MyDB();
// $result = $dbh->query($sql);
$crr = '';
//	while ($row = $result->fetchArray()) {
	foreach ($dbh->query($sql) as $row) {
		$crr = $crr . '[\'' . $row['fname'] . '\', ' . $row['qty'] . '],';
	}	
	
	
?>

<html>
  <head>
	<meta charset='utf-8'>
	<title>Reading Graph</title>
	<link rel="stylesheet" href="css/form.css" />
	<link href='http://fonts.googleapis.com/css?family=Ubuntu|Roboto|Roboto+Slab' rel='stylesheet' type='text/css'>
    <!--Load the AJAX API-->
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">
    
      // Load the Visualization API and the piechart package.
      google.load('visualization', '1.0', {'packages':['corechart']});

      // Set a callback to run when the Google Visualization API is loaded.
      google.setOnLoadCallback(drawChart);
      google.setOnLoadCallback(drawPieChart);
      google.setOnLoadCallback(drawFormatChart);

      // Callback that creates and populates a data table,
      // instantiates the pie chart, passes in the data and
      // draws it.
      function drawChart() {

        // Create the data table.
        var data = new google.visualization.DataTable();
		    data.addColumn('string', 'Year');
            data.addColumn('number', 'Read');
            data.addRows([
			<?php echo $arr; ?>
        //['', 0]
        ]);
        
        function selectHandler () {
          var selectedItem = chart.getSelection()[0];
          var url;
          if (selectedItem) {
            var topping = data.getValue(selectedItem.row, 0);
            url = 'index.php?yr=' + topping;
            window.location = url;
            //alert(url);
          }
        }

        // Set chart options
        var options = {'title':'Books Read by Year',
					   'is3D':true,
                       'width':600,
                       'height':600};

        // Instantiate and draw our chart, passing in some options.
        var chart = new google.visualization.BarChart(document.getElementById('chart_div'));
        google.visualization.events.addListener(chart, 'select', selectHandler);
             
        chart.draw(data, options);
      }  // end of draw Chart
      
      // Callback that creates and populates a data table,
      // instantiates the pie chart, passes in the data and
      // draws it.
      function drawPieChart() {

        // Create the data table.
        var data = new google.visualization.DataTable();
		    data.addColumn('string', 'Genre');
            data.addColumn('number', 'Read');
            data.addRows([
				<?php echo $brr; ?>
        //['', 0]
        ]);
        
        function selectHandler () {
          var selectedItem = chart.getSelection()[0];
          var url;
          if (selectedItem) {
            var topping = data.getValue(selectedItem.row, 0);
            url = 'index.php?sort=genre&' + topping;
            window.location = url;
            //alert(url);
          }
        }

        // Set chart options
        var options = {'title':'Books by Genre',
					   'is3D':true,
                       'width':600,
                       'height':600};

        // Instantiate and draw our chart, passing in some options.
        var chart = new google.visualization.PieChart(document.getElementById('genre_div'));
        google.visualization.events.addListener(chart, 'select', selectHandler);
             
        chart.draw(data, options);
      } // end of drawPieChart
      
      function drawFormatChart() {

        // Create the data table.
        var data = new google.visualization.DataTable();
		    data.addColumn('string', 'Format');
            data.addColumn('number', 'Read');
            data.addRows([
				<?php echo $crr; ?>
        //['', 0]
        ]);
        
        function selectHandler () {
          var selectedItem = chart.getSelection()[0];
          var url;
          if (selectedItem) {
            var topping = data.getValue(selectedItem.row, 0);
            url = 'index.php?sort=format&' + topping;
            window.location = url;
            //alert(url);
          }
        }

        // Set chart options
        var options = {'title':'Books by Format',
					   'is3D':true,
                       'width':600,
                       'height':600};

        // Instantiate and draw our chart, passing in some options.
        var chart = new google.visualization.PieChart(document.getElementById('format_div'));
        google.visualization.events.addListener(chart, 'select', selectHandler);
             
        chart.draw(data, options);
      } // end of drawFormatChart
      
    </script>
    <script type='text/javascript' src="jquery-1.10.2.min.js"></script>
	<script type='text/javascript' src='book.js'></script>
  </head>

  <body>
  	
<?php  	

echo "<header>";
echo "<h1>Reading Graph</h1>";
echo "</header>";

echo "<nav>";
echo "<ul>";
echo "<li><a href='index.php?pg=read'>Home</a></li>";
echo "<li><a href='index.php?pg=wish'>Wish List</a></li>";
echo "<li><a href='index.php?pg=lib'>Library</a></li>";
echo "<li><a href='graph.php'>Graph</a></li>";
echo "<li><a href='add.php'>Add New</a></li>";
echo "<li><a href='logout.php'>Logout</a></li>";
echo "</ul>";
echo "</nav>";

?>  	
  
    <!--Divs that will hold the bar and pie charts -->
<table><tr>
    <td><div id="chart_div" style="border: 1px solid #ccc"></td><td></div><div id="genre_div" style="border: 1px solid #ccc"></div></td>
    </tr>
    <tr>
    <td><div id="format_div" style="border: 1px solid #ccc"</td><td>&nbsp;</td>
    </tr>
</table>

  </body>
</html>
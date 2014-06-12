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
    
// open the database
try {
  $dbh = new PDO("sqlite:data/datastore.db");
} catch (PDOException $e) {
  echo 'Connection failed: ' . $e->getMessage();
}
$pg = $_SESSION["pg"];


if (isset($_GET["sort"])) {
	$sortval = $_GET["sort"];
}
else {
	$sortval = "";
}

$result_genre = $dbh->query('SELECT genre, genreID FROM genre ORDER BY genre');
$result_format = $dbh->query('SELECT format, formatID FROM format ORDER BY format');
$result_status = $dbh->query('SELECT status, statusID FROM status ORDER BY status');

if (isset($_GET["id"])) {
  $id = $_GET["id"];
  
  $edit = true;
  $sql = "SELECT * FROM books WHERE ID=" . $id;
  
  $result = $dbh->query($sql);
  $row = $result->fetch();
  $author = $row["author"];
  $title = $row["title"];
  $description = $row["description"];
  $statusid = $row["statusID"];
  $read = $row['read'];
  $genreid = $row['genreID'];
  $formatid = $row['formatID'];
  $isbn = $row['ISBN'];
  $series = $row['series'];
  $seriesno = $row['seriesNo'];
  $owned = $row['owned'];
  $rating = $row['rating'];
}
else {
  $id = 0;  
  $edit = false;
  $author = '';
  $title = '';
  $description = '';
  $statusid = '1';
  $read = date("Y");
  $genreid = 1;
  $formatid = 1;
  $isbn = '';
  $series = '';
  $seriesno = '';
  $owned = 0;
  $rating = 0;
}
?>

<!doctype html>
<html>
<head>
	<meta charset='utf-8'>
	<title>Book Details</title>
	<link rel="stylesheet" type="text/css" href="css/form.css" />
	<link rel="stylesheet" type="text/css" href="css/rateit.css" />
	<script type='text/javascript' src="jquery-1.10.2.min.js"></script>
	<script type='text/javascript' src='book.js'></script>
	<script type='text/javascript' src="jquery.rateit.min.js"></script>
	
	
</head>
<!-- see http://www.bruceontheloose.com/htmlcss/examples/chapter-16/form-email.html for example of the css -->
<body>
    

    
	
<?php
if ($edit) {
  echo "<h1>Edit Book</h1>";
}
else {
  echo "<h1>Add New Book</h1>";
}
?>

<div id="wrapper">
<form method='post' action='handler.php'>
	<fieldset>
		<h2 class='account'>Book Information</h2>
		<ul>
			<li>
<input type="hidden" id='id' name='id' value=' <?php echo $id; ?> ' />
<label>Author</label>
<input type="text" id='author' name='author' placeholder='Enter name of author' class='large' required='required' autofocus='autofocus' value="<?php echo stripslashes($author); ?>" />
</li>
<li>
<label>Title</label>
<input type="text" id='title' name='title' class='large' required='required' value="<?php echo stripslashes($title); ?>" />
</li>
<li>
<label>Description</label>
<textarea id='description' name='description' rows="5" cols="60" ><?php echo stripslashes($description); ?></textarea>
</li>
<li>
<label>Status</label>
<select id='statusid' name='statusid'>
	<?php
	//while ($row = $result_status->fetchArray()) {
	foreach($result_status as $row) {	
	  print ('<option value="' . $row['statusID'] . '"' );
	  if ($row['statusID'] == (string)$statusid) { print ' selected '; }
	  print ('>' . $row['status'] . '</option>');
}
?>
</select>
</li>
<li>
<label>Year Read</label>
<select id='read' name='read'>
<?php
print('<option value="0000">N/A</option>');
for ($beg = date("Y"); $beg >= 2000; $beg--) {
    
    print('<option value="' . $beg . '"');
    if ($beg == $read) {
        print(' selected ');
    }
    print('>' . $beg . '</option>');
}
?>
</select>
</li>
<li>
<label>Genre</label>
<select id='genreid' name='genreid'>
	<?php
	foreach ($result_genre as $row) {
	print ('<option value="' . $row['genreID'] . '"' );
	if ($row['genreID'] == (string)$genreid) { print ' selected '; }
	print ('>' . $row['genre'] . '</option>');
}
?>
</select>
</li>

<li>
<label>Format</label>
<select id='formatid' name='formatid'>
		<?php
	foreach ($result_format as $row) {
	print ('<option value="' . $row['formatID'] . '"' );
	if ($row['formatID'] == (string)$formatid) { print ' selected '; }
	print ('>' . $row['format'] . '</option>');
}
?>
</select>
</li>
<li>
<label>I own this book</label>
<fieldset class="radios">
	<ul>
	<li>Yes: <input type="radio" id="owned" name="owned" value="1" <?php ($owned==1 ? print ('checked') : '') ?> /></li>
	<li> No: <input type="radio" id="owned" name="owned" value="0" <?php ($owned==0 ? print ('checked') : '') ?> /></li>
	</ul>
</fieldset>
</li>
<li>
<label>ISBN</label>
<input type="text" id='isbn' name='isbn' value='<?php echo $isbn; ?>' />
</li>
<li>
<label>Rating</label>
<div class='rateit' id='rateitbook' data-rateit-step='1' data-rateit-value='<?php echo $rating; ?>' >
</div>
</li>
<li>
<label>Series</label>
<input type="text" id='series' name='series' value='<?php echo $series; ?>' />
<label>No.</label>
<input type="text" id='seriesno' name='seriesno' size="3" maxlength="3" value='<?php echo $seriesno; ?>' />
</li>
</ul>
<input type="hidden" id='id' name='id' value='<?php echo $id; ?>' />
<input type="hidden" id='pg' name='pg' value='<?php echo $pg; ?>' />
<input type="hidden" id='sort' name='sort' value='<?php echo $sortval; ?>' />
<input type="hidden" id='ed' name='ed' value='<?php echo $edit; ?>' />
<input type="hidden" id=rating name='rating' value='<?php echo $rating; ?>' />
<input type="submit" class="create_profile" value="Save Book">
<input type="button" class="create_profile" value="Back" onclick="history.go(-1);" />
</fieldset>
</form>
</div>

<script type="text/javascript">
  var tooltips = ['Did not like it', 'It was OK', 'I liked it', 'REALLY liked it', 'I loved it'];
  $("#rateitbook").bind('over', 
    function (event, value) { 
      $(this).attr('title', tooltips[value - 1]); 
    }); 
    $("#rateitbook").bind('rated', 
    function() { 
      $('#rating').val( $(this).rateit('value') );
    });
</script>

</body>
</html>


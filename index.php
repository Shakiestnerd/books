<?php
    // First we exec our common code to connection to the database and start the session 
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
?>
<!doctype html>
<html>
<head>
	
	<meta charset='utf-32'>
	<title>My Books</title>
	<link rel="stylesheet" href="css/form.css" />
	<link href='http://fonts.googleapis.com/css?family=Ubuntu|Roboto|Roboto+Slab' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" type="text/css" href="css/rateit.css" />
	<script type='text/javascript' src="jquery-1.10.2.min.js"></script>
	<script type='text/javascript' src='book.js'></script>
	<script type='text/javascript' src="jquery.rateit.min.js"></script>
</head>

<body>
    
<?php   
$up = ' ˄';
$dn = ' ˅';
$userid = $_SESSION['user']['id'];

if (isset($_GET['pg'])) {
    $_SESSION['pg'] = $_GET['pg'];
}
if (!isset($_SESSION['pg'])) {
    $_SESSION['pg'] = 'read';
}

echo '<header>';
echo '<div class="public-profile"> <a href="edit_account.php">' . $_SESSION['user']['username'] . '</a></div>';
if ($_SESSION['pg'] == 'read') {
	echo "<h1>My Books Read</h1>";
}
elseif ($_SESSION["pg"] == "lib") {
    echo "<h1>My Library</h1>";
}
else {
	echo "<h1>Wish List</h1>";
}	
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
echo '<p />';

?>

<! ###################################
##### The book info div is here. #####
###################################### -->

<div id='bookinfo'>
 <h2 class='account'></h2>
 <h3 class='jauthor'></h3>
 <p class='jdescription'></p>
 <div class='rateit' data-rateit-value="0" data-rateit-ispreset="true" data-rateit-readonly="true"></div>
 <p>ISBN: <span class='jisbn'></span></p>
 <p>Genre: <span class='jgenre'></span></p>
 <p>Format: <span class='jformat'></span></p>
 <p class='jseries'></p>
 <p class='jnote'></p>
 <hr />
 <nav><ul>
 	<li><a id='close'>Close</a></li>
 	<li><a id='edit' href="add.php?id=0">Edit</a></li>
 	<li><a id='delete' href="del.php?id=0">Delete</a></li>
 	</ul></nav>
</div>

<?php
if (isset($_GET["sort"])) {
    // manual sortvals take the highest precedence
    $sortval = $_GET["sort"]; 
}
elseif (isset($_SESSION["sort"]) ) {
    // session level takes the next highest precence
    $sortval = $_SESSION["sort"];
}
else {
    // otherwise, set the sort default based on the page being loaded.
    if ($_SESSION['pg'] == "wish") {
        $sortval = "author";
    }
    elseif ($_SESSION["pg"] == "lib") {
        $sortval = "author";
    }
    else {
        $sortval = "read";
    }
}


$showyear = '';
if (isset($_GET["yr"])) {
    $showyear = $_GET["yr"]; 
}

// open the database
try {
  $dbh = new PDO("sqlite:data/datastore.db");
} catch (PDOException $e) {
  echo 'Connection failed: ' . $e->getMessage();
}

$sortarray = array("author" => "author, title", "title" => "title", "genre" => "genre, read DESC, title", "read" => "read DESC, title", "authordesc" => "author DESC, title", "titledesc" => "title DESC", "genredesc" => "genre DESC, read DESC, title", "readdesc" => "read, title", "series" => "series, seriesno, title", "seriesdesc" => "series DESC, seriesno, title");
switch ( $sortval ) {
    case "author":
    case "title":
    case "genre":
    case "read":
    case "series":
        $sortnext = $sortval . " DESC";
        $fetchval = $sortval;
        break;
    case "authordesc":
    case "titledesc":
    case "genredesc":
    case "readdesc":
    case "seriesdesc":
        $sortnext = substr($sortval, 0, strlen($sortval)-4);
        $fetchval = $sortnext;
        break;
}

//echo $sortval . ": " . $_SESSION["sort"] . ": " . $sortnext;
// echo $sortval . " -- " . $fetchval;
$curYear = date('Y'); 
$foo=0;
$cnt=0;
$sread = '';
$sql = '
SELECT ID, author, title, genre, read, stamp, statusid, owned, series, seriesNo, upper(substr(format, 1, 1)) as fmt 
FROM books 
LEFT JOIN genre ON books.genreid = genre.genreid 
LEFT JOIN format ON books.formatid = format.formatid
WHERE '
. ($_SESSION['pg'] == 'read' ? '(StatusID = 1 OR StatusID = 3) ' : ($_SESSION["pg"] == 'wish' ? '(StatusID = 2) ' : '(owned = 1) ')) 
. ($showyear =='' ? '' : 'and read = "' . $showyear . '"') . ' and userid = ' . $userid .
' ORDER BY ' . $sortarray[$sortval];

// $dbh->beginTransaction();

// echo $sql;

echo '<table><thead><tr><th></th><th scope="col">';
echo '<a href="index.php?sort=';
echo ($sortval=="author" ? 'authordesc' : 'author');
echo '&yr=' . $showyear . '">Author</a>' . ($sortval=="author" ? $up : ($sortval=="authordesc" ? $dn : '')) . '</th>';

echo '<th scope="col">';
echo '<a href="index.php?sort=';
echo ($sortval=="title" ? 'titledesc' : 'title');
echo '&yr=' . $showyear . '">Title</a>' . ($sortval=="title" ? $up : ($sortval=="titledesc" ? $dn : '')) . '</th>';

echo '<th scope="col">';
echo '<a href="index.php?sort=';
echo ($sortval=="genre" ? 'genredesc' : 'genre');
echo '&yr=' . $showyear . '">Genre</a>' . ($sortval=="genre" ? $up : ($sortval=="genredesc" ? $dn : '')) . '</th>';

echo '<th scope="col">';
echo '<a href="index.php?sort=';
echo ($sortval=="series" ? 'seriesdesc' : 'series');
echo '&yr=' . $showyear . '">Series</a>' . ($sortval=="series" ? $up : ($sortval=="seriesdesc" ? $dn : '')) . '</th>';

// Show the year column if is the books read page.
if ($_SESSION['pg'] == 'read') {
    echo '<th scope="col">';
    echo '<a href="index.php?sort=';
    echo ($sortval=="read" ? 'readdesc' : 'read');
    echo '&yr=' . $showyear . '">Year</a>' . ($sortval=="read" ? $up : ($sortval=="readdesc" ? $dn : '')) . '</th>';
}

echo '<th></th></tr></thead><tbody>';


	foreach ($dbh->query($sql) as $row) {

    if ($sread == '') { 
      $sread = $row[$fetchval]; }
    $diff = time() - strtotime($row['stamp']);
    if ($row['statusID']=='3') { $inprog = true; } else { $inprog = false; }
    if ($diff < 1000000 and $inprog == false) { $new = ' new'; } else { $new = ''; }
		if ($row['owned'] == True) { $owned = $row['fmt']; } else { $owned = ''; }   
    if ($sread == $row[$fetchval] ) {
    	// only count the row if it is not 'in progress'
    	if ($inprog == false) { $cnt++; }
		}
	  else {
	    // add the summary row
	    if (($sortval == 'read' or $sortval == 'genre' or $sortval == 'readdesc' or $sortval == 'genredesc') and ($_SESSION['pg'] == 'read')) {
	    echo '<tr><th></th><th></th><th>';
	    // add calculation for current year projection
	    if ($curYear == $sread) {
	    	$doy = date('z') + 1;
	    	$trend = intval($cnt * 365.0 / $doy);
	    	echo '<i>Current Year Projection:</i> <strong>' . $trend . '</strong>';
	    }
	    echo '</th><th></th>';
	    if ($_SESSION['pg'] == 'read') {
	        echo '<th></th>';
	        }
	    echo '<th>' . $sread . '</th><th>' . $cnt . '</th></tr>';
	    }
	    $sread = $row[$fetchval];
	    $cnt = 1;
	  }
	  
	  // adding the real row of data
	
    if ($inprog == false) {
  	      echo '<tr id=' . $row['ID'] . '>
  	      <td>
  	      <small><a href="add.php?id=' . $row['ID'] . '&pg=' . $_SESSION['pg'] . '">edit</a> 
  	      <a href="del.php?id=' . $row['ID'] . '">del</a></small>
  	      </td>';
  	      echo '<td class="info" id=' . $row['ID'] . '>' . stripslashes($row['author']) . '</td>';
  	      echo '<td class="info" id=' . $row['ID'] . '>' . stripslashes($row['title']) .'</td>';
  	      echo '<td>' . $row['genre'] . '</td><td>'. $row['series'] . ($row['seriesNo']>0 ? ' #' : '') . $row['seriesNo'] . '</td>';
  	      if ($_SESSION['pg'] == 'read') {
  	        echo '<td>' . $row['read'] . '</td>';
  	      }
  	      echo '<td><small><i>' . $owned . $new . '</i></small> </td></tr>';
		  $foo++;		// adding up for grand total
	  }
	  else {
		  echo '<tr><td><small><a href="add.php?id=' . $row['ID'] . '&pg=' . $_SESSION['pg'] . '">edit</a> <a href="del.php?id=' . $row['ID'] . '">del</a></small></td>';
		  echo '<td class="info" id=' . $row['ID'] . '><i>' . stripslashes($row['author']) . '</i></td>';
		  echo '<td class="info" id=' . $row['ID'] . '><i>' . stripslashes($row['title']) . '</i></td>';
		  echo '<td><i>' . $row['genre'] . '</i></td>';
		  echo '<td>' . $row['series'] . ($row['seriesNo']>0 ? ' #' : '') . $row['seriesNo'] . '</td>';
		  if ($_SESSION['pg'] == 'read') {
		    echo '<td><i>' . $row['read'] . '</i></td>';
		  }
		  echo '<td><small><i>' . $owned . $new . ' reading</i></small></td></tr>';
	  }
	
  }
// add last summary row at the bottom of the screen.  
if (($sortval == 'read' or $sortval == 'readdesc' or $sortval == 'genre' or $sortval == 'genredesc') and ($_SESSION['pg'] == 'read')) {
  echo '<tr><th></th><th></th><th></th><th></th><th></th>';
  echo '<th>' . $sread . '</th><th>' . $cnt . '</th></tr>';
}

// Grand summary row
echo '</tbody><tfoot><tr><th scope="row">.</th><th></th><th></th><th> </th>';
    if ($_SESSION['pg'] == 'read') {
        echo '<th></th>';
    }
echo '<th>Total</th><th>' . $foo . '</th></tr></tfoot></table>';

// Save the state variables to session variables.
$_SESSION["sort"] = $sortval;
?>
<footer>&copy; 2013 Canofworms.com</footer>
</body>
</html>

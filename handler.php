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
try {
  $dbh = new PDO("sqlite:data/datastore.db");
} catch (PDOException $e) {
  echo 'Connection failed: ' . $e->getMessage();
}
$userid = $_SESSION['user']['id'];
$id = $_POST['id'];
if (isset($_POST["pg"])) {
	$pg = $_POST["pg"];
}
else {
	$pg = "read";
}
// $ed = $_POST["ed"];
$sortval = $_POST["sort"];
$author = $_POST['author'];
$title = $_POST['title'];
$description = $_POST['description'];
$statusid = $_POST["statusid"];
$read = $_POST['read'];
$genreid = $_POST['genreid'];
$formatid = $_POST['formatid'];
$isbn = $_POST['isbn'];
$series = $_POST['series'];
$seriesno=$_POST["seriesno"];
$owned = $_POST['owned'];
$rating = $_POST['rating'];
$stamp = date("c");

if ($id > 0) {
  $sql = "UPDATE books SET author = :author, title = :title, description = :description, 
	  statusid = :statusid, read = :read, genreid = :genreid, formatid = :formatid, isbn = :isbn, 
	  series = :series, seriesno= :seriesno, lastedit = :stamp, userid = :userid, owned = :owned, rating = :rating 
	  WHERE ID = $id";
}
else {
	$id = 0;
    $sql = "INSERT INTO books (author, title, description, statusid, read, genreid, formatid, isbn, series, seriesno, stamp, userid, owned, rating) VALUES 
	  (:author, :title, :description, :statusid, :read, :genreid, :formatid, :isbn, :series, :seriesno, :stamp, :userid, :owned, :rating)";

}

//$dbh->beginTransaction();

$stmt = $dbh->prepare($sql);
$stmt->bindParam('author', $author);
$stmt->bindParam('title', $title);
$stmt->bindParam('description', $description);
$stmt->bindParam('statusid', $statusid);
$stmt->bindParam('read', $read);
$stmt->bindParam('genreid', $genreid);
$stmt->bindParam('formatid', $formatid);
$stmt->bindParam('isbn', $isbn);
$stmt->bindParam('series', $series);
$stmt->bindParam('seriesno', $seriesno);
$stmt->bindParam('stamp', $stamp);
$stmt->bindParam('userid', $userid);
$stmt->bindParam('owned', $owned);
$stmt->bindParam('rating', $rating);

// echo $sql;
$stmt->execute();

$result = $dbh->lastInsertId();

// $dbh->commit();

if (!$result  && $id == 0) {
	echo "Query:";
	echo "<PRE>$sql</PRE>";
	echo $dbh->errorCode();
	echo "Error Info:\n";
	print_r($dbh->errorInfo());
	}
//	else {
//		echo $result;
//	}
	
	
	
$home_url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/index.php';
if ($pg <> '') {
	  $home_url = $home_url . '?pg=' . $pg;
}
// echo $home_url; 
header('Location: ' . $home_url);

?>
<?php
$q = intval($_GET['q']);

try {
  $dbh = new PDO("sqlite:data/datastore.db");
} catch (PDOException $e) {
  echo 'Connection failed: ' . $e->getMessage();
}

$sql = "SELECT ID
    ,author
    ,title
    ,genre
    ,description
    ,ISBN
    ,read
    ,rating
    ,status
    ,owned
    ,series
    ,seriesNo
    ,format.format as readformat
    ,upper(substr(format.format, 1, 1)) as fmt
FROM books 
LEFT JOIN genre ON books.genreid = genre.genreid 
LEFT JOIN format ON books.formatid = format.formatid
LEFT JOIN status ON books.statusID = status.statusID
WHERE ID = " . $q . ";";

$result = $dbh->query($sql);

$row = $result->fetch();

echo json_encode($row);

$result->closeCursor();

?>


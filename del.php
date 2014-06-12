<?php

try {
  $dbh = new PDO("sqlite:data/datastore.db");
} catch (PDOException $e) {
  echo 'Connection failed: ' . $e->getMessage();
}

if ($_GET["id"]) {
  $id = $_GET["id"];
  $sql = 'DELETE FROM books WHERE ID=' . $id;
  $result = $dbh->prepare($sql);
  $result->execute();
}

$home_url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/index.php';

//echo $home_url; 
header('Location: ' . $home_url);

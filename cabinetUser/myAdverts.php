<?php

include "dbConfig.php";

$stmt = $pdo->prepare("SELECT * FROM adverts WHERE userId = :id");
$stmt->bindParam(':id', $_COOKIE["userId"]);
$stmt->execute();
$result = $stmt->fetchAll((PDO::FETCH_ASSOC)); 

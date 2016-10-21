<?php

include "dbConfig.php";

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
$stmt->bindParam(':id', $_COOKIE["userId"]);
$stmt->execute();
$result = $stmt->fetch((PDO::FETCH_ASSOC)); 

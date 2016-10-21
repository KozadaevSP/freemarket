<?php

include 'dbConfig.php';

$per_page = 10;

//получаем номер страницы и значение для лимита 
$cur_page = 1;
if (isset($_GET['page']) && $_GET['page'] > 0) 
{
    $cur_page = $_GET['page'];
}
$start = ($cur_page - 1) * $per_page;

$stmt = $pdo->prepare("SELECT adverts.id, users.name, adverts.photo, adverts.description, adverts.changeFor, adverts.dateAndTime 
FROM adverts 
INNER JOIN users
ON adverts.userId = users.id
ORDER BY dateAndTime DESC
LIMIT $per_page
OFFSET $start");
$stmt->execute();
$result = $stmt->fetchAll((PDO::FETCH_ASSOC)); 

$rows = $pdo->exec("SELECT * FROM adverts");

$num_pages = ceil($rows / $per_page);

$page = 0;

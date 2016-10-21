<?php

  $dbHost = 'localhost';
  $dbName = 'freemarket';
  $dbUser = 'root';
  $dbPassword = 'root';
  
  $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPassword);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $pdo->exec("set names utf8");
  
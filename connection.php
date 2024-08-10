<?php 
session_start();
$dsn = 'mysql:host=localhost;dbname=fos_db';
$username = 'root';
$password = '';
$pdo = new PDO($dsn, $username, $password);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
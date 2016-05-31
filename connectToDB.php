<?php
global $db;
$db = new PDO('mysql:host=localhost;dbname=mldb;charset=utf8mb4', 'mlmod', 'deeznuts');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>
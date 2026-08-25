<?php
$pdo = new PDO('mysql:host=127.0.0.1', 'root', '');
$pdo->exec('DROP DATABASE IF EXISTS `spp-sikas`');
$pdo->exec('CREATE DATABASE `spp-sikas`');
echo "Database dropped and recreated successfully.\n";

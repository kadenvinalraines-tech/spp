<?php
$output = [];
$returnVar = -1;
exec('cmd.exe /c ""C:\xampp\mysql\bin\mysql.exe" --version"', $output, $returnVar);
var_dump($output, $returnVar);

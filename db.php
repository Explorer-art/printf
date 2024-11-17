<?php
define("HOST", "localhost");
define("USER", "root");
define("PASSWORD", "hDRDshWbH5.!dN");
define("DATABASE", "registerUsers");

try {
    $connection = new PDO("mysql:host=" . HOST . ";dbname=" . DATABASE, USER, PASSWORD);
} catch (PDOException $e) {
    exit("Error: " . $e->getMessage());
}

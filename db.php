<?php
define("HOST", "localhost");
define("USER", "root");
define("PASSWORD", "pass");
define("DATABASE", "db");
try {
    $connection = new PDO("mysql:host=" . HOST . ";dbname=" . DATABASE, USER, PASSWORD);
} catch (PDOException $e) {
    exit("Error: " . $e->getMessage());
}

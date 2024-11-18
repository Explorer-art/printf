<?php
define("HOST", "localhost");
define("USER", "root");
<<<<<<< Updated upstream
define("PASSWORD", "hDRDshWbH5.!dN");
define("DATABASE", "registerUsers");
=======
define("PASSWORD", "password");
define("DATABASE", "printf");
>>>>>>> Stashed changes

try {
    $connection = new PDO("mysql:host=" . HOST . ";dbname=" . DATABASE, USER, PASSWORD);
} catch (PDOException $e) {
    exit("Error: " . $e->getMessage());
}

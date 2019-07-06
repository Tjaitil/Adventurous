<?php
    define('DB_server', 'localhost');
    define('DB_username', 'root');
    define('DB_pass', '');
    define('DB_name', 'Adventurous');
    
    try {
        $conn = new PDO ("mysql:host=" . DB_server . ";dbname=" . DB_name, DB_username, DB_pass);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    catch (PDOexception $e) {
        die ("ERROR: Could not connect." . $e->getMessage());
    }
?>
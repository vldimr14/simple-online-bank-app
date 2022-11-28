<?php
    define("DB_SERVER", "localhost");
    define("DB_USERNAME", "root");
    define("DB_PASSWORD", "");
    define("DB_NAME", "online-bank-db");

    class DbHandler {
        // connect to database
        public function connect() {
            try {
                $pdo = new PDO("mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME, DB_USERNAME, DB_PASSWORD);
                return $pdo;
            } catch (PDOException $e) {
                die("Error: " . $e->getMessage());
            }
        }

    }
    
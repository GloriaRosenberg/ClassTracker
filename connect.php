<?php
function connect($server, $username, $password, $database, $port = 3306) {
    try {
        $connection = mysqli_init();

        // Path to your Aiven SSL CA certificate
        $ca_path = __DIR__ . '/ca.pem';
        if (file_exists($ca_path)) {
            $connection->ssl_set(NULL, NULL, $ca_path, NULL, NULL);
        }

        // Establish connection with Aiven's custom port and SSL
        $connection->real_connect(
            $server, 
            $username, 
            $password, 
            $database, 
            (int)$port, 
            NULL, 
            MYSQLI_CLIENT_SSL
        );

        return $connection;
    } catch (mysqli_sql_exception $e) {
        die("Error connecting to database: " . $e->getMessage());
    }
}

// Automatically pulls Aiven credentials on Render, or defaults to XAMPP on localhost
$server   = getenv('DB_HOST') ?: "localhost";
$username = getenv('DB_USER') ?: "root";
$password = getenv('DB_PASS') ?: "";
$database = getenv('DB_NAME') ?: "class_logs";
$port     = getenv('DB_PORT') ?: 3306;

$connection = connect($server, $username, $password, $database, $port);
?>
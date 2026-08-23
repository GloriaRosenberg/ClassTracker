<?php
    function connect ($server, $username, $password, $database){
        try {
            $connection=mysqli_connect("$server", "$username", "$password", "$database");
            return $connection;
        } catch (mysqli_sql_exception $e) {
            die ("Error conectando a la base de datos: ".$e->getMessage());
        }
    }
?>
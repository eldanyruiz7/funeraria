<?php
date_default_timezone_set('America/Mexico_City');
    $servidor       = 'localhost';
    $usr            = 'root';
    $contrasena     = '12345';
    $bd             = 'funerariadb_prod';

    $mysqli         = new mysqli($servidor , $usr , $contrasena , $bd);
    $mysqli->set_charset("utf8");

    if($mysqli->connect_errno)
    {
        echo "Error de Base de datos\n";
        echo "Errno: ". $mysqli->connect_errno . "\n";
        echo "Error: ". $mysqli->connect_error . "\n";
        exit;
    }
 ?>

<?php
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
    date_default_timezone_set('America/Mexico_City');
    require("assets/connect/bd.php");
    require("assets/connect/sesion.class.php");
    $sesion = new sesion();
    $usuario = $sesion->get("id");
    $idSesion = $sesion->get("idsesion");
    if( $usuario == false )
    {
       header("Location: login.php");
    }
    else
    {
        $fechaLogout    = date("Y-m-d H:i:s");
        //$idSesion       = $sesion->get("idsesion");
        $sql            = "UPDATE sesionescontrol
                            SET timestampsalida = '$fechaLogout',
                                activo = 0
                            WHERE id = $idSesion
                            AND activo = 1
                            LIMIT 1";
        if ($mysqli->query($sql)==true)
        {
            $usuario = $sesion->get("usuario");
            $sesion->termina_sesion();
        }
        header("location: login.php");
    }
?>

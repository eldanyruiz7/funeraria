<?php
$fecha          = date("Y-m-d H:i:s");
$idSesion       = $sesion->get("idsesion");
$idUsuario      = $sesion->get("id");
if($idSesion != false)
{
    $sql            = "SELECT * FROM sesionescontrol WHERE usuario = $idUsuario AND id = $idSesion AND activo = 1";
    $resultSesion   = $mysqli->query($sql);
    $c_r = $resultSesion->num_rows;
    //echo $resultSesion->error;
    if($c_r != 0)
    {
        $sql        = "UPDATE sesionescontrol SET timestampsalida = '$fecha', activo = 0 WHERE usuario = $idUsuario AND activo = 1 AND id != $idSesion";
        $result = $mysqli->query($sql);
        if(is_object($result))
            $result->close();

        /*$sql        = "UPDATE ventas SET sesion = $idSesion WHERE corte = 0 AND usuario = $idUsuario";
        $resultVta = $mysqli->query($sql);
        if(is_object($resultVta))
            $resultVta->close();*/
    }
    if(is_object($resultSesion))
        $resultSesion->close();
}
 ?>

<?php
function logueado($idSesion,$usuario,$mysqli)
{

    $sql = "SELECT * FROM sesionescontrol WHERE usuario = $usuario AND id = $idSesion AND activo = 1";
    $r = $mysqli->query($sql);
    $c_r = $r->num_rows;
    if(is_object($r))
        $r->close();
    if($c_r > 0)
        return true;
    else
        return false;
}

 ?>

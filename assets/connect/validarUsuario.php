<?php
function validarUsuario($usuario, $password, $mysqli)
{
    if (strlen($usuario) == 0 || strlen($password) == 0)
    {
        usleep(2600000);
        return false;
    }
    require "assets/php/hash.php";
    $sql = "SELECT * FROM cat_usuarios WHERE nickName = ? OR email = ? AND activo = 1 LIMIT 1";
    $prepare            = $mysqli->prepare($sql);
    if( $prepare    ->bind_param('ss',$usuario,$usuario) && $prepare     ->execute())
    {
        $res = $prepare->get_result();
        if($res->num_rows == 1)
        {
            $fila       = $res->fetch_assoc();
            $pass = new password;
            if( $pass->verify($password, $fila["cntrsn"]) )
            {
                $prepare->close();
                return $fila;
            }
            else
            {
                $prepare->close();
                return false;
            }
        }
        else
        {
            usleep(3000000);
            $prepare->close();
            return false;
        }
    }
    else
    {
        usleep(3000000);
        $prepare->close();
        return false;
    }
}
?>

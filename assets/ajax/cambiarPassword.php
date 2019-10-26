<?php
    require_once ('../connect/bd.php');
    require_once ("../connect/sesion.class.php");
    $sesion = new sesion();
    require_once ("../connect/cerrarOtrasSesiones.php");
    require_once ("../connect/usuarioLogeado.php");
    require_once ("../php/funcionesVarias.php");
    require_once ("../php/hash.php");
    if( logueado($idSesion,$idUsuario,$mysqli) == false || $idSesion == false)
    {
        header("Location: salir.php");
    }
    else
    {
        require "../php/responderJSON.php";
        require ("../php/usuario.class.php");
        $usuario = new usuario($idUsuario,$mysqli);

        $password1                      = $_POST['contrasena-actual'];
        $password2                      = $_POST['contrasena-nueva1'];
        $password3                      = $_POST['contrasena-nueva2'];

        $response = array(
            "status"                    => 1
        );

        if (!$password1 = validarFormulario('s',$password1, 5))
        {
            $response['mensaje'] = "El campo Contraseña actual no puede estar en blanco y debe contener al menos 6 caracteres";
            $response['status'] = 0;
            $response['focus'] = 'contrasena-actual';
            responder($response, $mysqli);
        }
        if (!$password2 = validarFormulario('s',$password2, 5))
        {
            $response['mensaje'] = "El campo Contraseña nueva no puede estar en blanco y debe contener al menos 6 caracteres";
            $response['status'] = 0;
            $response['focus'] = 'contrasena-nueva1';
            responder($response, $mysqli);
        }
        if (!$password3 = validarFormulario('s',$password3, 5))
        {
            $response['mensaje'] = "El campo Contraseña nueva no puede estar en blanco y debe contener al menos 6 caracteres";
            $response['status'] = 0;
            $response['focus'] = 'contrasena-nueva2';
            responder($response, $mysqli);
        }
        $mysqli->autocommit(FALSE);
        $password = new password;
        $sql = "SELECT cntrsn FROM cat_usuarios WHERE id = ? LIMIT 1";
        $prepare_p = $mysqli->prepare($sql);
        if ($prepare_p &&
            $prepare_p->bind_param("i",$idUsuario) &&
            $prepare_p->execute() &&
            $prepare_p->store_result() &&
            $prepare_p->bind_result($hash) &&
            $prepare_p->fetch() &&
            $prepare_p->num_rows > 0)
                $comprobar = $password->verify($password1, $hash);
        else
        {
            $response['mensaje'] = "Error al preparar los parámetros. Vuelve a intentarlo.";
            $response['status'] = 0;
            $response['focus'] = '';
            responder($response, $mysqli);
        }
        if (!$comprobar)
        {
            $response['mensaje'] = "Error cambiar la contraseña <br> La contraseña actual que capturaste no coincide con la registrada. Vuelve a intentarlo.";
            $response['status'] = 0;
            $response['focus'] = '';
            responder($response, $mysqli);
        }
        if ($password2 != $password3)
        {
            $response['mensaje'] = "Las contraseñas deben coincidir. Escribe la misma contraseña en ambos campos.";
            $response['status'] = 0;
            $response['focus'] = 'contrasena-nueva1';
            responder($response, $mysqli);
        }
        $hash = $password->hash($password2);
        $sql            = "UPDATE cat_usuarios
                            SET cntrsn = ?
                            WHERE id = ? LIMIT 1";
        if($prepare     = $mysqli->prepare($sql))
        {
            if(!$prepare->bind_param('si', $hash, $idUsuario))
            {
                $mysqli->rollback();
                $response['mensaje'] = "Error. No se pudo guardar la información. Falló el la vinculación de parámetros. Inténtalo nuevamente";
                $response['status'] = 0;
                responder($response, $mysqli);
            }
            if(!$prepare->execute())
            {
                $mysqli->rollback();
                $response['mensaje'] = "Error. No se pudo guardar la información. Falló el enlace a la base de datos. Inténtalo nuevamente";
                $response['status'] = 0;
                responder($response, $mysqli);
            }
            if($prepare->affected_rows == 0)
            {
                $mysqli->rollback();
                $response['mensaje']        = "No se modificó nada, Las contraseñas actual y nueva son iguales";
                $response['status']         = 0;
                responder($response, $mysqli);
            }

            if ($mysqli->commit())
            {
                $response['mensaje']        = "";
                $response['status']         = 1;
                responder($response, $mysqli);
            }
            else
            {
                $mysqli->rollback();
                $response['mensaje']        = "Error en commit, no se guardó nada, inténtalo nuevamente";
                $response['status']         = 0;
                responder($response, $mysqli);
            }
        }
        else
        {
            $mysqli->rollback();
            $response['mensaje']        = "Error. No se pudo guardar la información. Falló la preparación de los datos. Vuelve a intentarlo nuevamente";
            $response['status']         = 0;
            responder($response, $mysqli);
        }
    }
?>

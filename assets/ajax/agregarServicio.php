<?php
    require_once ('../connect/bd.php');
    require_once ("../connect/sesion.class.php");
    $sesion = new sesion();
    require_once ("../connect/cerrarOtrasSesiones.php");
    require_once ("../connect/usuarioLogeado.php");
    require_once ("../php/funcionesVarias.php");
    if( logueado($idSesion,$idUsuario,$mysqli) == false || $idSesion == false)
    {
        header("Location: salir.php");
    }
    else
    {
        require "../php/responderJSON.php";
        require ("../php/usuario.class.php");
        $usuario = new usuario($idUsuario,$mysqli);
        $permiso = $usuario->permiso("agregarServicio",$mysqli);
        if (!$permiso)
        {
            $response['mensaje'] = "No se pudo guardar este registro. Usuario con permisos insuficientes para realizar esta acción";
            $response['status'] = 0;
            responder($response, $mysqli);
        }

        $nombre                         = $_POST['nombre'];
        $descripcion                    = $_POST['descripcion'];
        $precio                         = $_POST['precio'];
        $response = array(
            "status"                    => 1
        );

        if (!$nombre = validarFormulario('s',$nombre, FALSE))
        {
            $response['mensaje'] = "El campo Nombre no puede estar en blanco";
            $response['status'] = 0;
            $response['focus'] = 'nombre';
            responder($response, $mysqli);
        }
        $descripcion = validarFormulario('s', $descripcion, FALSE);
        if (!$precio = validarFormulario('i',$precio,0))
        {
            $response['mensaje'] = "El Precio debe ser mayor que cero (0)";
            $response['status'] = 0;
            $response['focus'] = 'precio';
            responder($response, $mysqli);
        }
        $sql            = "INSERT INTO cat_servicios
                                (nombre, descripcion, precio, usuario)
                            VALUES
                                (?,?,?,?)";
        if($prepare     = $mysqli->prepare($sql))
        {
            if(!$prepare->bind_param('ssii', $nombre, $descripcion, $precio, $idUsuario))
            {
                $response['mensaje'] = "Error. No se pudo guardar la información. Falló el la vinculación de parámetros. Inténtalo nuevamente";
                $response['status'] = 0;
                responder($response, $mysqli);
            }
            if(!$prepare->execute())
            {
                $response['mensaje'] = "Error. No se pudo guardar la información. Falló el enlace a la base de datos. Inténtalo nuevamente";
                $response['status'] = 0;
                responder($response, $mysqli);
            }
            if($prepare->affected_rows == 0)
            {
                $response['mensaje']        = "No se modificó nada, no se pudo guardar el registro, inténtalo nuevamente";
                $response['status']         = 0;
                responder($response, $mysqli);
            }
            $insert_id                  = $mysqli->insert_id;
            $response['mensaje']        = "$nombre";
            $response['status']         = 1;
            responder($response, $mysqli);
        }
        else
        {
            $response['mensaje']        = "Error. No se pudo modificar. Falló la preparación de los datos. Vuelve a intentarlo nuevamente";
            $response['status']         = 0;
            responder($response, $mysqli);
        }
    }
?>

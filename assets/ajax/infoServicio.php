<?php
    require_once ('../connect/bd.php');
    require_once ("../connect/sesion.class.php");
    $sesion = new sesion();
    require_once ("../connect/cerrarOtrasSesiones.php");
    require_once ("../connect/usuarioLogeado.php");
    if( logueado($idSesion,$idUsuario,$mysqli) == false || $idSesion == false)
    {
        header("Location: salir.php");
    }
    else
    {
        require "../php/responderJSON.php";
        require ("../php/usuario.class.php");
        $usuario = new usuario($idUsuario,$mysqli);
        $permiso = $usuario->permiso("listarServicios",$mysqli);
        if (!$permiso)
        {
            $response['mensaje'] = "No se pudo mostrar este registro. Usuario con permisos insuficientes para realizar esta acción";
            $response['status'] = 0;
            responder($response, $mysqli);
        }

        $idCliente = $_POST['idCliente'];

        $response = array(
            "status"        => 1
        );
        if(is_numeric($idCliente) == FALSE)
        {
            $response['mensaje'] = "El formato del id del servicio no es el correcto";
            $response['status'] = 0;
            responder($response, $mysqli);
        }
        $sql = "SELECT cat_servicios.*
                FROM cat_servicios
                WHERE cat_servicios.id = ? AND cat_servicios.activo = 1 LIMIT 1";
        if($prepare         = $mysqli->prepare($sql))
        {
            $prepare        ->bind_param('i',$idCliente);
            $prepare        ->execute();
            $res            = $prepare->get_result();
        }
        else
        {
            $response['mensaje'] = "Ocurrió un error en la consulta a la Base de datos.".$mysqli->errno . " " . $mysqli->error;
            $response['status'] = 0;
            responder($response, $mysqli);
        }
        if($res->num_rows == 0)
        {
            $response['mensaje'] = "No existe informaci&oacute;n para este servicio. Posiblemente ya ha sido eliminado.";
            $response['status'] = 0;
            responder($response, $mysqli);
        }
        $row                            = $res->fetch_assoc();
        $id                             = $row['id'];
        $nombre                         = $row['nombre'];
        $descripcion                    = $row['descripcion'];
        $precio                         = $row['precio'];
        $fechaCreacion                  = $row['fechaCreacion'];
        $response['id']                 = $id;
        $response['nombre']             = $nombre;
        $response['descripcion']        = $descripcion;
        $response['precio']             = $precio;
        $response['fechaCreacion']      = $fechaCreacion;
        $response['status']             = 1;
        $prepare                        ->close();
        responder($response, $mysqli);
    }
?>

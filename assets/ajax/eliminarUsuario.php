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
        $permiso = $usuario->permiso("eliminarUsuario",$mysqli);
        if (!$permiso)
        {
            $response['mensaje'] = "No se pudo eliminar este registro. Usuario con permisos insuficientes para realizar esta acción";
            $response['status'] = 0;
            responder($response, $mysqli);
        }
        $id                             = $_POST['idCliente'];
		$idUsuario      				= $sesion->get('id');
        $sql            				= "SELECT idSucursal FROM cat_usuarios WHERE id = $idUsuario LIMIT 1";
        $res_noSucursal 				= $mysqli->query($sql);
        $row_noSucursal 				= $res_noSucursal->fetch_assoc();
        $idSucursal     				= $row_noSucursal['idSucursal'];
        $response = array(
            "status"                    => 1
        );
        if(!$id = validarFormulario('i', $id))
        {
            $response['mensaje']        = "El ID del usuario no cumple con el formato establecido";
            $response['status']         = 0;
            $response['focus']          = '';
            responder($response, $mysqli);
        }
        $sql = "SELECT id, CONCAT(nombres,' ',apellidop,' ',apellidom) AS nombresUsuario FROM cat_usuarios WHERE id = $id AND activo = 1";
        $res_venta = $mysqli->query($sql);
        if ($res_venta->num_rows == 0)
        {
            $response['mensaje'] = "No se puede cancelar este usuario porque no existe o ya ha sido cancelado con anterioridad";
            $response['status'] = 0;
            $response['focus'] = '';
            responder($response, $mysqli);
        }
        $row_venta = $res_venta->fetch_assoc();
        // $idSucursal = $row_venta['idSucursal'];
        $mysqli->autocommit(FALSE);
        $sql = "UPDATE cat_usuarios
                SET activo              = 0
                WHERE id                = ?
                LIMIT 1";
        if($prepare                     = $mysqli->prepare($sql))
        {
            if(!$prepare->bind_param('i', $id))
            {
                $response['mensaje']    = "Error. No se pudo modificar la información. Falló el la vinculación de parámetros. Inténtalo nuevamente";
                $response['status']     = 0;
                $mysqli->rollback();
                responder($response, $mysqli);
            }
            if(!$prepare->execute())
            {
                $response['mensaje']    = "Error. No se pudo modificar la información. Falló el enlace a la base de datos. Inténtalo nuevamente";
                $response['status']     = 0;
                $mysqli->rollback();
                responder($response, $mysqli);
            }
            if($prepare->affected_rows  == 0)
            {
                $response['mensaje']    = "No se modificó nada. Vuelve a intentarlo";
                $response['status']     = 2;
                $mysqli->rollback();
                responder($response, $mysqli);
            }
			// Agregar evento en la bitácora de eventos ///////
			$nombresUsuario				= $row_venta['nombresUsuario'];
			$ipUsuario 					= $sesion->get("ip");
			$pantalla					= "Listar usuarios";
			$descripcion				= "Se ha eliminado un usuario del sistema, id=$id, nombre=$nombresUsuario";
			$sql						= "CALL agregarEvento($idUsuario, '$ipUsuario', '$pantalla', '$descripcion', $idSucursal);";
			$mysqli						->query($sql);
            if($mysqli->commit())
            {
                $response['mensaje']        = "Usuario eliminado exitosamente";
                $response['status']         = 1;
                responder($response, $mysqli);
            }
            else
            {
                $response['mensaje']        = "Error en 'commit', No se pudo eliminar. Vuelve a intentarlo";
                $response['status']         = 0;
                responder($response, $mysqli);
            }
        }
        else
        {
            $response['mensaje']        = "Error. No se pudo modificar. Falló la preparación de los datos. Vuelve a intentarlo nuevamente";
            $response['status']         = 0;
            responder($response, $mysqli);
        }
    }
?>

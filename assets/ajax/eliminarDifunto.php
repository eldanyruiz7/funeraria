<?php
    require_once ('../connect/bd.php');
    require_once ("../connect/sesion.class.php");
    $sesion = new sesion();
    require_once ("../connect/cerrarOtrasSesiones.php");
    require_once ("../connect/usuarioLogeado.php");
    require_once ("../php/funcionesVarias.php");
    if( logueado($idSesion,$idUsuario,$mysqli) == false || $idSesion == false)
    {
		header("Location: ".dirname(__FILE__)."../../salir.php");
    }
    else
    {
        require "../php/responderJSON.php";
        require ("../php/usuario.class.php");
        $usuario = new usuario($idUsuario,$mysqli);
        $permiso = $usuario->permiso("eliminarDifunto",$mysqli);
        if (!$permiso)
        {
            $response['mensaje'] = "No se pudo eliminar este registro. Usuario con permisos insuficientes para realizar esta acción";
            $response['status'] = 0;
            responder($response, $mysqli);
        }
        function borrarDirectorio($dir)
        {
            if(!$dh = @opendir($dir)) return;
            while (false !== ($current = readdir($dh)))
            {
                if($current != '.' && $current != '..')
                {
                    if (!@unlink($dir.'/'.$current))
                        deleteDirectory($dir.'/'.$current);
                }
            }
            closedir($dh);
            @rmdir($dir);
        }
        $id                             = $_POST['idCliente'];
        $response = array(
            "status"                    => 1
        );
        if(!$id = validarFormulario('i', $id))
        {
            $response['mensaje']        = "El ID del registro del difunto no cumple con el formato establecido";
            $response['status']         = 0;
            $response['focus']          = '';
            responder($response, $mysqli);
        }
        $idUsuario      = $sesion->get('id');
        $sql = "SELECT id, idSucursal, idContrato, idVenta, CONCAT(nombres,' ',apellidop,' ',apellidom) AS nombreDifunto FROM cat_difuntos WHERE id = $id AND activo = 1 LIMIT 1";
        $res_compra = $mysqli->query($sql);
        if ($res_compra->num_rows == 0)
        {
            $response['mensaje'] = "No se puede cancelar este registro porque no existe o ya ha sido cancelado";
            $response['status'] = 0;
            $response['focus'] = '';
            responder($response, $mysqli);
        }
        $row_difunto = $res_compra->fetch_assoc();
        if ($row_difunto['idVenta'] != 0 || $row_difunto['idContrato'] != 0)
        {
            $response['mensaje'] = "No se puede eliminar este registro porque ya está asociado a un contrato o a una venta.";
            $response['status'] = 0;
            $response['focus'] = '';
            responder($response, $mysqli);
        }
        $idSucursal = $row_difunto['idSucursal'];
        $mysqli->autocommit(FALSE);
        $sql = "UPDATE cat_difuntos
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
            else
            {
                $sql = "UPDATE detalle_causasdecesos SET activo = 0 WHERE idDifunto = ?";
                if($prepare_det                     = $mysqli->prepare($sql))
                {
                    if(!$prepare_det->bind_param('i', $id))
                    {
                        $response['mensaje']    = "Error. No se pudo modificar la lista de causa de decesos. Falló el la vinculación de parámetros. Inténtalo nuevamente";
                        $response['status']     = 0;
                        $mysqli->rollback();
                        responder($response, $mysqli);
                    }
                    if(!$prepare_det->execute())
                    {
                        $response['mensaje']    = "Error.  No se pudo modificar la lista de causa de decesos. Falló el enlace a la base de datos. Inténtalo nuevamente";
                        $response['status']     = 0;
                        $mysqli->rollback();
                        responder($response, $mysqli);
                    }
					// Agregar evento en la bitácora de eventos ///////
					$ipUsuario 					= $sesion->get("ip");
					$nombresDifunto				= $row_difunto['nombreDifunto'];
					$pantalla					= "Listar difuntos";
					$descripcion				= "Se ha eliminado un difunto, id=$id, nombre=$nombresDifunto";
					$sql						= "CALL agregarEvento($idUsuario, '$ipUsuario', '$pantalla', '$descripcion', $idSucursal);";
					$mysqli						->query($sql);
                    if($mysqli->commit())
                    {
                        $ds          			= DIRECTORY_SEPARATOR;  //1
                        $insert_id 				= $id;
                        $storeFolder 			= '../images/avatars/difuntos/'.$insert_id;   //2
                        $targetPath 			= dirname( __FILE__ ) . $ds. $storeFolder . $ds;  //4
                        borrarDirectorio($targetPath);
                        $response['mensaje']    = "Este registro ha sido eliminado correctamente";
                        $response['status']     = 1;
                        responder($response, $mysqli);
                    }
                    else
                    {
                        $response['mensaje']    = "Error en commit. Ocurrió un rollback. No se modificó nada. Vuelve  intentarlo";
                        $response['status']     = 0;
                        responder($response, $mysqli);
                    }

                }
                else
                {
                    $response['respuesta'] = "Error. No se pudo modificar la lista de causa de decesos. No se pudo actualizar la información. Falló en la preparación de los datos. Inténtalo nuevamente";
                    $response['status'] = 0;
                    $mysqli->rollback();
                    responder($response, $mysqli);
                }
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

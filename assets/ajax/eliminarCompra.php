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
        $permiso = $usuario->permiso("eliminarCompra",$mysqli);
        if (!$permiso)
        {
            $response['mensaje'] = "No se pudo eliminar este registro. Usuario con permisos insuficientes para realizar esta acción";
            $response['status'] = 0;
            responder($response, $mysqli);
        }
        $id                             = $_POST['idCliente'];
        $response = array(
            "status"                    => 1
        );
        if(!$id = validarFormulario('i', $id))
        {
            $response['mensaje']        = "El ID de la compra no cumple con el formato establecido";
            $response['status']         = 0;
            $response['focus']          = '';
            responder($response, $mysqli);
        }
        $idUsuario      = $sesion->get('id');
        $sql = "SELECT id, idSucursal FROM compras WHERE id = $id AND activo = 1";
        $res_compra = $mysqli->query($sql);
        if ($res_compra->num_rows == 0)
        {
            $response['mensaje'] = "No se puede cancelar esta compra porque no existe o ya ha sido cancelada";
            $response['status'] = 0;
            $response['focus'] = '';
            responder($response, $mysqli);
        }
        $row_compra = $res_compra->fetch_assoc();
        $idSucursal = $row_compra['idSucursal'];
        $mysqli->autocommit(FALSE);
        $sql = "UPDATE compras
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
            // Regresar estado de las existencias antes de la compra
            $sql = "SELECT
                        detalle_compras.idProducto      AS idProducto,
                        detalle_compras.cantidad        AS cantidad
                    FROM detalle_compras
                    WHERE detalle_compras.idCompra = ? AND detalle_compras.idSucursal = ? AND detalle_compras.activo = 1";
            if($prepare_exist_down = $mysqli->prepare($sql))
            {
                if(!$prepare_exist_down->bind_param('ii', $id, $idSucursal))
                {
                    $response['respuesta'] = "Error en acceso al detalle de la compra. No se pudo actualizar la información. Falló la vinculación de parámetros. Inténtalo nuevamente";
                    $response['status'] = 0;
                    $mysqli->rollback();
                    responder($response, $mysqli);
                }
                if(!$prepare_exist_down->execute())
                {
                    $response['respuesta'] = "Error en acceso al detalle de la compra. No se pudo consultar la información. Falló el enlace a la base de datos. Inténtalo nuevamente";
                    $response['status'] = 0;
                    $mysqli->rollback();
                    responder($response, $mysqli);
                }
                $res_info_detalle= $prepare_exist_down->get_result();
                while ($row_info_detalle = $res_info_detalle->fetch_assoc())
                {
                    $idProducto_exist_down = $row_info_detalle['idProducto'];
                    $cantidad_exist_down = $row_info_detalle['cantidad'];
                    $sql = "UPDATE detalle_existenciasproductos
                            SET existencias = existencias - ?
                            WHERE idProducto = ? AND idSucursal = ? LIMIT 1";
                    if($prepare_exist_down_update = $mysqli->prepare($sql))
                    {
                        if (!$prepare_exist_down_update->bind_param('iii',$cantidad_exist_down, $idProducto_exist_down, $idSucursal))
                        {
                            $response['respuesta'] = "Error al intentar actualizar el inventario. No se pudo actualizar la información. Falló la vinculación de parámetros. Inténtalo nuevamente";
                            $response['status'] = 0;
                            $mysqli->rollback();
                            responder($response, $mysqli);
                        }
                        if(!$prepare_exist_down_update->execute())
                        {
                            $response['respuesta'] = "Error al intentar actualizar el inventario. No se pudo consultar la información. Falló el enlace a la base de datos. Inténtalo nuevamente";
                            $response['status'] = 0;
                            $mysqli->rollback();
                            responder($response, $mysqli);
                        }
                    }
                    else
                    {
                        $response['respuesta'] = "Error al intentar actualizar el inventario. No se pudo actualizar la información. Falló en la preparación de los datos. Inténtalo nuevamente";
                        $response['status'] = 0;
                        $mysqli->rollback();
                        responder($response, $mysqli);
                    }
                }
				// Agregar evento en la bitácora de eventos ///////
				$ipUsuario 					= $sesion->get("ip");
				$pantalla					= "Listar compras";
				$descripcion				= "Se ha cancelado una compra, id=$id";
				$sql						= "CALL agregarEvento($idUsuario, '$ipUsuario', '$pantalla', '$descripcion', $idSucursal);";
				$mysqli						->query($sql);
                if($mysqli->commit())
                {
                    $response['mensaje']        = "La compra No. '$id' fue cancelada exitosamente
                                                    </br> <strong><a href='listarCompras.php' class='orange'>Lista de compras</a></strong>
                                                    </br> <strong><a target='_blank' href='assets/pdf/comprobanteCompra.php?idCompra=$id' class='orange'>Imprimir</a></strong>";
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
                $response['respuesta'] = "Error al intentar actualizar el inventario. No se pudo actualizar la información. Falló en la preparación de los datos. Inténtalo nuevamente";
                $response['status'] = 0;
                $mysqli->rollback();
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

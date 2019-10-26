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
        $permiso = $usuario->permiso("eliminarVenta",$mysqli);
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
            $response['mensaje']        = "El ID de la venta no cumple con el formato establecido";
            $response['status']         = 0;
            $response['focus']          = '';
            responder($response, $mysqli);
        }
        $idUsuario      = $sesion->get('id');
        $sql = "SELECT id, idSucursal FROM ventas WHERE id = $id AND activo = 1";
        $res_venta = $mysqli->query($sql);
        if ($res_venta->num_rows == 0)
        {
            $response['mensaje'] = "No se puede cancelar esta venta porque no existe o ya ha sido cancelada";
            $response['status'] = 0;
            $response['focus'] = '';
            responder($response, $mysqli);
        }
        $row_venta = $res_venta->fetch_assoc();
        $idSucursal = $row_venta['idSucursal'];
        $mysqli->autocommit(FALSE);
        $sql = "UPDATE ventas
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
                        detalle_ventas.idProducto      AS idProducto,
                        detalle_ventas.cantidad        AS cantidad
                    FROM detalle_ventas
                    WHERE detalle_ventas.idVenta = ? AND detalle_ventas.idSucursal = ? AND detalle_ventas.activo = 1 AND detalle_ventas.idProducto > 0";
            if($prepare_exist_down = $mysqli->prepare($sql))
            {
                if(!$prepare_exist_down->bind_param('ii', $id, $idSucursal))
                {
                    $response['respuesta'] = "Error en acceso al detalle de la venta. No se pudo actualizar la información. Falló la vinculación de parámetros. Inténtalo nuevamente";
                    $response['status'] = 0;
                    $mysqli->rollback();
                    responder($response, $mysqli);
                }
                if(!$prepare_exist_down->execute())
                {
                    $response['respuesta'] = "Error en acceso al detalle de la venta. No se pudo consultar la información. Falló el enlace a la base de datos. Inténtalo nuevamente";
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
                            SET existencias = existencias + ?
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
                if($mysqli->commit())
                {
                    $response['mensaje']        = "La venta No. '$id' fue cancelada exitosamente
                                                    </br> <strong><a href='listarVentas.php' class='orange'>Lista de ventas</a></strong>
                                                    </br> <strong><a target='_blank' href='assets/pdf/comprobanteVenta.php?idVenta=$id' class='orange'>Imprimir</a></strong>";
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

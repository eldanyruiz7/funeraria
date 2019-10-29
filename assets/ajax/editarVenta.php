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
        $permiso = $usuario->permiso("modificarVenta",$mysqli);
        if (!$permiso)
        {
            $response['mensaje'] = "No se pudo modificar este registro. Usuario con permisos insuficientes para realizar esta acción";
            $response['status'] = 0;
            responder($response, $mysqli);
        }

        $idCliente                      = $_POST['cliente'];
        $idVenta                        = $_POST['idVenta'];
        $response = array(
            "status"                    => 1
        );
        if (!$idCliente = validarFormulario('i',$idCliente,0))
        {
            $response['mensaje'] = "El campo id del cliente no cumple con el formato esperado y no puede estar en blanco<br> Elige un cliente de la lista de clientes";
            $response['status'] = 0;
            $response['focus'] = 'precio';
            responder($response, $mysqli);
        }
        if (!$idVenta = validarFormulario('i',$idVenta,0))
        {
            $response['mensaje'] = "El campo id de la venta no cumple con el formato esperado";
            $response['status'] = 0;
            $response['focus'] = '';
            responder($response, $mysqli);
        }

        $idUsuario      = $sesion->get('id');
        $sql            = "SELECT idSucursal FROM cat_usuarios WHERE id = $idUsuario LIMIT 1";
        $res_noSucursal = $mysqli->query($sql);
        $row_noSucursal = $res_noSucursal->fetch_assoc();
        $idSucursal     = $row_noSucursal['idSucursal'];
        $arrayProductos = json_decode($_POST['arrayProductos']);
        if (sizeof($arrayProductos) == 0)
        {
            $response['status']     = 0;
            $response['mensaje']  = "La lista de productos y servicios no puede estar vacía. Agrega al menos un producto o servicio para poder guardar la venta";
            responder($response, $mysqli);
        }
        $sql = "SELECT id FROM ventas WHERE id = ? AND activo = 1 LIMIT 1";
        if($prepare_venta = $mysqli->prepare($sql))
        {
            if (!$prepare_venta->bind_param('i',$idVenta))
            {
                $response['mensaje'] = "Error en el Id de la venta. Falló la vinculación de parámetros. Inténtalo nuevamente";
                $response['status'] = 0;
                responder($response, $mysqli);
            }
            if (!$prepare_venta->execute())
            {
                $response['mensaje'] = "Error en el Id de la venta. No se pudo consultar la información. Falló el enlace a la base de datos. Inténtalo nuevamente";
                $response['status'] = 0;
                responder($response, $mysqli);
            }
            $res_venta                 = $prepare_venta->get_result();
            if($res_venta->num_rows == 0)
            {
                $response['mensaje']        = "Error. No existe el id <b>($idVenta)</b> de la venta en la Base de datos. Posiblemente ya fue eliminada o cancelada.
                                                <br><b>Esta venta no se puede modificar.</b> <br>No se guardó nada
                                                </br> <strong><a href='listarVentas.php' class='orange'>Lista de ventas</a></strong>";
                $response['status']         = 0;
                responder($response, $mysqli);
            }
            $row_venta = $res_venta->fetch_assoc();
            $idVenta = $row_venta['id'];
        }
        else
        {
            $response['mensaje'] = "Error en el id de la venta. Fallo en la preparación de parámetros. Inténtalo nuevamente";
            $response['status'] = 0;
            responder($response, $mysqli);
        }


        $mysqli->autocommit(FALSE);
        $sql            = "UPDATE ventas
                            SET vendedor = ?, idCliente = ?, tasaComision = ?, idSucursal = ?, usuario = ?
                            WHERE id = ? LIMIT 1";
        if($prepare     = $mysqli->prepare($sql))
        {
            $idVendedor = 0;
            $tasaComision = 0;
            if(!$prepare->bind_param('iiiiii', $idVendedor, $idCliente, $tasaComision, $idSucursal, $idUsuario, $idVenta))
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
            else
            {
                // Regresar estado de las existencias antes de la compra
                $sql = "SELECT
                            detalle_ventas.idProducto      AS idProducto,
                            detalle_ventas.cantidad        AS cantidad
                        FROM detalle_ventas
                        WHERE detalle_ventas.idVenta = ? AND detalle_ventas.idSucursal = ? AND detalle_ventas.activo = 1 AND detalle_ventas.idProducto > 0";
                if($prepare_exist_down = $mysqli->prepare($sql))
                {
                    if(!$prepare_exist_down->bind_param('ii', $idVenta, $idSucursal))
                    {
                        $response['mensaje'] = "Error en acceso al detalle de la venta. No se pudo actualizar la información. Falló la vinculación de parámetros. Inténtalo nuevamente";
                        $response['status'] = 0;
                        $mysqli->rollback();
                        responder($response, $mysqli);
                    }
                    if(!$prepare_exist_down->execute())
                    {
                        $response['mensaje'] = "Error en acceso al detalle de la venta. No se pudo consultar la información. Falló el enlace a la base de datos. Inténtalo nuevamente";
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
                                $response['mensaje'] = "Error al intentar actualizar el inventario. No se pudo actualizar la información. Falló la vinculación de parámetros. Inténtalo nuevamente";
                                $response['status'] = 0;
                                $mysqli->rollback();
                                responder($response, $mysqli);
                            }
                            if(!$prepare_exist_down_update->execute())
                            {
                                $response['mensaje'] = "Error al intentar actualizar el inventario. No se pudo consultar la información. Falló el enlace a la base de datos. Inténtalo nuevamente";
                                $response['status'] = 0;
                                $mysqli->rollback();
                                responder($response, $mysqli);
                            }
                        }
                        else
                        {
                            $response['mensaje'] = "Error al intentar actualizar el inventario. No se pudo actualizar la información. Falló en la preparación de los datos. Inténtalo nuevamente";
                            $response['status'] = 0;
                            $mysqli->rollback();
                            responder($response, $mysqli);
                        }
                    }
                }
                else
                {
                    $response['mensaje'] = "Error al intentar actualizar el inventario. No se pudo actualizar la información. Falló en la preparación de los datos. Inténtalo nuevamente";
                    $response['status'] = 0;
                    $mysqli->rollback();
                    responder($response, $mysqli);
                }
                // Cancelar registros antíguos de detalle de la compra
                $sql = "UPDATE detalle_ventas SET activo = 0 WHERE idVenta = ? AND activo = 1";
                if($prepare_cancelar = $mysqli->prepare($sql))
                {
                    if (!$prepare_cancelar->bind_param('i',$idVenta))
                    {
                        $response['mensaje'] = "Error al cancelar entradas antíguas del detalle de la venta. No se pudo actualizar la información. Falló en la vinculación de los datos. Inténtalo nuevamente";
                        $response['status'] = 0;
                        $mysqli->rollback();
                        responder($response, $mysqli);
                    }
                    if (!$prepare_cancelar->execute())
                    {
                        $response['mensaje'] = "Error al cancelar entradas antíguas del detalle de la venta. No se pudo actualizar la información. Falló en la ejecución de los parámetros. Inténtalo nuevamente";
                        $response['status'] = 0;
                        $mysqli->rollback();
                        responder($response, $mysqli);
                    }
                }
                else
                {
                    $response['mensaje'] = "Error al cancelar entradas antíguas del detalle de la venta. No se pudo actualizar la información. Falló en la preparación de los parámetros. Inténtalo nuevamente";
                    $response['status'] = 0;
                    $mysqli->rollback();
                    responder($response, $mysqli);
                }

                foreach ($arrayProductos as $esteProducto)
                {
                    $nombreProducto         =   $esteProducto    ->nombre;
                    $servicio               =   ($esteProducto    ->servicio == 0) ? 0 : 1;
                    if (!$idProducto = validarFormulario('i',$esteProducto->id, 0))
                    {
                        $mysqli->rollback();
                        $response['mensaje'] = "El formato del id <b>$idProducto->$nombreProducto</b> no es el correcto. Ocurrió un rollback. <br>No se guardó nada. <br>Por favor vuelve a intentarlo";
                        $response['status'] = 0;
                        responder($response, $mysqli);
                        break;
                    }
                    if (!$cantidadProducto = validarFormulario('i',$esteProducto->cantidad, 0))
                    {
                        $mysqli->rollback();
                        $response['mensaje'] = "El formato del parámetro 'cantidad' del producto: <b>$idProducto->$nombreProducto</b> no es el correcto y no puede ser menor o igual que cero (0). Ocurrió un rollback. <br>No se guardó nada. <br>Por favor vuelve a intentarlo";
                        $response['status'] = 0;
                        responder($response, $mysqli);
                        break;
                    }
                    if (!$precioProducto = validarFormulario('i',$esteProducto->precio, 0))
                    {
                        $mysqli->rollback();
                        $response['mensaje'] = "El formato del parámetro 'precio' del producto: <b>$idProducto->$nombreProducto</b> no es el correcto y no puede ser menor o igual que cero (0). Ocurrió un rollback. <br>No se guardó nada. <br>Por favor vuelve a intentarlo";
                        $response['status'] = 0;
                        responder($response, $mysqli);
                        break;
                    }
                    if ($servicio)
                    {
                        $idServicio = $idProducto;
                        $idProducto = 0;
                    }
                    else
                    {
                        $idServicio = 0;
                    }
                    $sql = "INSERT INTO
                                detalle_ventas (idVenta, idProducto, idServicio, precioVenta, cantidad, idSucursal, usuario)
                            VALUES
                                (?,?,?,?,?,?,?)";
                    if($prepare_det = $mysqli->prepare($sql))
                    {
                        if (!$prepare_det->bind_param('iiidiii',$idVenta, $idProducto, $idServicio, $precioProducto, $cantidadProducto, $idSucursal, $idUsuario ))
                        {
                            $mysqli->rollback();
                            $response['mensaje'] = "Error al registrar el detalle de la venta. Error en el id de producto: <b>$idProducto</b>. No se pudo guardar la información. Falló la vinculación de parámetros. Inténtalo nuevamente";
                            $response['status'] = 0;
                            responder($response, $mysqli);
                        }
                        if (!$prepare_det->execute())
                        {
                            $mysqli->rollback();
                            $response['mensaje'] = "Error en el detalle de la venta. No se pudo guardar la información. Error en el id de producto: <b>$idProducto</b>. Falló el enlace a la base de datos. Inténtalo nuevamente";
                            $response['status'] = 0;
                            responder($response, $mysqli);
                        }
                    }
                    else
                    {
                        $mysqli->rollback();
                        $response['mensaje'] = "Error en el detalle de la venta. No se pudo guardar la información. Falló el la preparación de parámetros. Inténtalo nuevamente".$mysqli->error;
                        $response['status'] = 0;
                        responder($response, $mysqli);
                    }
                    if ($idServicio == 0)
                    {

                        $sql =  "SELECT id FROM detalle_existenciasproductos WHERE idProducto = ? AND idSucursal = ?";
                        if($prepare_exist = $mysqli->prepare($sql))
                        {
                            if (!$prepare_exist->bind_param('ii',$idProducto, $idSucursal))
                            {
                                $mysqli->rollback();
                                $response['mensaje'] = "Error al consultar la existencia del producto. Error en el id de producto: <b>$idProducto</b>. No se pudo guardar la información. Falló la vinculación de parámetros. Inténtalo nuevamente";
                                $response['status'] = 0;
                                responder($response, $mysqli);
                            }
                            if (!$prepare_exist->execute())
                            {
                                $mysqli->rollback();
                                $response['mensaje'] = "Error al consultar la existencia del producto. No se pudo guardar la información. Error en el id de producto: <b>$idProducto</b>. Falló el enlace a la base de datos. Inténtalo nuevamente";
                                $response['status'] = 0;
                                responder($response, $mysqli);
                            }
                            $res_exist = $prepare_exist->get_result();
                            if ($res_exist->num_rows == 0)
                            {
                                $sql = "INSERT INTO detalle_existenciasproductos
                                            (idProducto, idSucursal, existencias, usuario)
                                        VALUES (?,?,?,?)";
                                if($prepare_exist_insert = $mysqli->prepare($sql))
                                {
                                    $cantidadP = 0;
                                    if (!$prepare_exist_insert->bind_param('iiii',$idProducto, $idSucursal, $cantidadP, $idUsuario))
                                    {
                                        $mysqli->rollback();
                                        $response['mensaje'] = "Error al consultar la existencia del producto. Error en el id de producto: <b>$idProducto</b>. No se pudo guardar la información. Falló la vinculación de parámetros. Inténtalo nuevamente";
                                        $response['status'] = 0;
                                        responder($response, $mysqli);
                                    }
                                    if (!$prepare_exist_insert->execute())
                                    {
                                        $mysqli->rollback();
                                        $response['mensaje'] = "Error al consultar la existencia del producto. No se pudo guardar la información. Error en el id de producto: <b>$idProducto</b>. Falló el enlace a la base de datos. Inténtalo nuevamente";
                                        $response['status'] = 0;
                                        responder($response, $mysqli);
                                    }
                                }
                            }
                            else
                            {
                                $sql = "UPDATE detalle_existenciasproductos
                                        SET existencias = existencias - ?, usuario = ?
                                        WHERE idSucursal = ? AND idProducto = ? LIMIT 1";
                                if($prepare_exist_insert = $mysqli->prepare($sql))
                                {
                                    if (!$prepare_exist_insert->bind_param('iiii',$cantidadProducto, $idUsuario, $idSucursal, $idProducto))
                                    {
                                        $mysqli->rollback();
                                        $response['mensaje'] = "Error al actulizar la existencia del producto. Error en el id de producto: <b>$idProducto</b>. No se pudo guardar la información. Falló la vinculación de parámetros. Inténtalo nuevamente";
                                        $response['status'] = 0;
                                        responder($response, $mysqli);
                                    }
                                    if (!$prepare_exist_insert->execute())
                                    {
                                        $mysqli->rollback();
                                        $response['mensaje'] = "Error al actualizar la existencia del producto. No se pudo guardar la información. Error en el id de producto: <b>$idProducto</b>. Falló el enlace a la base de datos. Inténtalo nuevamente";
                                        $response['status'] = 0;
                                        responder($response, $mysqli);
                                    }
                                }
                            }
                        }
                        else
                        {
                            $mysqli->rollback();
                            $response['mensaje'] = "Error al preparar los parámetros de las existencias. No se pudo guardar la información. Inténtalo nuevamente";
                            $response['status'] = 0;
                            responder($response, $mysqli);
                        }
                    }
                }
				// Agregar evento en la bitácora de eventos ///////
				$ipUsuario 					= $sesion->get("ip");
				$pantalla					= "Agregar/Modificar venta";
				$descripcion				= "Se modificó una venta con id=$idVenta.";
				$sql						= "CALL agregarEvento($idUsuario, '$ipUsuario', '$pantalla', '$descripcion', $idSucursal);";
				$mysqli						->query($sql);
				//////////////////////////////////////////////////
                if($mysqli->commit())
                {
                    $response['mensaje']        = "Venta No. <b>$idVenta</b> ha sido MODIFICADA correctamente. <br>
                                                    <h5><a target='_blank' href='assets/pdf/comprobanteVenta.php?idVenta=$idVenta' class='orange'>Imprimr venta</a></h5>
                                                    <h5><a href='listarVentas.php' class='orange'>Lista de ventas</a></h5>";
                    $response['status']         = 1;
                    responder($response, $mysqli);
                }
                else
                {
                    $mysqli->rollback();
                    $response['mensaje']        = "Fallo en commit. No se pudo guardar. Inténtalo nuevamente.";
                    $response['status']         = 0;
                    responder($response, $mysqli);
                }
            }
        }
        else
        {
            $mysqli->rollback();
            $response['mensaje']        = "Error. No se pudo modificar. Falló la preparación de los datos. Vuelve a intentarlo nuevamente";
            $response['status']         = 0;
            responder($response, $mysqli);
        }
    }
?>

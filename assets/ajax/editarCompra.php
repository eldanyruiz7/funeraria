<?php
    // error_reporting(E_ALL);
    // ini_set('display_errors', '1');
    //error_reporting(0);
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
        $permiso = $usuario->permiso("modificarCompra",$mysqli);
        if (!$permiso)
        {
            $response['respuesta'] = "No se pudo modificar este registro. Usuario con permisos insuficientes para realizar esta acción";
            $response['status'] = 0;
            responder($response, $mysqli);
        }
        $response = array(
            "status"        => 1
        );
        $idUsuario                  = $sesion->get("id");
        $sql = "SELECT id, idSucursal FROM cat_usuarios WHERE id = ? LIMIT 1";
        if($prepare = $mysqli->prepare($sql))
        {
            if (!$prepare->bind_param('i',$idUsuario))
            {
                $response['respuesta'] = "Error en el Id de usuario. Falló la vinculación de parámetros. Inténtalo nuevamente";
                $response['status'] = 0;
                responder($response, $mysqli);
            }
            if (!$prepare->execute())
            {
                $response['respuesta'] = "Error en el Id de usuario. No se pudo consultar la información. Falló el enlace a la base de datos. Inténtalo nuevamente";
                $response['status'] = 0;
                responder($response, $mysqli);
            }
            $res_usr                 = $prepare->get_result();
            if($res_usr->num_rows == 0)
            {
                $response['respuesta']        = "Error. No existe el id de usuario en la Base de datos. No se guardó nada";
                $response['status']         = 0;
                responder($response, $mysqli);
            }
            $row_usr = $res_usr->fetch_assoc();
            $idSucursal = $row_usr['idSucursal'];
        }
        else
        {
            $response['respuesta'] = "Error en el id de usuario. Fallo en la preparación de parámetros. Inténtalo nuevamente";
            $response['status'] = 0;
            responder($response, $mysqli);
        }
        $idCompra   = $_POST['idCompra'];
        if (strlen($idCompra) == 0)
        {
            $response['respuesta'] = "El formato del id de la compra no es el correcto";
            $response['status'] = 0;
            responder($response, $mysqli);
        }
        if (!is_numeric($idCompra) || $idCompra <= 0)
        {
            $response['respuesta'] = "El formato del id de la compra no es el correcto. Inténtalo nuevamente";
            $response['status'] = 0;
            responder($response, $mysqli);
        }
        $sql = "SELECT id FROM compras WHERE id = ? AND activo = 1 LIMIT 1";
        if($prepare_compra = $mysqli->prepare($sql))
        {
            if (!$prepare_compra->bind_param('i',$idCompra))
            {
                $response['respuesta'] = "Error en el Id de la compra. Falló la vinculación de parámetros. Inténtalo nuevamente";
                $response['status'] = 0;
                responder($response, $mysqli);
            }
            if (!$prepare_compra->execute())
            {
                $response['respuesta'] = "Error en el Id de la compra. No se pudo consultar la información. Falló el enlace a la base de datos. Inténtalo nuevamente";
                $response['status'] = 0;
                responder($response, $mysqli);
            }
            $res_compra                 = $prepare_compra->get_result();
            if($res_compra->num_rows == 0)
            {
                $response['respuesta']        = "Error. No existe el id <b>($idCompra)</b> de la compra en la Base de datos. Posiblemente ya fue eliminada o cancelada.
                                                <br><b>Esta compra no se puede modificar.</b> <br>No se guardó nada
                                                </br> <strong><a href='listarCompras.php' class='orange'>Lista de compras</a></strong>";
                $response['status']         = 0;
                responder($response, $mysqli);
            }
            $row_compra = $res_compra->fetch_assoc();
            $idCompra = $row_compra['id'];
        }
        else
        {
            $response['respuesta'] = "Error en el id de la compra. Fallo en la preparación de parámetros. Inténtalo nuevamente";
            $response['status'] = 0;
            responder($response, $mysqli);
        }
        $idProveedor = $_POST['proveedor'];
        if (strlen($idProveedor) == 0)
        {
            $response['respuesta'] = "Debes elegir un proveedor de la lista. <br> Error: El campo <b>'Proveedor'</b> no puede estar en blanco'";
            $response['status'] = 0;
            responder($response, $mysqli);
        }
        if (!is_numeric($idProveedor) || $idProveedor <= 0)
        {
            $response['respuesta'] = "El formato del id del proveedor no es el correcto. Inténtalo nuevamente";
            $response['status'] = 0;
            responder($response, $mysqli);
        }
        $sql = "SELECT id FROM cat_proveedores WHERE id = ? AND activo = 1 LIMIT 1";
        if($prepare_prov = $mysqli->prepare($sql))
        {
            if (!$prepare_prov->bind_param('i',$idProveedor))
            {
                $response['respuesta'] = "Error en el Id de usuario. Falló la vinculación de parámetros. Inténtalo nuevamente";
                $response['status'] = 0;
                responder($response, $mysqli);
            }
            if (!$prepare_prov->execute())
            {
                $response['respuesta'] = "Error en el Id de usuario. No se pudo consultar la información. Falló el enlace a la base de datos. Inténtalo nuevamente";
                $response['status'] = 0;
                responder($response, $mysqli);
            }
            $res_prov                 = $prepare_prov->get_result();
            if($res_prov->num_rows == 0)
            {
                $response['respuesta']        = "Error. No existe el id <b>($idProveedor)</b> de proveedor en la Base de datos. Posiblemente ya fue eliminado. No se guardó nada";
                $response['status']         = 0;
                responder($response, $mysqli);
            }
            $row_prov = $res_prov->fetch_assoc();
            $idProv = $row_prov['id'];
        }
        else
        {
            $response['respuesta'] = "Error en el id del proveedor. Fallo en la preparación de parámetros. Inténtalo nuevamente";
            $response['status'] = 0;
            responder($response, $mysqli);
        }
        $arrayProductos             = json_decode($_POST['arrayProductos']);
        if (sizeof($arrayProductos) == 0)
        {
            $response['status']     = 0;
            $response['respuesta']  = "La lista de productos no puede estar vacía. Agrega al menos un producto para poder guardar la compra";
            responder($response, $mysqli);
        }
        $mysqli->autocommit(FALSE);

        $sql = "UPDATE compras SET idProveedor = ? WHERE id = ? LIMIT 1";
        if($prepare_compra = $mysqli->prepare($sql))
        {
            if(!$prepare_compra->bind_param('ii', $idProveedor, $idCompra))
            {
                $response['respuesta'] = "Error al actualizar la compra. No se pudo guardar la información. Falló la vinculación de parámetros. Inténtalo nuevamente";
                $response['status'] = 0;
                responder($response, $mysqli);
            }
            if (!$prepare_compra->execute())
            {
                $response['respuesta'] = "Error al actualizar la compra. No se pudo guardar la información. Falló el enlace a la base de datos. Inténtalo nuevamente";
                $response['status'] = 0;
                responder($response, $mysqli);
            }
        }
        else
        {
            $response['respuesta'] = "Error al actualizar la compra. No se pudo guardar la información. Falló la preparación de parámetros. Inténtalo nuevamente";
            $response['status'] = 0;
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
                if(!$prepare_exist_down->bind_param('ii', $idCompra, $idSucursal))
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
            }
            else
            {
                $response['respuesta'] = "Error al intentar actualizar el inventario. No se pudo actualizar la información. Falló en la preparación de los datos. Inténtalo nuevamente";
                $response['status'] = 0;
                $mysqli->rollback();
                responder($response, $mysqli);
            }
            // Cancelar registros antíguos de detalle de la compra
            $sql = "UPDATE detalle_compras SET activo = 0 WHERE idCompra = ? AND activo = 1";
            if($prepare_cancelar = $mysqli->prepare($sql))
            {
                if (!$prepare_cancelar->bind_param('i',$idCompra))
                {
                    $response['respuesta'] = "Error al cancelar entradas antíguas del detalle de la compra. No se pudo actualizar la información. Falló en la vinculación de los datos. Inténtalo nuevamente";
                    $response['status'] = 0;
                    $mysqli->rollback();
                    responder($response, $mysqli);
                }
                if (!$prepare_cancelar->execute())
                {
                    $response['respuesta'] = "Error al intentar actualizar el inventario. No se pudo actualizar la información. Falló en la ejecución de los parámetros. Inténtalo nuevamente";
                    $response['status'] = 0;
                    $mysqli->rollback();
                    responder($response, $mysqli);
                }
            }
            else
            {
                $response['respuesta'] = "Error al intentar actualizar el inventario. No se pudo actualizar la información. Falló en la preparación de los parámetros. Inténtalo nuevamente";
                $response['status'] = 0;
                $mysqli->rollback();
                responder($response, $mysqli);
            }
            foreach ($arrayProductos as $esteProducto)
            {
                $nombreProducto         =   $esteProducto    ->nombre;
                $codigoProducto         =   $esteProducto    ->codigo;
                if (!$idProducto = validarFormulario('i',$esteProducto->id, 0))
                {
                    $response['respuesta'] = "El formato del id <b>$idProducto->$nombreProducto</b> no es el correcto. Ocurrió un rollback. <br>No se guardó nada. <br>Por favor vuelve a intentarlo";
                    $response['status'] = 0;
                    $mysqli->rollback();
                    responder($response, $mysqli);
                    break;
                }
                if (!$cantidadProducto = validarFormulario('i',$esteProducto->cantidad, 0))
                {
                    $response['respuesta'] = "El formato del parámetro 'cantidad' del producto: <b>$idProducto->$nombreProducto</b> no es el correcto y no puede ser menor o igual que cero (0). Ocurrió un rollback. <br>No se guardó nada. <br>Por favor vuelve a intentarlo";
                    $response['status'] = 0;
                    $mysqli->rollback();
                    responder($response, $mysqli);
                    break;
                }
                if (!$precioProducto = validarFormulario('i',$esteProducto->precio, 0))
                {
                    $response['respuesta'] = "El formato del parámetro 'precio' del producto: <b>$idProducto->$nombreProducto</b> no es el correcto y no puede ser menor o igual que cero (0). Ocurrió un rollback. <br>No se guardó nada. <br>Por favor vuelve a intentarlo";
                    $response['status'] = 0;
                    $mysqli->rollback();
                    responder($response, $mysqli);
                    break;
                }
                $idServicio = 0;
                $sql = "INSERT INTO
                            detalle_compras (idCompra, idProducto, idServicio, precioCompra, cantidad, idSucursal, usuario)
                        VALUES
                            (?,?,?,?,?,?,?)";
                if($prepare_det = $mysqli->prepare($sql))
                {
                    if (!$prepare_det->bind_param('iiidiii',$idCompra, $idProducto, $idServicio, $precioProducto,$cantidadProducto, $idSucursal, $idUsuario ))
                    {
                        $response['respuesta'] = "Error al registrar el detalle de la compra. Error en el id de producto: <b>$idProducto</b>. No se pudo guardar la información. Falló la vinculación de parámetros. Inténtalo nuevamente";
                        $response['status'] = 0;
                        $mysqli->rollback();
                        responder($response, $mysqli);
                    }
                    if (!$prepare_det->execute())
                    {
                        $response['respuesta'] = "Error en el detalle de la compra. No se pudo guardar la información. Error en el id de producto: <b>$idProducto</b>. Falló el enlace a la base de datos. Inténtalo nuevamente";
                        $response['status'] = 0;
                        $mysqli->rollback();
                        responder($response, $mysqli);
                    }
                }
                else
                {
                    $response['respuesta'] = "Error en el detalle de la compra. No se pudo guardar la información. Falló el la preparación de parámetros. Inténtalo nuevamente";
                    $response['status'] = 0;
                    $mysqli->rollback();
                    responder($response, $mysqli);
                }
                $sql =  "UPDATE cat_productos SET precioCompra = ? WHERE id = ? LIMIT 1";
                if($prepare_precio = $mysqli->prepare($sql))
                {
                    if (!$prepare_precio->bind_param('ii',$precioProducto, $idProducto))
                    {
                        $response['respuesta'] = "Error al registrar precio del producto. Error en el id de producto: <b>$idProducto</b>. No se pudo guardar la información. Falló la vinculación de parámetros. Inténtalo nuevamente";
                        $response['status'] = 0;
                        $mysqli->rollback();
                        responder($response, $mysqli);
                    }
                    if (!$prepare_precio->execute())
                    {
                        $response['respuesta'] = "Error al registrar el preio del producto. No se pudo guardar la información. Error en el id de producto: <b>$idProducto</b>. Falló el enlace a la base de datos. Inténtalo nuevamente";
                        $response['status'] = 0;
                        $mysqli->rollback();
                        responder($response, $mysqli);
                    }
                }
                else
                {
                    $response['respuesta'] = "Error al registrar precio del producto. Error en el id de producto: <b>$idProducto</b>. No se pudo guardar la información. Falló la vinculación de parámetros. Inténtalo nuevamente";
                    $response['status'] = 0;
                    $mysqli->rollback();
                    responder($response, $mysqli);
                }
                $sql =  "SELECT id FROM detalle_existenciasproductos WHERE idProducto = ? AND idSucursal = ?";
                if($prepare_exist = $mysqli->prepare($sql))
                {
                    if (!$prepare_exist->bind_param('ii',$idProducto, $idSucursal))
                    {
                        $response['respuesta'] = "Error al consultar la existencia del producto. Error en el id de producto: <b>$idProducto</b>. No se pudo guardar la información. Falló la vinculación de parámetros. Inténtalo nuevamente";
                        $response['status'] = 0;
                        $mysqli->rollback();
                        responder($response, $mysqli);
                    }
                    if (!$prepare_exist->execute())
                    {
                        $response['respuesta'] = "Error al consultar la existencia del producto. No se pudo guardar la información. Error en el id de producto: <b>$idProducto</b>. Falló el enlace a la base de datos. Inténtalo nuevamente";
                        $response['status'] = 0;
                        $mysqli->rollback();
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
                            if (!$prepare_exist_insert->bind_param('iiii',$idProducto, $idSucursal, $cantidadProducto, $idUsuario))
                            {
                                $response['respuesta'] = "Error al consultar la existencia del producto. Error en el id de producto: <b>$idProducto</b>. No se pudo guardar la información. Falló la vinculación de parámetros. Inténtalo nuevamente";
                                $response['status'] = 0;
                                $mysqli->rollback();
                                responder($response, $mysqli);
                            }
                            if (!$prepare_exist_insert->execute())
                            {
                                $response['respuesta'] = "Error al consultar la existencia del producto. No se pudo guardar la información. Error en el id de producto: <b>$idProducto</b>. Falló el enlace a la base de datos. Inténtalo nuevamente";
                                $response['status'] = 0;
                                $mysqli->rollback();
                                responder($response, $mysqli);
                            }
                        }
                    }
                    else
                    {
                        $sql = "UPDATE detalle_existenciasproductos
                                SET existencias = existencias + ?, usuario = ?
                                WHERE idSucursal = ? AND idProducto = ? LIMIT 1";
                        if($prepare_exist_insert = $mysqli->prepare($sql))
                        {
                            if (!$prepare_exist_insert->bind_param('iiii',$cantidadProducto, $idUsuario, $idSucursal, $idProducto))
                            {
                                $response['respuesta'] = "Error al actulizar la existencia del producto. Error en el id de producto: <b>$idProducto</b>. No se pudo guardar la información. Falló la vinculación de parámetros. Inténtalo nuevamente";
                                $response['status'] = 0;
                                $mysqli->rollback();
                                responder($response, $mysqli);
                            }
                            if (!$prepare_exist_insert->execute())
                            {
                                $response['respuesta'] = "Error al actualizar la existencia del producto. No se pudo guardar la información. Error en el id de producto: <b>$idProducto</b>. Falló el enlace a la base de datos. Inténtalo nuevamente";
                                $response['status'] = 0;
                                $mysqli->rollback();
                                responder($response, $mysqli);
                            }
                        }
                    }
                }
                else
                {
                    $response['respuesta'] = "Error al preparar los parámetros de las existencias. No se pudo guardar la información. Inténtalo nuevamente";
                    $response['status'] = 0;
                    $mysqli->rollback();
                    responder($response, $mysqli);
                }
            }
			// Agregar evento en la bitácora de eventos ///////
			$idUsuario 					= $sesion->get("id");
			$ipUsuario 					= $sesion->get("ip");
			$idTicket               	= $idCompra;
			$pantalla					= "Editar compra";
			$descripcion				= "Se ha modificado la compra con id=$idTicket";
			$sql						= "CALL agregarEvento($idUsuario, '$ipUsuario', '$pantalla', '$descripcion', $idSucursal);";
			$mysqli						->query($sql);
            if($mysqli->commit())
            {
                $response['status']     = 1;
                $response['respuesta']  = "La compra se ha ctualizado correctamente.
                                            </br>No. de compra: <strong>$idTicket</strong> </br> <strong><a href='listarCompras.php' class='orange'>Lista de compras</a></strong>
                                            </br> <strong><a target='_blank' href='assets/pdf/comprobanteCompra.php?idCompra=$idTicket' class='orange'>Imprimir</a></strong>";
                responder($response, $mysqli);
            }
            else
            {
                $response['status']     = 0;
                $response['respuesta']  = "Ocurrió un error. No se pudo guardar. Error en commit. Vuelve a intentarlo";
                $mysqli->rollback();
                responder($response, $mysqli);
            }
    }
?>

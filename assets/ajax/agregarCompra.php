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
        header("Location: salir.php");
    }
    else
    {
        require "../php/responderJSON.php";
        require ("../php/usuario.class.php");
        $usuario = new usuario($idUsuario,$mysqli);
        $permiso = $usuario->permiso("agregarCompra",$mysqli);
        if (!$permiso)
        {
            $response['respuesta'] = "No se pudo guardar este registro. Usuario con permisos insuficientes para realizar esta acción";
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
            $idProveedor = $row_prov['id'];
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
        $sql = "INSERT INTO compras (usuario, idProveedor, idSucursal) VALUES (?,?,?)";
        if($prepare_compra = $mysqli->prepare($sql))
        {
            if(!$prepare_compra->bind_param('iii',$idUsuario, $idProveedor, $idSucursal))
            {
                $response['respuesta'] = "Error al registrar la compra. No se pudo guardar la información. Falló la vinculación de parámetros. Inténtalo nuevamente";
                $response['status'] = 0;
                responder($response, $mysqli);
            }
            if (!$prepare_compra->execute())
            {
                $response['respuesta'] = "Error en el Id de usuario. No se pudo consultar la información. Falló el enlace a la base de datos. Inténtalo nuevamente";
                $response['status'] = 0;
                responder($response, $mysqli);
            }
            if($prepare_compra->affected_rows == 0)
            {
                $response['respuesta']        = "No se modificó nada, no se pudo registrar la compra, inténtalo nuevamente";
                $response['status']         = 0;
                responder($response, $mysqli);
            }
            $insert_id                  = $mysqli->insert_id;
            // $res_detalle                = $prepare_compra->get_result();
        }
        else
        {
            $response['respuesta'] = "Error al registrar la compra. No se pudo guardar la información. Falló la preparación de parámetros. Inténtalo nuevamente";
            $response['status'] = 0;
            responder($response, $mysqli);
        }

            $idCompra = $mysqli->insert_id;
            foreach ($arrayProductos as $esteProducto)
            {
                $nombreProducto         =   $esteProducto    ->nombre;
                $codigoProducto         =   $esteProducto    ->codigo;
                if (!$idProducto = validarFormulario('i',$esteProducto->id, 0))
                {
                    $mysqli->rollback();
                    $response['respuesta'] = "El formato del id <b>$idProducto->$nombreProducto</b> no es el correcto. Ocurrió un rollback. <br>No se guardó nada. <br>Por favor vuelve a intentarlo";
                    $response['status'] = 0;
                    responder($response, $mysqli);
                    break;
                }
                if (!$cantidadProducto = validarFormulario('i',$esteProducto->cantidad, 0))
                {
                    $mysqli->rollback();
                    $response['respuesta'] = "El formato del parámetro 'cantidad' del producto: <b>$idProducto->$nombreProducto</b> no es el correcto y no puede ser menor o igual que cero (0). Ocurrió un rollback. <br>No se guardó nada. <br>Por favor vuelve a intentarlo";
                    $response['status'] = 0;
                    responder($response, $mysqli);
                    break;
                }
                if (!$precioProducto = validarFormulario('i',$esteProducto->precio, 0))
                {
                    $mysqli->rollback();
                    $response['respuesta'] = "El formato del parámetro 'precio' del producto: <b>$idProducto->$nombreProducto</b> no es el correcto y no puede ser menor o igual que cero (0). Ocurrió un rollback. <br>No se guardó nada. <br>Por favor vuelve a intentarlo";
                    $response['status'] = 0;
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
                        $mysqli->rollback();
                        $response['respuesta'] = "Error al registrar el detalle de la compra. Error en el id de producto: <b>$idProducto</b>. No se pudo guardar la información. Falló la vinculación de parámetros. Inténtalo nuevamente";
                        $response['status'] = 0;
                        responder($response, $mysqli);
                    }
                    if (!$prepare_det->execute())
                    {
                        $mysqli->rollback();
                        $response['respuesta'] = "Error en el detalle de la compra. No se pudo guardar la información. Error en el id de producto: <b>$idProducto</b>. Falló el enlace a la base de datos. Inténtalo nuevamente";
                        $response['status'] = 0;
                        responder($response, $mysqli);
                    }
                }
                else
                {
                    $mysqli->rollback();
                    $response['respuesta'] = "Error en el detalle de la compra. No se pudo guardar la información. Falló el la preparación de parámetros. Inténtalo nuevamente";
                    $response['status'] = 0;
                    responder($response, $mysqli);
                }
                $sql =  "UPDATE cat_productos SET precioCompra = ? WHERE id = ? LIMIT 1";
                if($prepare_precio = $mysqli->prepare($sql))
                {
                    if (!$prepare_precio->bind_param('ii',$precioProducto, $idProducto))
                    {
                        $mysqli->rollback();
                        $response['respuesta'] = "Error al registrar precio del producto. Error en el id de producto: <b>$idProducto</b>. No se pudo guardar la información. Falló la vinculación de parámetros. Inténtalo nuevamente";
                        $response['status'] = 0;
                        responder($response, $mysqli);
                    }
                    if (!$prepare_precio->execute())
                    {
                        $mysqli->rollback();
                        $response['respuesta'] = "Error al registrar el preio del producto. No se pudo guardar la información. Error en el id de producto: <b>$idProducto</b>. Falló el enlace a la base de datos. Inténtalo nuevamente";
                        $response['status'] = 0;
                        responder($response, $mysqli);
                    }
                }
                else
                {
                    $mysqli->rollback();
                    $response['respuesta'] = "Error al registrar precio del producto. Error en el id de producto: <b>$idProducto</b>. No se pudo guardar la información. Falló la vinculación de parámetros. Inténtalo nuevamente";
                    $response['status'] = 0;
                    responder($response, $mysqli);
                }
                $sql =  "SELECT id FROM detalle_existenciasproductos WHERE idProducto = ? AND idSucursal = ?";
                if($prepare_exist = $mysqli->prepare($sql))
                {
                    if (!$prepare_exist->bind_param('ii',$idProducto, $idSucursal))
                    {
                        $mysqli->rollback();
                        $response['respuesta'] = "Error al consultar la existencia del producto. Error en el id de producto: <b>$idProducto</b>. No se pudo guardar la información. Falló la vinculación de parámetros. Inténtalo nuevamente";
                        $response['status'] = 0;
                        responder($response, $mysqli);
                    }
                    if (!$prepare_exist->execute())
                    {
                        $mysqli->rollback();
                        $response['respuesta'] = "Error al consultar la existencia del producto. No se pudo guardar la información. Error en el id de producto: <b>$idProducto</b>. Falló el enlace a la base de datos. Inténtalo nuevamente";
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
                            if (!$prepare_exist_insert->bind_param('iiii',$idProducto, $idSucursal, $cantidadProducto, $idUsuario))
                            {
                                $mysqli->rollback();
                                $response['respuesta'] = "Error al consultar la existencia del producto. Error en el id de producto: <b>$idProducto</b>. No se pudo guardar la información. Falló la vinculación de parámetros. Inténtalo nuevamente";
                                $response['status'] = 0;
                                responder($response, $mysqli);
                            }
                            if (!$prepare_exist_insert->execute())
                            {
                                $mysqli->rollback();
                                $response['respuesta'] = "Error al consultar la existencia del producto. No se pudo guardar la información. Error en el id de producto: <b>$idProducto</b>. Falló el enlace a la base de datos. Inténtalo nuevamente";
                                $response['status'] = 0;
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
                                $mysqli->rollback();
                                $response['respuesta'] = "Error al actulizar la existencia del producto. Error en el id de producto: <b>$idProducto</b>. No se pudo guardar la información. Falló la vinculación de parámetros. Inténtalo nuevamente";
                                $response['status'] = 0;
                                responder($response, $mysqli);
                            }
                            if (!$prepare_exist_insert->execute())
                            {
                                $mysqli->rollback();
                                $response['respuesta'] = "Error al actualizar la existencia del producto. No se pudo guardar la información. Error en el id de producto: <b>$idProducto</b>. Falló el enlace a la base de datos. Inténtalo nuevamente";
                                $response['status'] = 0;
                                responder($response, $mysqli);
                            }
                        }
                    }
                }
                else
                {
                    $mysqli->rollback();
                    $response['respuesta'] = "Error al preparar los parámetros de las existencias. No se pudo guardar la información. Inténtalo nuevamente";
                    $response['status'] = 0;
                    responder($response, $mysqli);
                }
            }
            if($mysqli->commit())
            {
                $idTicket               = $idCompra;
                $response['status']     = 1;
                $response['respuesta']  = "La compra se ha generado correctamente. </br>No. de compra: <strong>$idTicket</strong>
                                            </br> <strong><a href='listarCompras.php' class='orange'>Lista de compras</a></strong>
                                            </br> <strong><a target='_blank' href='assets/pdf/comprobanteCompra.php?idCompra=$idTicket' class='orange'>Imprimir</a></strong>";
                responder($response, $mysqli);
            }
            else
            {
                $mysqli->rollback();
                $response['status']     = 0;
                $response['respuesta']  = "Ocurrió un error. No se pudo guardar. Error en commit. Vuelve a intentarlo";
                responder($response, $mysqli);
            }
    }
?>

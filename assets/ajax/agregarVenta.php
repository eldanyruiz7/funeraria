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
        $permiso = $usuario->permiso("agregarVenta",$mysqli);
        if (!$permiso)
        {
            $response['mensaje'] = "No se pudo guardar este registro. Usuario con permisos insuficientes para realizar esta acción";
            $response['status'] = 0;
            responder($response, $mysqli);
        }

        $idCliente                      = $_POST['cliente'];
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
        $mysqli->autocommit(FALSE);
        $sql            = "INSERT INTO ventas
                                (vendedor, idCliente, tasaComision, idSucursal, usuario)
                            VALUES
                                (?,?,?,?,?)";
        if($prepare     = $mysqli->prepare($sql))
        {
            $idVendedor = 0;
            $tasaComision = 0;
            if(!$prepare->bind_param('iiiii', $idVendedor, $idCliente, $tasaComision, $idSucursal, $idUsuario))
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
            else
            {
                $idVenta 					= $mysqli->insert_id;
				$totalMontoVenta			= 0;
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
					$totalMontoVenta += $precioProducto * $cantidadProducto;
                    if ($idServicio == 0)
                    {

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
                                    $cantidadP = 0;
                                    if (!$prepare_exist_insert->bind_param('iiii',$idProducto, $idSucursal, $cantidadP, $idUsuario))
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
                                        SET existencias = existencias - ?, usuario = ?
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
                }
				// Agregar evento en la bitácora de eventos ///////
				$idUsuario 					= $sesion->get("id");
				$ipUsuario 					= $sesion->get("ip");
				$pantalla					= "Agregar venta";
				$descripcion				= "Se agregó una nueva venta con id=$idVenta. Monto de venta=$$totalMontoVenta";
				$sql						= "CALL agregarEvento($idUsuario, '$ipUsuario', '$pantalla', '$descripcion', $idSucursal);";
				$mysqli						->query($sql);
                if($mysqli->commit())
                {
                    $response['mensaje']        = "Venta No. <b>$idVenta</b> ha sido creada correctamente. <br>
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

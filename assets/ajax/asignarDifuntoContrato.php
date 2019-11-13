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
        require "../php/contrato.class.php";
        require "../php/responderJSON.php";

        $idContrato                     = $_POST['idContrato'];
        $idDifunto                      = $_POST['idDifunto'];
        $observaciones                  = $_POST['observaciones'];
        $selectCajonUrna                = $_POST['selectCajonUrna'];
        $response = array(
            "status"                    => 1
        );
        if (!$idContrato = validarFormulario('i',$idContrato))
        {
            $response['mensaje'] = "El formato del id del contrato no es el correcto";
            $response['status'] = 0;
            $response['focus'] = '';
            responder($response, $mysqli);
        }
        if (!$idDifunto = validarFormulario('i',$idDifunto))
        {
            $response['mensaje'] = "El formato del id del difunto no es el correcto";
            $response['status'] = 0;
            $response['focus'] = '';
            responder($response, $mysqli);
        }
        $observaciones = validarFormulario('s',$observaciones,FALSE);
        $selectCajonUrna = validarFormulario('s',$selectCajonUrna,FALSE);

        $contrato = new contrato($idContrato,$mysqli);
        if ($contrato->id == 0)
        {
            $response['mensaje'] = "No se puede asignar el difunto a este contrato porque el contrato no existe o ha sido eliminado. Vuelve a intentarlo con otro contrato distinto";
            $response['status'] = 0;
            $response['focus'] = '';
            responder($response, $mysqli);
        }
        if ($contrato->idDifunto != 0)
        {
            $response['mensaje'] = "No se puede asignar el difunto a este contrato porque el contrato ya contiene un difunto asignado. Vuelve a intentarlo con otro contrato distinto";
            $response['status'] = 0;
            $response['focus'] = '';
            responder($response, $mysqli);
        }
        else
        {
            $sql = "SELECT id, nombres, apellidop, apellidom FROM cat_difuntos WHERE id = $idDifunto AND idCliente = 0 AND idContrato = 0 AND idVenta = 0 AND activo = 1";
            $res_difunto = $mysqli->query($sql);
            if ($res_difunto->num_rows == 0)
            {
                $response['mensaje'] = "No se puede asignar el difunto a este contrato porque el difunto que intentas asignar ya ha sido asignado o no existe o ha ya sido eliminado. Vuelve a intentarlo con otro difunto distinto";
                $response['status'] = 0;
                $response['focus'] = '';
                responder($response, $mysqli);
            }
            $mysqli->autocommit(FALSE);
            $sql = "UPDATE cat_difuntos SET idContrato = ? WHERE id = ? LIMIT 1";
            $prepare_difunto = $mysqli->prepare($sql);
            if ($prepare_difunto &&
                $prepare_difunto->bind_param("ii",$idContrato, $idDifunto) &&
                $prepare_difunto->execute() &&
                $prepare_difunto->affected_rows > 0)
            {
                $sql = "UPDATE contratos SET idFallecido = ?, observaciones = ? WHERE id = ? LIMIT 1";
                $prepare_contrato = $mysqli->prepare($sql);
                if ($prepare_contrato &&
                    $prepare_contrato->bind_param("isi",$idDifunto, $observaciones, $idContrato) &&
                    $prepare_contrato->execute() &&
                    $prepare_contrato->affected_rows > 0)
                {
                    if ($selectCajonUrna != 0)
                    {
                        $sql = "UPDATE detalle_contrato SET activo = 0 WHERE idContrato = ? AND idProducto <> 0";
                        $prepare_u_det = $mysqli->prepare($sql);
                        if ($prepare_u_det &&
                            $prepare_u_det->bind_param("i", $idContrato) &&
                            $prepare_u_det->execute())
                        {
                            $sql = "INSERT INTO detalle_contrato (idContrato, idProducto, idServicio, idSucursal, cantidad, usuario)
                                    VALUES (?,?,?,?,?,?)";
                                    $idServicio = 0;
                                    $idSucursal = $contrato->idSucursal;
                                    $cantidad = 1;
                            $prepare_i_det = $mysqli->prepare($sql);
                            if (!$prepare_i_det ||
                                !$prepare_i_det->bind_param("iiiiii",$idContrato, $selectCajonUrna, $idServicio, $idSucursal, $cantidad, $idUsuario) ||
                                !$prepare_i_det->execute())
                            {
                                $mysqli->callback();
                                $response['mensaje'] = "No se puede actualizar la información del detalle del contrato. Error al enlazar parámetros";
                                $response['status'] = 0;
                                responder($response, $mysqli);
                            }
                        }
                        else
                        {
                            $mysqli->callback();
                            $response['mensaje'] = "No se puede actualizar la información del detalle del contrato. Error al actualizar el detalle de los parámetros en el catálogo de contratos";
                            $response['status'] = 0;
                            responder($response, $mysqli);
                        }
                    }
                    /// Actualizar inventario de la sucursal
                    $idSucursal = $contrato->idSucursal;
                    $sql = "SELECT id, idProducto, cantidad FROM detalle_contrato WHERE idContrato = $idContrato AND idServicio = 0 AND activo = 1";
                    $res_det = $mysqli->query($sql);
                    while ($row_det = $res_det->fetch_assoc())
                    {
                        $idProducto_ = $row_det['idProducto'];
                        $sql =  "SELECT id FROM detalle_existenciasproductos WHERE idProducto = ? AND idSucursal = ?";
                        if($prepare_exist = $mysqli->prepare($sql))
                        {
                            if (!$prepare_exist->bind_param('ii',$idProducto_, $idSucursal))
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
                                    $cantidadProducto = 0;
                                    if (!$prepare_exist_insert->bind_param('iiii',$cantidadProducto, $idUsuario, $idSucursal, $idProducto_))
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
					// Agregar evento en la bitácora de eventos ///////
					$row_difunto 				= $res_difunto->fetch_assoc();
					$nombreDifunto				= $row_difunto['nombres']." ".$row_difunto['apellidop']." ".$row_difunto['apellidom'];
					$folioContrato				= $contrato->folio;
					$idDifunto					= $row_difunto['id'];
					$idUsuario 					= $sesion->get("id");
					$ipUsuario 					= $sesion->get("ip");
					$pantalla					= "Listar contratos";
					$descripcion				= "Se asignó el difunto($nombreDifunto) con id=$idDifunto al Contrato id=$idContrato, folio=$folioContrato.";
					$sql						= "CALL agregarEvento($idUsuario, '$ipUsuario', '$pantalla', '$descripcion', $idSucursal);";
					$mysqli						->query($sql);
                    $mysqli->commit();
                    $response['mensaje'] = "Difunto asignado correctamente al contrato No. <b>".str_pad($idContrato, 10, "0", STR_PAD_LEFT)."</b>";
                    $response['status'] = 1;
                    responder($response, $mysqli);
                }
                else
                {
                    $mysqli->callback();
                    $response['mensaje'] = "No se puede actualizar la información. Error al enlazar parámetros en el catálogo de contratos";
                    $response['status'] = 0;
                    responder($response, $mysqli);
                }
            }
            else
            {
                $mysqli->callback();
                $response['mensaje'] = "No se puede actualizar la información. Error al enlazar parámetros en el catálogo de difuntos";
                $response['status'] = 0;
                responder($response, $mysqli);
            }
        }
    }
?>

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
		require "../php/query.class.php";
		$query 		= new Query();

        $idContrato                     = $_POST['idContrato'];
        $monto                          = $_POST['monto'];
        $formaPago                      = $_POST['formaPago'];
        $idFolio                        = $_POST['idFolio'];
        $response = array(
            "status"                    => 1
        );
		$idUsuario      = $sesion->get('id');
		$sql            = "SELECT idSucursal FROM cat_usuarios WHERE id = $idUsuario LIMIT 1";
		$res_noSucursal = $mysqli->query($sql);
		$row_noSucursal = $res_noSucursal->fetch_assoc();
		$idSucursal     = $row_noSucursal['idSucursal'];

        if (!$monto = validarFormulario('i',$monto,0))
        {
            $response['mensaje'] = "El monto no puede ser igual o menor que cero";
            $response['status'] = 0;
            $response['focus'] = 'inputPagaCon';
            responder($response, $mysqli);
        }
        if (!$idContrato = validarFormulario('i',$idContrato))
        {
            $response['mensaje'] = "El formato del id del contrato no es el correcto";
            $response['status'] = 0;
            $response['focus'] = '';
            responder($response, $mysqli);
        }
        if (!$formaPago = validarFormulario('i',$formaPago))
        {
            $response['mensaje'] = "El formato del id de la forma de pago no es el correcto";
            $response['status'] = 0;
            $response['focus'] = 'selectFormaPago';
            responder($response, $mysqli);
        }
        if (!$idFolio = validarFormulario('i',$idFolio))
        {
            $response['mensaje'] = "El formato del id del folio del recibo de cobro no es el correcto. <br>Debes elegir un folio físico de cobro";
            $response['status'] = 0;
            $response['focus'] = 'selectFolio';
            responder($response, $mysqli);
        }
		$row_folio = $query	->table("folios_cobranza_asignados AS fca")->select( "fca.id AS id, fca.folio AS folio,
																				  fca.idUsuario_asignado AS idUsuario_asignado,
																				  cu.tasaComisionCobranza AS tasaComisionCobranza")
							->innerJoin("cat_usuarios AS cu", "fca.idUsuario_asignado", "=", "cu.id")
							->where("fca.id", "=", $idFolio, "i")->and()->where("fca.asignado", "=", 0, "i")->limit()->execute();

        if ($query->num_rows() == 0)
        {
            $response['mensaje'] = "No se puede asignar este folio, posiblemente fue eliminado o ya ha sido asignado con anterioridad";
            $response['status'] = 0;
            $response['focus'] = '';
            responder($response, $mysqli);
        }
        $mysqli->autocommit(FALSE);
        require "../php/contrato.class.php";
        $contrato = new Contrato($idContrato,$query);
        if ($contrato->id == 0)
        {
            $response['mensaje'] = "No se puede añadir pago a este contrato porque no existe o ya ha sido eliminado. Vuelve a intentarlo con otro contrato distinto";
            $response['status'] = 0;
            $response['focus'] = '';
            responder($response, $mysqli);
        }
        else
        {
            $totalSaldo = $contrato->saldo($mysqli);
            if ($monto > $totalSaldo)
            {
                $monto = number_format($monto,2,".",",");
                $totalSaldo = number_format($totalSaldo,2,".",",");
                $response['mensaje'] = "El monto del pago (<b>$$monto</b>) no puede ser mayor que el saldo (<b>$$totalSaldo</b>).";
                $response['status'] = 0;
                $response['focus'] = 'inputPagaCon';
                responder($response, $mysqli);
            }
            $idContrato = $contrato->id;
            $sql = "INSERT INTO detalle_pagos_contratos
                        (idContrato, monto, tasaComisionCobranza, usuario_cobro, usuario_registro, formaPago, idFolio_cobranza)
                    VALUES (?,?,?,?,?,?, ?)";
            $prepare_pago = $mysqli->prepare($sql);
            $idRecibo = $row_folio[0]['id'];
			$idUsuarioCobro = $row_folio[0]['idUsuario_asignado'];
            $tasaComisionCobranza = $row_folio[0]['tasaComisionCobranza'];
            if ($prepare_pago &&
                $prepare_pago->bind_param("idiiiii",$idContrato, $monto, $tasaComisionCobranza, $idUsuarioCobro, $idUsuario, $formaPago, $idRecibo) &&
                $prepare_pago->execute() &&
                $prepare_pago->affected_rows > 0)
            {
                $insert_id = $prepare_pago->insert_id;
                $sql = "UPDATE folios_cobranza_asignados SET asignado = ? WHERE id = ? LIMIT 1";
                $prepare_asign = $mysqli->prepare($sql);
                if ($prepare_asign &&
                    $prepare_asign->bind_param("ii", $insert_id, $idRecibo) &&
                    $prepare_asign->execute() &&
                    $prepare_asign->affected_rows > 0)
                {
					// Agregar evento en la bitácora de eventos ///////
					$idUsuario 				= $sesion->get("id");
					$ipUsuario 				= $sesion->get("ip");
					$pantalla				= "Listar contratos";
					$folioFisico			= $row_folio[0]['folio'];
					$descripcion			= "Se registró un nuevo pago($$monto) al contrato=$idContrato, folio físico=$folioFisico, id del cobrador=$idUsuarioCobro.";
					$sql					= "CALL agregarEvento($idUsuario, '$ipUsuario', '$pantalla', '$descripcion', $idSucursal);";
					$mysqli					->query($sql);
					//////////////////////////////////////////////////
                    if ($contrato->saldo($mysqli) <= 0)
                    {
                        $sql = "UPDATE contratos SET enCurso = 0 WHERE id = ? LIMIT 1";
                        $prepare_curso = $mysqli->prepare($sql);
                        if ($prepare_curso &&
                            $prepare_curso->bind_param("i", $idContrato) &&
                            $prepare_curso->execute() &&
                            $prepare_curso->affected_rows > 0)
                        {
							$descripcion			= "Contrato con id=$idContrato saldado exitosamente.";
							$sql					= "CALL agregarEvento($idUsuario, '$ipUsuario', '$pantalla', '$descripcion', $idSucursal);";
							$mysqli					->query($sql);
							//////////////////////////////////////////////////
                            $mysqli->commit();
                            $code = str_pad($idContrato, 10, "0", STR_PAD_LEFT);
                            $response['mensaje'] = "Pago de <b>$$monto</b> para el contrato No. <b>$code ha</b> sido creado correctamente<br>
                                                    <h5><a target='_blank' href='assets/pdf/recibo.php?idRecibo=$insert_id' class='orange'>Imprimr recibo de este pago</a></h5>";
                            $response['mensaje2'] = "Este contrato ha sido saldado exitosamente. Será archivado en la lista de contratos pagados<br>
                                                    <h5><a target='_blank' href='' class='orange'>Lista de contratos a</a></h5>";
                            $response['status'] = 2;
                            responder($response, $mysqli);
                        }
                        else
                        {
                            $mysqli->rollback();
                            $response['mensaje'] = "Ocurrió un error al enlazar los parámetros del curso del contrato. Vuelve a intentarlo";
                            $response['status'] = 0;
                            $response['focus'] = '';
                            responder($response, $mysqli);
                        }
                    }
                    else
                    {

                    $mysqli->commit();
                    $code = str_pad($idContrato, 10, "0", STR_PAD_LEFT);
                    $response['mensaje'] = "Pago de <b>$$monto</b> para el contrato No. <b>$code ha</b> sido creado correctamente<br>
                                            <h5><a target='_blank' href='assets/pdf/recibo.php?idRecibo=$insert_id' class='orange'>Imprimr recibo de este pago</a></h5>";
                    $response['status'] = 1;
                    responder($response, $mysqli);
                    }
                }
                else
                {
                    $mysqli->rollback();
                    $response['mensaje'] = "Ocurrió un error al enlazar los parámetros del detalle del folio. Vuelve a intentarlo";
                    $response['status'] = 0;
                    $response['focus'] = '';
                    responder($response, $mysqli);
                }
            }
            else
            {
                $mysqli->rollback();
                $response['mensaje'] = "No se puede almacenar la información. Error al enlazar parámetros";
                $response['status'] = 0;
                responder($response, $mysqli);
            }
        }
    }
?>

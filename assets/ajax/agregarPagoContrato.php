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

        $idContrato                     = $_POST['idContrato'];
        $monto                          = $_POST['monto'];
        $formaPago                      = $_POST['formaPago'];
        $idFolio                        = $_POST['idFolio'];
        $response = array(
            "status"                    => 1
        );

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
        $sql = "SELECT id, idUsuario_asignado
                FROM folios_cobranza_asignados
                WHERE id = $idFolio
                AND activo = 1 AND asignado = 0 LIMIT 1";
        $res_folio = $mysqli->query($sql);
        if ($res_folio->num_rows == 0)
        {
            $response['mensaje'] = "No se puede asignar este folio, posiblemente fue eliminado o ya ha sido asignado con anterioridad";
            $response['status'] = 0;
            $response['focus'] = '';
            responder($response, $mysqli);
        }
        $mysqli->autocommit(FALSE);
        $row_folio = $res_folio->fetch_assoc();
        require "../php/contrato.class.php";
        $contrato = new contrato($idContrato,$mysqli);
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
                        (idContrato, monto, usuario_cobro, usuario_registro, formaPago, idFolio_cobranza)
                    VALUES (?,?,?,?,?,?)";
            $prepare_pago = $mysqli->prepare($sql);
            $idFolio = $row_folio['id'];
            $idUsuarioCobro = $row_folio['idUsuario_asignado'];
            if ($prepare_pago &&
                $prepare_pago->bind_param("idiiii",$idContrato, $monto, $idUsuarioCobro, $idUsuario, $formaPago, $idFolio) &&
                $prepare_pago->execute() &&
                $prepare_pago->affected_rows > 0)
            {
                $insert_id = $prepare_pago->insert_id;
                $sql = "UPDATE folios_cobranza_asignados SET asignado = ? WHERE id = ? LIMIT 1";
                $prepare_asign = $mysqli->prepare($sql);
                if ($prepare_asign &&
                    $prepare_asign->bind_param("ii", $insert_id, $idFolio) &&
                    $prepare_asign->execute() &&
                    $prepare_asign->affected_rows > 0)
                {
                    if ($contrato->saldo($mysqli) <= 0)
                    {
                        $sql = "UPDATE contratos SET enCurso = 0 WHERE id = ? LIMIT 1";
                        $prepare_curso = $mysqli->prepare($sql);
                        if ($prepare_curso &&
                            $prepare_curso->bind_param("i", $idContrato) &&
                            $prepare_curso->execute() &&
                            $prepare_curso->affected_rows > 0)
                        {
                            $mysqli->commit();
                            $code = str_pad($idContrato, 10, "0", STR_PAD_LEFT);
                            $response['mensaje'] = "Pago de <b>$$monto</b> para el contrato No. <b>$code ha</b> sido creado correctamente<br>
                                                    <h5><a target='_blank' href='assets/pdf/recibo.php?idRecibo=$insert_id' class='orange'>Imprimr recibo de este pago</a></h5>";
                            $response['mensaje2'] = "Este contrato ha sido pagado exitosamente. Será archivado en la lista de contratos pagados<br>
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

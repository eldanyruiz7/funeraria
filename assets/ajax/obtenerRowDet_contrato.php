<?php
    require_once ('../connect/bd.php');
    require_once ("../connect/sesion.class.php");
    $sesion = new sesion();
    require_once ("../connect/cerrarOtrasSesiones.php");
    require_once ("../connect/usuarioLogeado.php");
    if( logueado($idSesion,$idUsuario,$mysqli) == false || $idSesion == false)
    {
		header("Location: ".dirname(__FILE__)."../../salir.php");
    }
    else
    {
        require_once "../php/contrato.class.php";
        require_once "../php/funcionesVarias.php";
        require_once "../php/responderJSON.php";
		require_once ("../php/query.class.php");
		$query = new Query();
        $response = array(
            "status"        => 1
        );
        $idContrato = $_POST['idCliente'];
        $contrato = new Contrato($idContrato,$query);
        if ($contrato->id == 0)
        {
            $response['mensaje'] = "Error. No se pudo consultar la información. No existe el id del contrato o posiblemente ya ha sido eliminado. Inténtalo nuevamente";
            $response['status'] = 0;
            responder($response, $mysqli);
        }
        $idContrato = $contrato->id;
        $sql = "SELECT COUNT(id) AS totalPagos FROM detalle_pagos_contratos
         WHERE detalle_pagos_contratos.idContrato = $idContrato
         AND detalle_pagos_contratos.activo = 1";
         $res_cont_pagos = $mysqli->query($sql);
         $row_cont_pagos = $res_cont_pagos->fetch_assoc();

        $response['fechaCreacion']          = $contrato->fechaCreacion();
        $response['fechaPrimerAportacion']  = $contrato->fechaPrimerAportacion();
        $response['estadoContrato']         = $contrato->comentarios_estatus($mysqli);
        $response['noAportaciones']         = $contrato->pagosCalculados();
        $response['aportacionesReg']        = $row_cont_pagos['totalPagos'];
        $response['domicilio']              = $contrato->domicilio;
        $response['referencias']            = $contrato->referencias;
        $response['rfc']                    = $contrato->rfcCliente;
        $response['telefono']               = $contrato->telCliente;
        $response['celular']                = $contrato->celCliente;
        $response['nombrePlan']             = $contrato->nombrePlan;
        $response['tasaComision']           = $contrato->tasaComision;
        $response['nombreVendedor']         = $contrato->nombreVendedor;
        $response['nombreUsuario']          = $contrato->nombreUsuario;
        $response['idVendedor']             = $contrato->idVendedor;
        $response['precio']                 = "$".number_format($contrato->precio,2,".",",");
        $response['aportacion']             = "$".number_format($contrato->aportacion,2,".",",");
        $response['primerAportacion']       = "$".number_format($contrato->anticipo,2,".",",");
        $response['abonado']                = "$".number_format($contrato->totalAbonado($mysqli),2,".",",");
        $response['sucursal']               = $contrato->domicilioSucursal;
        $response['observaciones']          = $contrato->observaciones;
        $response['costoTotal']             = "$".number_format($contrato->costoTotal,2,".",",");
        $response['descuentoDuplicacionInversion'] = "$".number_format($contrato->descuentoDuplicacionInversion,2,".",",");
        $response['descuentoCambioFuneraria'] = "$".number_format($contrato->descuentoCambioFuneraria,2,".",",");
        $response['descuentoAdicional']     = "$".number_format($contrato->descuentoAdicional,2,".",",");
        // $response['html_hist']              = "";

        $response['status']                 = 1;

        $sql = "SELECT
                    detalle_pagos_contratos.id              AS idPago,
                    detalle_pagos_contratos.fechaCreacion   AS fechaCreacion,
                    detalle_pagos_contratos.monto           AS monto,
                    cat_formas_pago.nombre                  AS formaPago,
                    folios_cobranza_asignados.folio         AS folio
                FROM detalle_pagos_contratos
                INNER JOIN cat_formas_pago
                ON detalle_pagos_contratos.formaPago = cat_formas_pago.id
                INNER JOIN folios_cobranza_asignados
                ON detalle_pagos_contratos.idFolio_cobranza = folios_cobranza_asignados.id
                WHERE detalle_pagos_contratos.idContrato = $idContrato
                AND detalle_pagos_contratos.activo = 1 ORDER BY detalle_pagos_contratos.id ASC";
        $res_pagos = $mysqli->query($sql);
        $abonado = $contrato->anticipo;
        $saldo = $contrato->costoTotal - $abonado;
        $response['html_hist']              = " <tr>";
        $response['html_hist']             .= "     <td>Inversión</td>";
        $response['html_hist']             .= "     <td>--</td>";
        $response['html_hist']             .= "     <td>".$response['fechaCreacion']."</td>";
        $response['html_hist']             .= "     <td class='text-right'>".$response['primerAportacion']."</td>";
        $response['html_hist']             .= "     <td class='text-right'>$".number_format($saldo,2,".",",")."</td>";
        $response['html_hist']             .= "     <td>Efectivo</td>";
        $response['html_hist']             .= '     <td class="text-center">
        <a href="assets/pdf/reciboAnticipo.php?idContrato='.$idContrato.'" target="_blank" class="orange pointer" data-rel="tooltip" title="Imprimir recibo de pago">
        <i class="ace-icon fa fa-print bigger-130"></i>
        </a>
        </td>';
        $response['html_hist']             .= " </tr>";
        if ($res_pagos->num_rows > 0)
        {
            while ($row_pagos = $res_pagos->fetch_assoc())
            {
                $saldo -= $row_pagos['monto'];
                $response['html_hist']             .= " <tr>";
                $response['html_hist']             .= "     <td>".str_pad($row_pagos['idPago'], 9, "0", STR_PAD_LEFT)."</td>";
                $response['html_hist']             .= "     <td>".$row_pagos['folio']."</td>";
                $response['html_hist']             .= "     <td>".date_format(date_create($row_pagos['fechaCreacion']),"d-m-Y h:i:s a")."</td>";
                $response['html_hist']             .= "     <td class='text-right'>$".number_format($row_pagos['monto'],2,".",",")."</td>";
                $response['html_hist']             .= "     <td class='text-right'>$".number_format($saldo,2,".",",")."</td>";
                $response['html_hist']             .= "     <td>".$row_pagos['formaPago']."</td>";
                $response['html_hist']             .= '     <td class="text-center">
                                                                <a href="assets/pdf/recibo.php?idRecibo='.$row_pagos['idPago'].'" target="_blank" class="orange pointer" data-rel="tooltip" title="Imprimir recibo de pago">
                            									    <i class="ace-icon fa fa-print bigger-130"></i>
                            									</a>
                                                            </td>';
                $response['html_hist']             .= " </tr>";
            }
        }
        responder($response, $mysqli);
    }
?>

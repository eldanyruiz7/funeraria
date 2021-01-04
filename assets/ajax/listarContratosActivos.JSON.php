<?php
    // error_reporting(E_ALL);
    // ini_set('display_errors', '1');
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
        require "../php/responderJSON.php";
        require "../php/contrato.class.php";
        require_once "../php/funcionesVarias.php";
		require_once ("../php/query.class.php");
		$query = new Query();
        $response = array(
            "status"        => 1
        );

		/**
		 * Réplica función contrato.class
		 */
		 function estatus_cobranza($Contrato)
		 {
		 	if ($Contrato->motivoCancelado == 0)
		 	{
	 			$fechaUltimoPago = new DateTime($Contrato->fechaUltimoAbono);
		 		$hoy = new DateTime("now");
		 		$diff = $hoy->diff($fechaUltimoPago);
		 		$diferenciaDias = $diff->days;
		 		$atrasado = FALSE;
		 		switch ($Contrato->idFrecuenciaPago)
				{
		 			case 1:
		 				if ($diferenciaDias > 14)
		 				{
		 					$atrasado = TRUE;
		 				}
		 				break;
		 			case 2:
		 			case 3:
		 				if ($diferenciaDias > 30)
		 				{
		 					$atrasado = TRUE;
		 				}
		 				break;
		 		}
	 			if ($atrasado)
	 				$e = '<span class="label label-danger" style="margin-bottom:1px"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Atrasado</span>';
	 			else
	 				$e = '<span class="label label-success" style="margin-bottom:1px"><i class="fa fa-check" aria-hidden="true"></i> Al corriente</span>';
	 			$e .= '<br>';

				if ($Contrato->enCurso)
					$e .= '<span class="label label-info"><i class="fa fa-check-circle-o" aria-hidden="true"></i> En curso</span>';
				else
					$e.= '<span class="label label-inverse"><i class="fa fa-check-square" aria-hidden="true"></i> Pagado</span>';
		 	}
			else
	 			$e = '<span class="label"><i class="fa fa-ban" aria-hidden="true"></i> Cancelado</span>';

		 	return $e;
		}

		/**
		 * Select contratos con clase query
		 */
		$resContratos 		= $query->table("contratos AS c")
									->select("c.id, CONCAT(cli.nombres, ' ', cli.apellidop, ' ', cli.apellidom) AS nombreCliente,
											  c.precio,
											  c.descuentoDuplicacionInversion,
											  c.descuentoCambioFuneraria,
											  c.descuentoAdicional,
											  c.primerAnticipo,
											  c.idFallecido,
											  CONCAT(dif.nombres, ' ', dif.apellidop, ' ', dif.apellidom) AS nombreDifunto,
											  c.idFactura,
											  c.enCurso,
											  c.folio,
											  c.motivoCancelado,
											  c.fechaCreacion,
											  c.frecuenciaPago AS idFrecuenciaPago,
											  cfp.clase AS frecuenciaPago,
											  (c.precio - c.descuentoDuplicacionInversion - c.descuentoCambioFuneraria - c.descuentoAdicional) AS costoTotal,
											  c.precioAportacion AS aportacion,
											  (IFNULL(SUM(dpc.monto),0) + c.primerAnticipo) AS totalAbonado,
											  IFNULL(MAX(dpc.fechaCreacion),c.fechaPrimerAportacion) AS fechaUltimoAbono")
									->leftJoin("clientes AS cli", "c.idTitular", "=", "cli.id")
									->leftJoin("cat_difuntos AS dif", "c.idFallecido", "=", "dif.id")
									->leftJoin("detalle_pagos_contratos AS dpc", "c.id", "=", "dpc.idContrato")->and("dpc.activo", "=", 1)
									->leftJoin("cat_frecuencias_pago AS cfp", "cfp.id", "=", "c.frecuenciaPago")
									->where("c.enCurso", "=", 1 , "i")->and()
									->where("c.activo", "=", 1, "i")
									->groupBy("c.id")
									->execute(FALSE, RETURN_OBJECT);
									// echo $query ->lastStatement()."<br>";
        if ($query->num_rows() == 0)
        {
            $json_data = [
                "data"   => 0
            ];
        }
        else
        {
			foreach ($resContratos as $Contrato)
			{
                $htmlBtns = '<div class="action-buttons">';
                if ($Contrato->idFactura != 0)
                {
                    $htmlBtns.=             '<a class="green pointer fPdf" id="'.$Contrato->idFactura.'" target="_blank" href="assets/pdf/facturaPDF.php?idFactura='.$Contrato->idFactura.'" data-rel="tooltip" title="PDF factura">
                                                <i class="ace-icon fa fa-file-pdf-o bigger-130"></i>
                                            </a>
                                            <a class="green pointer aXml" id="" target="_blank" href="assets/ws/descargarXML.php?xml='.$Contrato->idFactura.'" data-rel="tooltip" title="Descargar XML">
                                                <i class="ace-icon fa fa-file-code-o bigger-130"></i>
                                            </a>';
                }
                else
                {
                    if (!$Contrato->enCurso)
                    {
                        $htmlBtns.=         '<a class="pink pointer crearFactura" id="'.$Contrato->idFactura.'" href="agregarFactura_contrato.php?idVenta='.$Contrato->idFactura.'" data-rel="tooltip" title="Agegar factura">
                                                <i class="ace-icon fa fa-bolt bigger-130"></i>
                                            </a>';
                    }
                }
                $htmlBtns.=                 '<a href="assets/pdf/contrato.php?idContrato='.$Contrato->id.'" target="_blank" class="blue pointer aPdf" name="'.$Contrato->id.'" data-rel="tooltip" title="Imprimir contrato">
                                                <i class="ace-icon fa fa-file-pdf-o bigger-130"></i>
                                            </a>
                                            <a class="orange pointer aEstadoCuenta" target="_blank" id="'.$Contrato->id.'" href="assets/pdf/estadoCuenta.php?idContrato='.$Contrato->id.'" data-rel="tooltip" title="Generar estado de cuenta">
                                                <i class="ace-icon fa fa-file-text-o bigger-130"></i>
                                            </a>
                                            <a class="green2 pointer aModalPago" name="'.$Contrato->id.'" data-toggle="modal" data-rel="tooltip" title="Agregar pago">
                                                <i class="ace-icon fa fa-money bigger-130"></i>
                                            </a>
                                            <a class="green pointer aAsignarDifunto" name="'.$Contrato->id.'" data-rel="tooltip" title="Asignar difunto">
                                                <i class="ace-icon fa fa-user-circle bigger-130"></i>
                                            </a>';
                if ($Contrato->motivoCancelado)
                {
                    $htmlBtns.=             '<a class="grey pointer aReactivar" name="'.$Contrato->id.'" data-rel="tooltip" title="Reactivar contrato">
                                                <i class="ace-icon fa fa-level-up bigger-130"></i>
                                            </a>';
                }
                else {
                    $htmlBtns.=             '<a class="red pointer aCancelar" name="'.$Contrato->id.'" data-rel="tooltip" title="Cancelar contrato">
                                                <i class="ace-icon fa fa-ban bigger-130"></i>
                                            </a>';
                }
                $htmlBtns.=                 '<a class="purple pointer aEdit" id="'.$Contrato->id.'" href="agregarContrato.php?idContrato='.$Contrato->id.'" data-rel="tooltip" title="Editar contrato">
                                                <i class="ace-icon fa fa-pencil-square-o bigger-130"></i>
                                            </a>
                                        </div>';
				$saldo = ($Contrato->precio - $Contrato->descuentoDuplicacionInversion - $Contrato->descuentoCambioFuneraria - $Contrato->descuentoAdicional) - $Contrato->totalAbonado;
                $InfoData[] = array(
                    'id'                =>str_pad($Contrato->id, 9, "0", STR_PAD_LEFT),
                    'folio'             => $Contrato->folio,
                    'fechaCreacion'     => date_format(date_create($Contrato->fechaCreacion), 'd-m-Y'),
                    'precio'            => "$".number_format($Contrato->costoTotal,2,".",","),
                    'precioAportacion'  => "$".number_format($Contrato->aportacion,2,".",","),
                    'abonado'           => "$".number_format($Contrato->totalAbonado,2,".",","),
                    'resta'             => "$".number_format($saldo,2,".",","),
                    'nombreDifunto'     => strlen($Contrato->nombreDifunto) > 0 ? $Contrato->nombreDifunto : '<span class="label label-white middle">No asignado</span>',
                    'nombresTitular'    => $Contrato->nombreCliente,
                    'frecuenciaPago'    => $Contrato->frecuenciaPago,
                    'estatus_cobranza'  => estatus_cobranza($Contrato),
                    'btns'              => $htmlBtns);
            }
            $json_data = [
                "data"   => $InfoData
            ];
        }
        echo json_encode($json_data);
    }

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
        header("Location: salir.php");
    }
    else
    {
		$fechaInicio = $_POST['fechaInicio'];
		// $fechaInicio = $_GET['fechaInicio'];
        $fInicio_e = explode('-',$fechaInicio);
        $Y_ini = intval($fInicio_e[0]);
        $m_ini = intval($fInicio_e[1]);
        $d_ini = intval($fInicio_e[2]);
        // var_dump($_GET);
		$fechaFin = $_POST['fechaFin'];
        // $fechaFin = $_GET['fechaFin'];
        $fFin_e = explode('-',$fechaFin);
        $Y_fin = intval($fFin_e[0]);
        $m_fin = intval($fFin_e[1]);
        $d_fin = intval($fFin_e[2]);
        // var_dump($fInicio_e);
        if(checkdate($m_ini,$d_ini,$Y_ini) == FALSE || checkdate($m_fin,$d_fin,$Y_fin) == FALSE)
        {
            $json_data = [
                "data"   => 0
            ];
            echo json_encode($json_data);
            die;
        }
		$response = array(
			"status"        => 1
		);
		require_once "../php/contrato.class.php";
        require "../php/responderJSON.php";
        require_once "../php/funcionesVarias.php";
		require_once "../php/query.class.php";
		$query 		= new Query();

		/**
		 * Obtener el Id de Sucursal
		 */
		$resultSuc = $query ->table('cat_usuarios')->select("idSucursal")
							->where("id", "=", $idUsuario, "i")->limit()->execute();
		$idSucursal= $resultSuc[0]['idSucursal'];

		/**
		 * Obtener el tipo de periodo (Semanal, quincenal, mensual)
		 */
		$resTipoPeriodo = $query->table("cat_sucursales")->select("periodoNomina")->where("id", "=", $idSucursal, "i")
								->and()->where("activo", "=", 1, "i")->limit()->execute();
		$tipoPeriodo = $resTipoPeriodo[0]["periodoNomina"];

		/**
		 * Obtener el total de nóminas a generar
		 */
		$totalNominas = $query ->table("cat_usuarios") ->select("id AS idUsuario, CONCAT(nombres, ' ', apellidop, ' ', apellidom) AS nombres")
								->where("activo", "=", 1, "i")->and()->where("id", "<>", 1, "i")->orderBy("nombres")->execute();

        $num = $query->num_rows();
        if ($num == 0)
        {
			$json_data["Records"] = 0;
        }
        else
        {
			$fechaInicio .= " 00:00:00";
	        $fechaFin .= " 23:59:59";
			$idUsuarioCreo = $idUsuario;
			$query ->autocommit(FALSE);
			$query ->table("cat_periodos_nominas")
				   ->insert(compact("tipoPeriodo", "fechaInicio", "fechaFin", "idUsuarioCreo", "idSucursal"), "issii")->execute();
			$idPeriodo = $query->insert_id();
			$tipo = 1;
            foreach ($totalNominas as $nomina)
			{
				$idUsuario = $nomina['idUsuario'];
				$query ->table("cat_nominas")->insert(compact("idPeriodo", "idUsuario"), "ii")->execute();
				$idNominaCobranza = $idNominaVenta = $idNomina = $query->insert_id();
				/**
				 * Obtener el total
				 * de las comisiones
				 * por los pagos de las primeras aportaciones
				 */
				$rowAportaciones = $query 	->table("contratos AS con")->select("con.id, con.primerAnticipo AS anticipo, con.folio AS folio, con.idNomina,
																				CONCAT(cli.nombres, ' ', cli.apellidop, ' ', cli.apellidom) AS nombreCliente")
											->leftJoin("clientes AS cli", "con.idTitular", "=", "cli.id")
											->where("fechaCreacion", "BETWEEN", "'$fechaInicio' AND '$fechaFin'", "ss")->and()
											->where("con.idNomina", "=", 0, "i")->and()
											->where("idVendedor", "=", $idUsuario, "i")->execute();
				$totalAportaciones = 0;
				$idConcepto = 1;
				$cantidad = 1;
				foreach ($rowAportaciones as $rowAportacion)
				{
					$contrato 				= new contrato($rowAportacion['id'], $mysqli);
					$comision_vendedor 		= $contrato->comision_vendedor();
					$total_pagado_vendedor 	= $contrato->total_pagado_vendedor($query);
					$resta_comision 		= $comision_vendedor - $total_pagado_vendedor;
					// if ($contrato->id == 12) {
					// 	var_dump($rowAportacion);
					// }
					// echo $contrato->id." resta comision: ". $resta_comision."Tot pagado a vendedor: ".$total_pagado_vendedor."comision_vendedor():".$contrato->comision_vendedor()."<br>";
					$nombreConcepto 		= "- 1° Aport. ".$rowAportacion['nombreCliente']." (".$rowAportacion['folio'].")";
					$monto 					= $rowAportacion['anticipo'] > $resta_comision ? $resta_comision :  $rowAportacion['anticipo'];

					/**
					 * Insert detalle_nomina de los pagos por
					 * primers aportaciones
					 */
					if ($monto > 0 && !$rowAportacion['idNomina'])
					{
						$query->table("detalle_nomina")->insert(compact("idNomina", "idConcepto", "nombreConcepto",
																		"cantidad", "monto", "tipo", "idUsuario", "idSucursal"), "iisidiii")->execute();
						$query ->table("contratos")->update(compact("idNomina"),"i") ->where("id", "=", $rowAportacion['id'], "i")->limit(1) ->execute();
						// echo $query->lastStatement();
						$totalAportaciones += $monto;
					}
				}

				/**
				 * Obtener el total
				 * de las comisiones
				 * por los pagos de los contratos
				 */
				$rowComisionesVentas=$query	->table("detalle_pagos_contratos AS dpc")
											->select( "dpc.monto AS monto, dpc.idNominaVenta, dpc.id AS id_dpc, con.folio AS folio,
													   dpc.tasaComisionCobranza AS tasaComisionCobranza,
													   con.id AS idContrato,
													   CONCAT(cli.nombres, ' ', cli.apellidop, ' ', cli.apellidom) AS nombreCliente")
											->innerJoin("contratos AS con", "dpc.idContrato", "=", "con.id")
											->leftJoin("clientes AS cli", "con.idTitular", "=", "cli.id")
											->where("dpc.fechaCreacion", "BETWEEN", "'$fechaInicio' AND '$fechaFin'", "ss")->and()
											->where("con.idVendedor", "=", $nomina['idUsuario'], "i")->and()
											->where("dpc.activo", "=", 1, "i")->execute();

				$totalComisionVentas = 0;
				$cantidad = 1;
				$idConcepto = 1;
				foreach ($rowComisionesVentas as $rowCom_venta)
				{
					if (!$rowCom_venta['idNominaVenta'])
					{
						$contrato 				= new contrato($rowCom_venta['idContrato'], $query);
						$montoPago 				= $rowCom_venta['monto'];
						$tasaCom_Cobranza 		= $rowCom_venta['tasaComisionCobranza'];
						$tasa_100 				= $tasaCom_Cobranza / 100;
				        $monto_pago_cobrador 	= $montoPago * $tasa_100;
						$monto_pago_vendedor 	= $montoPago - $monto_pago_cobrador;
						$totalAbonado 			= $contrato ->totalAbonado($mysqli);
						$comision_vendedor 		= $contrato->comision_vendedor();
						$total_pagado_vendedor 	= $contrato->total_pagado_vendedor($query);
						$primerAportacion		= $contrato->anticipo;
						$resta_comision 		= $comision_vendedor - $total_pagado_vendedor;
						if ($resta_comision > 0)
							$monto_pago_vendedor_real = $monto_pago_vendedor < $resta_comision ? $monto_pago_vendedor : $resta_comision;
						else
							$monto_pago_vendedor_real = 0;

						echo "idContrato: ".$contrato->id."idNomina".$contrato->idNomina." Anticipo".$contrato->anticipo."<br>".
						" total_pagado_vendedor: ".$total_pagado_vendedor." resta_comision: ".$resta_comision."<br>".
						" monto_pago_cobrador: ".$monto_pago_cobrador." monto_pago_vendedor_real: ".$monto_pago_vendedor_real."<br>".
						" omision vendedor: ".$comision_vendedor." totalAbonado: ".$totalAbonado." primerAportacion: ".$primerAportacion."<br>".
						" idContrato: ". $contrato ->id."<br>-----------------------------------------<br>";

						$totalComisionVentas += $monto = $monto_pago_vendedor_real;

						$nombreConcepto = "- Contrato. ".$rowCom_venta['nombreCliente']." (".$rowCom_venta['folio'].")";

						/**
						 * Insert detalle_nomina de los pagos de
						 * los contratos
						 */
						if ($monto > 0)
						{
							$query->table("detalle_nomina")->insert(compact("idNomina", "idConcepto", "nombreConcepto",
																			"cantidad", "monto", "tipo", "idUsuario", "idSucursal"), "iisidiii")->execute();
							$query->table("detalle_pagos_contratos")->update(compact("idNominaVenta"), "i") ->where("id", "=", $rowCom_venta['id_dpc'], "i")->limit(1) ->execute();
						}
					}
				}

				/**
				 * Obtener el total
				 * de las comisiones
				 * por la cobranza
				 */
				$rowComisionesCobranza=$query->table("detalle_pagos_contratos AS dpc")
											->select("dpc.monto AS monto, dpc.idNominaCobranza, dpc.id AS id_dpc, con.folio AS folio,
													  dpc.tasaComisionCobranza AS tasaComisionCobranza,
													  CONCAT(cli.nombres, ' ', cli.apellidop, ' ', cli.apellidom) AS nombreCliente")
											->leftJoin("contratos AS con", "dpc.idContrato", "=", "con.id")
											->leftJoin("clientes AS cli", "con.idTitular", "=", "cli.id")
											->where("dpc.usuario_cobro", "=", $nomina['idUsuario'], "i")->and()
											->where("dpc.activo", "=", 1, "i")->and()
											->where("dpc.fechaCreacion", "BETWEEN", "'$fechaInicio' AND '$fechaFin'", "ss")->execute();
				// echo "<br>".$query->lastStatement();
				$totalComisionCobranza = 0;
				$idConcepto = 2;
				$cantidad = 1;
				// var_dump($rowComisionesCobranza);
				foreach ($rowComisionesCobranza as $rowCom_cobranza)
				{
					// var_dump($rowCom_cobranza['idNominaCobranza']);
					if ($rowCom_cobranza['idNominaCobranza'] == 0)
					{
						$montoPago 				= $rowCom_cobranza['monto'];
						$tasaCom_Cobranza 		= $rowCom_cobranza['tasaComisionCobranza'];
						$tasa_100 				= $tasaCom_Cobranza / 100;
				        $monto_pago_cobrador 	= $montoPago * $tasa_100;
						$totalComisionCobranza	+= $monto = $monto_pago_cobrador;

						$nombreConcepto = "- Cobranza. ".$rowCom_cobranza['nombreCliente']." (".$rowCom_cobranza['folio'].")";
						// echo "detalle_pagos_contrato id: ".$rowCom_venta['id_dpc']." idNominaCobranza': ".$rowCom_cobranza['idNominaCobranza'];
						/**
						 * Insert detalle_nomina de los pagos de
						 * la cobranza diaria
						 */
						 if ($monto > 0)
						 {
							$query->table("detalle_nomina")->insert(compact("idNomina", "idConcepto", "nombreConcepto",
																			"cantidad", "monto", "tipo", "idUsuario", "idSucursal"), "iisidiii")->execute();
							$query->table("detalle_pagos_contratos")->update(compact("idNominaCobranza"), "i") ->where("id", "=", $rowCom_cobranza['id_dpc'], "i")->limit(1) ->execute();
						}
					}
				}

                $InfoData[] = array(
					'idNomina'				=> $idNomina,
					'idUsuario'				=> $nomina['idUsuario'],
					'idSucursal'			=> $idSucursal,
                    'nombres'				=> $nomina['nombres'],
                    'aportaciones'			=> "$".number_format($totalAportaciones,2,".",","),
					'comisionVentas'		=> "$".number_format($totalComisionVentas,2,".",","),
					'comisionCobranza'		=> "$".number_format($totalComisionCobranza,2,".",",")
				);
            }
			usleep(2000000);
			if ($query ->commit())
			{
				$json_data["Result"] = "OK";
	            $json_data["Records"] = $InfoData;
			}
			else {
				$json_data["Result"] = "ERROR";
			}
        }
        echo json_encode($json_data);
    }
?>

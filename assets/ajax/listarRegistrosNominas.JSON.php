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
		// $fechaInicio = $_POST['fechaInicio'];
		$fechaInicio = $_GET['fechaInicio'];
        $fInicio_e = explode('-',$fechaInicio);
        $Y_ini = intval($fInicio_e[0]);
        $m_ini = intval($fInicio_e[1]);
        $d_ini = intval($fInicio_e[2]);
        // var_dump($_GET);
		// $fechaFin = $_POST['fechaFin'];
        $fechaFin = $_GET['fechaFin'];
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
								->where("activo", "=", 1, "i")->and()->where("id", "<>", 1, "i")->execute();

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
            foreach ($totalNominas as $nomina)
			{
				$idUsuario = $nomina['idUsuario'];
				$query ->table("cat_nominas")->insert(compact("idPeriodo", "idUsuario"), "ii")->execute();
				$idNomina = $query->insert_id();
				/**
				 * Obtener el total
				 * de las comisiones
				 * por los pagos de las primeras aportaciones
				 */
				$rowAportaciones = $query 	->table("contratos AS con")->select("con.primerAnticipo AS anticipo, con.folio AS folio,
																				CONCAT(cli.nombres, ' ', cli.apellidop, ' ', cli.apellidom) AS nombreCliente")
											->leftJoin("clientes AS cli", "con.idTitular", "=", "cli.id")
											->where("fechaCreacion", "BETWEEN", "'$fechaInicio' AND '$fechaFin'", "ss")->and()
											->where("idVendedor", "=", $idUsuario, "i")->execute();
				$totalAportaciones = 0;
				$idConcepto = 1;
				foreach ($rowAportaciones as $rowAportacion)
				{
					$nombreConcepto = "- 1° Aport. ".$rowAportacion['nombreCliente']." (".$rowAportacion['folio'].")";
					$cantidad = 1;
					$monto = $rowAportacion['anticipo'];
					$query->table("detalle_nomina")->insert(compact("idNomina", "idConcepto", "nombreConcepto",
																	"cantidad", "monto", "idUsuario", "idSucursal"), "iisidii")->execute();
					$totalAportaciones += $rowAportacion['anticipo'];
				}

				/**
				 * Obtener el total
				 * de las comisiones
				 * por los pagos de los contratos
				 */
				$rowComisionesVentas=$query	->table("detalle_pagos_contratos AS dpc")
											->select( "dpc.monto AS monto,
													   dpc.tasaComisionCobranza AS tasaComisionCobranza,
													   con.id AS idContrato")
											->innerJoin("contratos AS con", "dpc.idContrato", "=", "con.id")
											->where("dpc.fechaCreacion", "BETWEEN", "'$fechaInicio' AND '$fechaFin'", "ss")->and()
											->where("con.idVendedor", "=", $nomina['idUsuario'], "i")->and()
											->where("dpc.activo", "=", 1, "i")->execute();

				$totalComisionVentas = 0;
				foreach ($rowComisionesVentas as $rowCom_venta)
				{
					$contrato 				= new contrato($rowCom_venta['idContrato'], $mysqli);
					$montoPago 				= $rowCom_venta['monto'];
					$tasaCom_Cobranza 		= $rowCom_venta['tasaComisionCobranza'];
					$tasa_100 				= $tasaCom_Cobranza / 100;
			        $monto_pago_cobrador 	= $montoPago * $tasa_100;
					$monto_pago_vendedor 	= $montoPago - $monto_pago_cobrador;
					$totalAbonado 			= $contrato ->totalAbonado($mysqli);
					$comision_vendedor 		= $contrato->comision_vendedor();
					$resta_comision 		= $comision_vendedor - $totalAbonado;
					if ($resta_comision > 0)
						$monto_pago_vendedor_real = $monto_pago_vendedor < $resta_comision ? $monto_pago_vendedor : $resta_comision;
					else
						$monto_pago_vendedor_real = 0;

					$totalComisionVentas += $monto_pago_vendedor_real;
				}

				/**
				 * Obtener el total
				 * de las comisiones
				 * por la cobranza
				 */
				$rowComisionesCobranza=$query->table("detalle_pagos_contratos AS dpc")
											->select("dpc.monto AS monto,
													  dpc.tasaComisionCobranza AS tasaComisionCobranza")
											->where("dpc.usuario_cobro", "=", $nomina['idUsuario'], "i")->and()
											->where("dpc.activo", "=", 1, "i")->and()
											->where("dpc.fechaCreacion", "BETWEEN", "'$fechaInicio' AND '$fechaFin'", "ss")->execute();
				$totalComisionCobranza = 0;
				foreach ($rowComisionesCobranza as $rowCom_cobranza)
				{
					$montoPago 				= $rowCom_cobranza['monto'];
					$tasaCom_Cobranza 		= $rowCom_cobranza['tasaComisionCobranza'];
					$tasa_100 				= $tasaCom_Cobranza / 100;
			        $monto_pago_cobrador 	= $montoPago * $tasa_100;
					$totalComisionCobranza	+= $monto_pago_cobrador;
				}
                $InfoData[] = array(
					'idUsuario'				=> $nomina['idUsuario'],
                    'nombres'				=> $nomina['nombres'],
                    'aportaciones'			=> "$".number_format($totalAportaciones[0]['suma'],2,".",","),
					'comisionVentas'		=> "$".number_format($totalComisionVentas,2,".",","),
					'comisionCobranza'		=> "$".number_format($totalComisionCobranza,2,".",",")
				);
            }
			$query ->commit();
			$json_data["Result"] = "OK";
            $json_data["Records"] = $InfoData;
        }
        echo json_encode($json_data);
    }
?>

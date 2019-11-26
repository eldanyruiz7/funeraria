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
		$fInicio = $_POST['fechaInicio'];
		// $fInicio = $_GET['fechaInicio'];
        $fInicio_e = explode('-',$fInicio);
        $Y_ini = intval($fInicio_e[0]);
        $m_ini = intval($fInicio_e[1]);
        $d_ini = intval($fInicio_e[2]);
        // var_dump($_GET);
		$fFin = $_POST['fechaFin'];
        // $fFin = $_GET['fechaFin'];
        $fFin_e = explode('-',$fFin);
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
        $fInicio       .= " 00:00:00";
        $fFin          .= " 23:59:59";
		$response = array(
			"status"        => 1
		);
		require_once "../php/contrato.class.php";
        require "../php/responderJSON.php";
        require_once "../php/funcionesVarias.php";
		require_once "../php/query.class.php";
		$query 		= new Query();
		/**
		 * Obtener totales por primeras aportaciones,
		 * por rango de fechas
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
            foreach ($totalNominas as $nomina)
			{
				$totalAportaciones = $query ->table("contratos")->select("IFNULL(SUM(primerAnticipo),0) AS suma")
											->where("fechaCreacion", "BETWEEN", "'$fInicio' AND '$fFin'", "ss")->and()
											->where("idVendedor", "=", $nomina['idUsuario'], "i")->and()
											->where("activo", "=", 1, "i")->execute();
				$rowComisionesVentas=$query	->table("detalle_pagos_contratos AS dpc")
											->select( "dpc.monto AS monto,
													   dpc.tasaComisionCobranza AS tasaComisionCobranza,
													   con.id AS idContrato")
											->innerJoin("contratos AS con", "dpc.idContrato", "=", "con.id")
											->innerJoin("folios_cobranza_asignados AS fca", "dpc.idFolio_cobranza", "=", "fca.id")
											->where("dpc.fechaCreacion", "BETWEEN", "'$fInicio' AND '$fFin'", "ss")->and()
											->where("fca.idUsuario_asignado", "=", $nomina['idUsuario'], "i")->and()
											->where("dpc.activo", "=", 1, "i")->execute();
				// print_r($rowComisionesVentas);
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
					// echo "<br>Total abonado".$totalAbonado;
					// echo "<br>Total comisión vendedor".$comision_vendedor;
					$resta_comision 		= $comision_vendedor - $totalAbonado;
					if ($resta_comision > 0)
					{
						$monto_pago_vendedor_real = $monto_pago_vendedor < $resta_comision ? $monto_pago_vendedor : $resta_comision;
					}
					else
					{
						$monto_pago_vendedor_real = 0;
					}
					$totalComisionVentas += $monto_pago_vendedor_real;
				}

                $InfoData[] = array(
					'idUsuario'         => $nomina['idUsuario'],
                    'nombres'           => $nomina['nombres'],
                    'aportaciones'      => $totalAportaciones[0]['suma'],
					'comisionVentas'	=> $totalComisionVentas);
            }
			$json_data["Result"] = "OK";
            $json_data["Records"] = $InfoData;
        }
        echo json_encode($json_data);
    }
?>

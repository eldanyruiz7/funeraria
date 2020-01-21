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
		$response = array(
			"status"        => 1
		);
        require "../php/responderJSON.php";
		require_once "../php/query.class.php";
		$query 		= new Query();
		$idNomina = (int)$_GET['idNomina'];
		$rowsConceptos = $query->table("detalle_nomina AS dn")->select("id, cantidad, nombreConcepto AS concepto, monto, tipo, idUsuario, idSucursal")
								->where("activo", "=", 1, "i")->and()->where("idNomina", "=", $idNomina, "i")->orderBy("idConcepto")->execute();
								// echo $query->lastStatement();
		$num = $query->num_rows();
		if ($num == 0)
		{
			$json_data["Records"] = 0;
		}
		else
		{

			foreach ($rowsConceptos as $concepto)
			{
				// var_dump($concepto);
				$InfoData[] = array(
					'idDetalle'				=> $concepto['id'],
					'idNominaDetalle'		=> $idNomina,
					'idUsuario'				=> $concepto['idUsuario'],
					'idSucursal'			=> $concepto['idSucursal'],
					'cantidad'				=> $concepto['cantidad'],
					'concepto'				=> $concepto['concepto'],
					'monto'					=> "$".number_format($concepto['monto'],2,".",","),
					'tipo'					=> $concepto['tipo']
				);
			}
			$json_data["Records"] = $InfoData;
		}
		$json_data["Result"] = "OK";
		usleep(500000);
        echo json_encode($json_data);
    }
?>

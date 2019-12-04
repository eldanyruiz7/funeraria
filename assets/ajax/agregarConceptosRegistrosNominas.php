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
		// $response = array(
		// 	"status"        => 1
		// );
        // require "../php/responderJSON.php";
		// require_once "../php/query.class.php";
		// $query 		= new Query();
		// $idNomina = (int)$_GET['idNomina'];
		// $rowsConceptos = $query	->table("detalle_nomina AS dn")->select("dn.cantidad, dn.nombreConcepto AS concepto, dn.monto, dn.tipo AS tipo")
		// 						->innerJoin("tipos_detalle_nomina AS tdn", "dn.tipo", "=", "tdn.id")
		// 						->where("dn.activo", "=", 1, "i")->and()->where("dn.idNomina", "=", $idNomina, "i")->orderBy("dn.idConcepto")->execute();
		// $num = $query->num_rows();
		// if ($num == 0)
		// {
		// 	$json_data["Records"] = 0;
		// }
		// else
		// {
		//
		// 	foreach ($rowsConceptos as $concepto)
		// 	{
		// 		$InfoData[] = array(
		// 			'idNomina'				=> $idNomina,
		// 			'cantidad'				=> $concepto['cantidad'],
		// 			'concepto'				=> $concepto['concepto'],
		// 			'monto'					=> "$".number_format($concepto['monto'],2,".",","),
		// 			'tipo'					=> $concepto['tipo']
		// 		);
		// 	}
		// 	$json_data["Records"] = $InfoData;
		// }
		// $json_data["Result"] = "OK";
		// usleep(500000);
        // echo json_encode($json_data);
        $json_data["Result"] = "OK";
		$InfoData[] = array(
					'idNomina'				=> 5,
					'cantidad'				=> 1,
					'concepto'				=> "ASDFER",
					'monto'					=> "$33",
					'tipo'					=> 1
				);
		// usleep(500000);
        echo json_encode($json_data);
    }
?>

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
		require_once "../php/funcionesVarias.php";
		function error($mensaje)
		{
			$json_data["Message"] = $mensaje;
			$json_data["Result"] = "ERROR";
			echo json_encode($json_data);
			die;
		}
		if (!$nombreConcepto = validarFormulario('s',$_POST['concepto'],0))
			error("El campo concepto no puede estar en blanco");

		if (!$monto = validarFormulario('i',$_POST['monto'],0))
			error("El campo monto no puede estar en blanco ni menor o igual que cero");

		if (!$tipo = validarFormulario('i',$_POST['tipo'],0))
			error("El formato del campo tipo de concepto no puede estar en blanco");

		if (!$idNomina = validarFormulario('i',$_POST['idNominaDetalle'],0))
			error("El formato del campo idNomina no es el correcto");

		if (!$idUsuario = validarFormulario('i',$_POST['idUsuario'],0))
			error("El formato del campo idUsuario no es el correcto");

		if (!$idSucursal = validarFormulario('i',$_POST['idSucursal'],0))
			error("El formato del campo idSucursal no es el correcto");

		// if (!$id = validarFormulario('i',$_POST['idDetalle'],0))
		// 	error("El formato del campo idDetalle no es el correcto");

		require_once "../php/query.class.php";
		$query 		= new Query();
		$idConcepto = 3;
		$cantidad = 1;
		$query->table("detalle_nomina")->insert(compact("idNomina", "idConcepto", "tipo", "nombreConcepto",
														"cantidad", "monto", "idUsuario", "idSucursal"), "iiisidii")->execute();

        if ($query->status())
		{
			$insert_id = $query->insert_id();
			$row = $query ->table("detalle_nomina")->select("*")->where("id", "=", $insert_id, "i")->limit(1)->execute();
			$json_data["Result"] = "OK";
			$json_data["Record"] = array(
				'idDetalle'				=> $row[0]['id'],
				'idNominaDetalle'		=> $row[0]['idNomina'],
				'idUsuario'				=> $row[0]['idUsuario'],
				'idSucursal'			=> $row[0]['idSucursal'],
				'cantidad'				=> $row[0]['cantidad'],
				'concepto'				=> $row[0]['nombreConcepto'],
				'monto'					=> "$".number_format($row[0]['monto'],2,".",","),
				'tipo'					=> $row[0]['tipo']
			);
        }
		else
		{
			error("Ocurrió un error al intentar guardar el registro, vuelve a intentarlo nuevamente");
		}

        echo json_encode($json_data);
    }
?>

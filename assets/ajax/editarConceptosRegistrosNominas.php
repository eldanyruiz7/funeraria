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
		var_dump($_POST['idDetalle']);
		if (!$id = validarFormulario('i',$_POST['idDetalle'],0))
			error("El formato del campo idDetalle no es el correcto");

		$query ->table("detalle_nomina")->select("*")->where("id", "=", $id, "i")->and()->where("idConcepto", "=", 3, "i")->limit(1)->execute();
		if ($query->num_rows() == 0)
		{
			error("Este concepto no se puede editar");
		}
		if (!$nombreConcepto = validarFormulario('s',$_POST['concepto'],0))
			error("El campo concepto no puede estar en blanco");

		if (!$monto = validarFormulario('i',$_POST['monto'],0))
			error("El campo monto no puede estar en blanco ni menor o igual que cero");

		if (!$tipo = validarFormulario('i',$_POST['tipo'],0))
			error("El formato del campo tipo de concepto no puede estar en blanco");

		require_once "../php/query.class.php";
		$query 		= new Query();
		$idConcepto = 3;
		$cantidad = 1;
		$query->table("detalle_nomina")->update(compact("tipo", "nombreConcepto", "monto"), "isd")->execute();

        if ($query->status())
		{
			$insert_id = $query->insert_id();
			$row = $query ->table("detalle_nomina")->select("*")->where("id", "=", $insert_id, "i")->limit(1)->execute();
			$json_data["Result"] = "OK";
			$json_data["Record"] = array(
				'idDetalle'				=> $row[0]['id'],
				'idNomina'				=> $row[0]['idNomina'],
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

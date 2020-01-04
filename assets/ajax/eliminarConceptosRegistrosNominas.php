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
		require_once "../php/query.class.php";
		$query 		= new Query();
		function error($mensaje)
		{
			$json_data["Message"] = $mensaje;
			$json_data["Result"] = "ERROR";
			echo json_encode($json_data);
			die;
		}
		// var_dump($_POST);
		// die;
		if (!$id = validarFormulario('i',$_POST['idDetalle'],0))
			error("El formato del campo idDetalle no es el correcto");

		$row = $query ->table("detalle_nomina")->select("idConcepto")->where("id", "=", $id, "i")->and()->where("activo", "=", 1, "i")->limit(1)->execute();
		// var_dump($_POST['idDetalle']);
		// var_dump($row[0]['idConcepto']);
		if ($query->num_rows() == 1 && $row[0]['idConcepto'] != 3)
		{
			error("Este concepto no se puede eliminar");
		}
		$activo = 0;
		$query->table("detalle_nomina")->update(compact("activo"), "i")->where("id", "=", $id, "i")->limit(1)->execute();

        if ($query->status())
		{
			$json_data["Result"] = "OK";
        }
		else
		{
			error("Ocurrió un error al intentar eliminar el registro, vuelve a intentarlo nuevamente");
		}

        echo json_encode($json_data);
    }
?>

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
		require_once "../php/contrato.class.php";
        require_once "../php/responderJSON.php";
        require_once "../php/funcionesVarias.php";
		require_once "../php/query.class.php";
		$query 		= new Query();
		$idPeriodo 	= $_POST['idPeriodo'];
		$resPeriodo = $query->table("cat_periodos_nominas")->select()->where("id", "=", $idPeriodo, "i")->and()
							->where("activo", "=", 1, "i")->limit()->execute(FALSE, OBJ);
		if (!$query->num_rows())
		{
			$json_data["Result"] = "ERROR";
			$json_data["Message"] = 'Este periodo no existe, posiblemente ya ha sido eliminado';
			echo json_encode($json_data);
			die;
		}
		$resNominas = $query->table("cat_nominas AS cn")->select("cn.id, cn.idUsuario, CONCAT(usr.nombres, ' ', usr.apellidop, ' ', usr.apellidom) AS nombreUsuario")
							->innerJoin("cat_usuarios AS usr", "cn.idUsuario", "=", "usr.id")
							->where("cn.idPeriodo", "=", $resPeriodo->id, "i")->and()
							->where("cn.activo", "=", 1, "i")->execute(FALSE, RETURN_OBJECT);

		foreach ($resNominas as $Nomina)
		{
			$InfoData[] = array(
				'idNomina'				=> $Nomina->id,
				'idUsuario'				=> $Nomina->idUsuario,
				'idSucursal'			=> $resPeriodo->idSucursal,
			    'nombres'				=> $Nomina->nombreUsuario,
			    'aportaciones'			=> "$0",
				'comisionVentas'		=> "$0",
				'comisionCobranza'		=> "$0"
			);
		}
		usleep(2000000);
		$json_data["Result"] = "OK";
        $json_data["Records"] = $InfoData;
        echo json_encode($json_data);
    }
?>

<?php
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
	require ("../php/usuario.class.php");
	require ("../php/query.class.php");
	$usuario = new usuario($idUsuario,$mysqli);
	$permiso = $usuario->permiso("listarBitacora",$mysqli);
	if (!$permiso)
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
	$fInicio = $_GET['fechaInicio'];
	$fInicio_e = explode('-',$fInicio);
	$Y_ini = intval($fInicio_e[0]);
	$m_ini = intval($fInicio_e[1]);
	$d_ini = intval($fInicio_e[2]);
	// var_dump($_GET);
	$fFin = $_GET['fechaFin'];
	$fFin_e = explode('-',$fFin);
	$Y_fin = intval($fFin_e[0]);
	$m_fin = intval($fFin_e[1]);
	$d_fin = intval($fFin_e[2]);
	// var_dump($fInicio_e);
	if(checkdate($m_ini,$d_ini,$Y_ini) == FALSE || checkdate($m_fin,$d_fin,$Y_fin) == FALSE)
	{
		// $InfoData = [
		//     "data"   => 0
		// ];
		$json_data = [
			"data"   => 0
		];
		echo json_encode($json_data);
		die;
	}
	$fInicio       .= " 00:00:00";
	$fFin          .= " 23:59:59";
	$query = new Query();
	$resBit = $query->table("bitacora_eventos AS b") ->select(" b.id, b.fecha, b.ip, b.pantalla, b.descripcion,
																CONCAT(usr.nombres, ' ', usr.apellidop, ' ', usr.apellidom) AS usuario,
																CONCAT(suc.nombre, ' ', suc.direccion2) AS sucursal")
					->leftJoin("cat_usuarios AS usr", "usr.id", "=", "b.idUsuario")
					->leftJoin("cat_sucursales AS suc", "suc.id", "=", "b.idSucursal")
					->where("b.fecha", "BETWEEN","'$fInicio' AND '$fFin'", "ss")->execute(FALSE, RETURN_OBJECT);

	if ($query->num_rows() == 0)
	{
		$json_data = [
			"data"   => 0
		];
	}
	else
	{
		foreach ($resBit as $bit)
		{

			$InfoData[] = array(
				'id'				=> str_pad($bit->id, 7, "0", STR_PAD_LEFT),
				'usuario'			=> $bit->usuario,
				'fecha'				=> date_format(date_create($bit->fecha), 'd-m-Y/H:i:s'),
				'ip'				=> $bit->ip,
				'pantalla'			=> $bit->pantalla,
				'descripcion'		=> $bit->descripcion,
				'sucursal'			=> $bit->sucursal
				);
			}
			//$data[] = $InfoData;
			$json_data = [
				"data"   => $InfoData
			];
		}
		echo json_encode($json_data);
	}
?>

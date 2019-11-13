<?php
require_once ('../connect/bd.php');
require_once ("../connect/sesion.class.php");
$sesion = new sesion();
require_once ("../connect/cerrarOtrasSesiones.php");
require_once ("../connect/usuarioLogeado.php");
require_once ("../php/funcionesVarias.php");
if( logueado($idSesion,$idUsuario,$mysqli) == false || $idSesion == false)
{
	header("Location: salir.php");
}
else
{
	require "../php/responderJSON.php";
	require ("../php/usuario.class.php");
	require ("../php/query.class.php");
	$usuario 	= new usuario($idUsuario,$mysqli);
	$query 		= new Query();
	$permiso 	= $usuario->permiso("agregarDifunto",$mysqli);
	if (!$permiso)
	{
		$response['mensaje'] = "No se pudo completar la información. Usuario con permisos insuficientes para realizar esta acción";
		$response['status'] = 0;
		responder($response, $mysqli);
	}
	$nombre                        	= $_POST['nombre'];
	$response 	= array(
		"status"                    => 1
	);
	$idUsuario      				= $sesion->get('id');
	$resultSuc = $query ->table('cat_usuarios')->select("idSucursal")
						->where("id", "=", $idUsuario, "i")->limit(1)
						->execute();

	$idSucursal     				= $resultSuc[0]['idSucursal'];
	if (!$nombre = validarFormulario('s',$nombre,0))
	{
		$response['mensaje'] 		= "El campo Nombre no cumple con el formato esperado y no puede estar en blanco";
		$response['status'] 		= 0;
		$response['focus'] 			= 'inputNuevaCausaDeceso';
		responder($response, $mysqli);
	}
	$query ->autocommit(FALSE);
	$resultDec = $query ->table('cat_causasdecesos')->select("nombre")
						->where("nombre", "=", $nombre, "s")->and()->where("activo", "=", 1, "i")
						->limit(1)->execute();

	if ($query 	->status())
	{
		if ($query->num_rows())
		{
			$response['mensaje'] 	= "No se puede agregar esta causa de deceso. Ya existe registrada una con el mismo nombre. Elije otra distinta";
			$response['status'] 	= 0;
			responder($response, $mysqli);
		}
		else
		{
			$query 	->table("cat_causasdecesos")
					->insert(array("nombre" => $nombre, "usuario" => $idUsuario), "si")->execute();

			if ($query ->status() && $query ->affected_rows() && $query ->commit())
			{
				// Agregar evento en la bitácora de eventos
				$idUsuario 			= $sesion->get("id");
				$ipUsuario 			= $sesion->get("ip");
				$pantalla			= "Agregar/Modificar difunto";
				$descripcion		= "Se agregó una nueva causa de deceso ($nombre) al catálogo de causas de decesos";
				$sql				= "CALL agregarEvento($idUsuario, '$ipUsuario', '$pantalla', '$descripcion', $idSucursal);";
				$mysqli				->query($sql);
				$response['mensaje']= "Nueva causa de deceso generada correctamente.";
				$response['status']	= 1;
				responder($response, $mysqli);
			}
			else
			{
				$query ->rollback();
				$response['mensaje']= $query ->mensaje();
				$response['status']	= 0;
				responder($response, $mysqli);
			}
		}
	}
	else
	{
		$query ->rollback();
		$response['mensaje'] 		= $query ->mensaje();
		$response['status'] 		= 0;
		responder($response, $mysqli);
	}
}

<?php
require_once ('../connect/bd.php');
require_once ("../connect/sesion.class.php");
$sesion = new sesion();
require_once ("../connect/cerrarOtrasSesiones.php");
require_once ("../connect/usuarioLogeado.php");
require_once ("../php/funcionesVarias.php");
if( logueado($idSesion,$idUsuario,$mysqli) == false || $idSesion == false)
{
	header("Location: ".dirname(__FILE__)."../../salir.php");
}
else
{
	require "../php/responderJSON.php";
	require ("../php/usuario.class.php");
	require ("../php/query.class.php");
	$usuario 	= new usuario($idUsuario,$mysqli);
	$query		= new Query();
	$response 	= array(
		"status"    => 0 );
	$permiso = $usuario->permiso("agregarCliente",$mysqli);
	if (!$permiso)
	{
		$response['mensaje'] = "No se pudo guardar este registro. Usuario con permisos insuficientes para realizar esta acción";
		responder($response, $mysqli);
	}

	if (!$nombres = validarFormulario('s', $_POST['nombres'], 0))
	{
		$response['mensaje'] = "El campo Nombre no cumple con el formato esperado y no puede estar en blanco";
		$response['focus'] = 'nombres';
		responder($response, $mysqli);
	}
	if (!$apellidop = validarFormulario('s', $_POST['apellidop'], 0))
	{
		$response['mensaje'] = "El campo Apellido paterno no puede estar en blanco";
		$response['focus'] = 'apellidop';
		responder($response, $mysqli);
	}
	$apellidom = validarFormulario('s', $_POST['apellidom'], FALSE);
	if (!$domicilio1 = validarFormulario('s', $_POST['domicilio1'], 0))
	{
		$response['mensaje'] = "El campo domicilio no puede estar en blanco";
		$response['focus'] = 'domicilio1';
		responder($response, $mysqli);
	}
	if (!$domicilio2 = validarFormulario('s', $_POST['domicilio2'], 0))
	{
		$response['mensaje'] = "El campo domicilio no puede estar en blanco";
		$response['focus'] = 'domicilio2';
		responder($response, $mysqli);
	}
	if (!$cp = validarFormulario('s', $_POST['cp'], 0))
	{
		$response['mensaje'] = "El campo código postal no puede estar en blanco";
		$response['focus'] = 'cp';
		responder($response, $mysqli);
	}
	if (!$idEstado = validarFormulario('i', $_POST['estado']))
	{
		$response['mensaje'] = "El formato del campo estado no es el correcto";
		$response['focus'] = 'estado';
		responder($response, $mysqli);
	}
	$rfc = validarFormulario('s', $_POST['rfc']);
	if (!$fechaNac = validarFormulario('d', $_POST['fechaNac']))
	{
		$response['mensaje'] = "Elige una fecha válida. El formato de la fecha no es el correcto.";
		$response['focus'] = 'fechaNac';
		responder($response, $mysqli);
	}
	$tel	 		= validarFormulario('s', $_POST['telefono'], FALSE);
	$cel	 		= validarFormulario('s', $_POST['celular'], FALSE);
	$email 			= validarFormulario('s', $_POST['email'], FALSE);

	$idUsuario      = $sesion->get('id');
	$usuario		= $idUsuario;
	$rowIdSucursal = $query ->table("cat_usuarios")	->select("idSucursal")
													->where("id", "=", $idUsuario, "i")
													->limit(1)->execute();
	$idSucursal = $rowIdSucursal[0]["idSucursal"];

	if (strlen($rfc) > 0)
	{
		$query 	->table("clientes")->select("id")
				->where("rfc", "=", $rfc, "s")->and()
				->where("idSucursal", "=", $idSucursal, "i")->execute();

		if ($query 	->status() && $query->num_rows())
		{
			$response['mensaje']= "No se puede guardar este nuevo registro porque ya existe un cliente en esta sucursal con el mismo RFC";
			$response['focus'] 	= 'rfc';
			responder($response, $mysqli);
		}
	}
	$query->table("clientes")->insert(compact( 	"nombres", "apellidop", "apellidom", "domicilio1", "domicilio2", "cp",
												"idEstado", "rfc", "fechaNac", "tel", "cel", "email", "idSucursal",
												 "usuario"), 'ssssssisssssii') ->execute();
	if ($query ->status())
	{
		if($query ->affected_rows() == 0)
		{
			$response['mensaje']        = "No se guardó nada, no se pudo guardar el registro, inténtalo nuevamente";
			responder($response, $mysqli);
		}
		// Agregar evento en la bitácora de eventos ///////
		$insert_id				= $mysqli->insert_id;
		$idUsuario 				= $sesion->get("id");
		$ipUsuario 				= $sesion->get("ip");
		$nombreClienteInsertado	= "$nombres $apellidop $apellidom";
		$pantalla				= "Agregar cliente";
		$descripcion			= "Se agregó un nuevo cliente ($nombreClienteInsertado) con id=$insert_id al catálogo de clientes";
		$sql					= "CALL agregarEvento($idUsuario, '$ipUsuario', '$pantalla', '$descripcion', $idSucursal);";
		$mysqli					->query($sql);
		//////////////////////////////////////////////////
		$response['mensaje']	= "$nombreClienteInsertado";
		$response['status']		= 1;
		responder($response, $mysqli);
	}
	else
	{
		$response['mensaje']	= "Error. No se pudo modificar. Falló la preparación de los datos. Vuelve a intentarlo nuevamente";
		responder($response, $mysqli);
	}
}

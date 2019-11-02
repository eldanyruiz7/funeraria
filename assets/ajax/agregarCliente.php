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
	$query		= new Query();
	$response 	= array(
		"status"    => 0 );
		$permiso = $usuario->permiso("agregarCliente",$mysqli);
		if (!$permiso)
		{
			$response['mensaje'] = "No se pudo guardar este registro. Usuario con permisos insuficientes para realizar esta acción";
			responder($response, $mysqli);
		}
		$nombres                        = $_POST['nombres'];
		$apellidop                      = $_POST['apellidop'];
		$apellidom                      = $_POST['apellidom'];
		$domicilio1                     = $_POST['domicilio1'];
		$domicilio2                     = $_POST['domicilio2'];
		$cp                             = $_POST['cp'];
		$idEstado                       = $_POST['estado'];
		$rfc                            = $_POST['rfc'];
		$fechaNac                       = $_POST['fechaNac'];
		$telefono                       = $_POST['telefono'];
		$celular                        = $_POST['celular'];
		$email                          = $_POST['email'];

		if (!$nombres = validarFormulario('s',$nombres,0))
		{
			$response['mensaje'] = "El campo Nombre no cumple con el formato esperado y no puede estar en blanco";
			$response['focus'] = 'nombres';
			responder($response, $mysqli);
		}
		if (!$apellidop = validarFormulario('s',$apellidop,0))
		{
			$response['mensaje'] = "El campo Apellido paterno no puede estar en blanco";
			$response['focus'] = 'apellidop';
			responder($response, $mysqli);
		}
		$apellidom = validarFormulario('s', $apellidom, FALSE);
		if (!$domicilio1 = validarFormulario('s',$domicilio1,0))
		{
			$response['mensaje'] = "El campo domicilio no puede estar en blanco";
			$response['focus'] = 'domicilio1';
			responder($response, $mysqli);
		}
		if (!$domicilio2 = validarFormulario('s',$domicilio2,0))
		{
			$response['mensaje'] = "El campo domicilio no puede estar en blanco";
			$response['focus'] = 'domicilio2';
			responder($response, $mysqli);
		}
		if (!$cp = validarFormulario('s',$cp,0))
		{
			$response['mensaje'] = "El campo código postal no puede estar en blanco";
			$response['focus'] = 'cp';
			responder($response, $mysqli);
		}
		if (!$idEstado = validarFormulario('i',$idEstado))
		{
			$response['mensaje'] = "El formato del campo estado no es el correcto";
			$response['focus'] = 'estado';
			responder($response, $mysqli);
		}
		$rfc = validarFormulario('s',$rfc);
		if (!$fechaNac = validarFormulario('d',$fechaNac))
		{
			$response['mensaje'] = "Elige una fecha válida. El formato de la fecha no es el correcto.";
			$response['focus'] = 'fechaNac';
			responder($response, $mysqli);
		}
		$telefono 		= validarFormulario('s',$telefono, FALSE);
		$celular 		= validarFormulario('s',$celular, FALSE);
		$email 			= validarFormulario('s', $email, FALSE);

		$idUsuario      = $sesion->get('id');
		$sql            = "SELECT idSucursal FROM cat_usuarios WHERE id = $idUsuario LIMIT 1";
		$res_noSucursal = $mysqli->query($sql);
		$row_noSucursal = $res_noSucursal->fetch_assoc();
		$idSucursal     = $row_noSucursal['idSucursal'];
		if (strlen($rfc) > 0)
		{
			$sql		= "SELECT id FROM clientes WHERE rfc = ? AND activo = 1 AND idSucursal = ?";
			$params		= array('si',$rfc, $idSucursal);
			if ($query 	->sentence($sql, $params) && $query->num_rows())
			{
				$response['mensaje']= "No se puede guardar este nuevo registro porque ya existe un cliente en esta sucursal con el mismo RFC";
				$response['focus'] 	= 'rfc';
				responder($response, $mysqli);
			}
		}
		$sql            = "INSERT INTO clientes
		(nombres, apellidop, apellidom, domicilio1, domicilio2, cp, idEstado, rfc, fechaNac, tel, cel, email, idSucursal, usuario)
		VALUES
		(?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
		$params			= array('ssssssisssssii', $nombres, $apellidop, $apellidom, $domicilio1, $domicilio2, $cp, $idEstado, $rfc, $fechaNac, $telefono, $celular, $email, $idSucursal, $idUsuario);
		if ($query 		->sentence($sql, $params))
		{
			if($query	->affected_rows() == 0)
			{
				$response['mensaje']        = "No se modificó nada, no se pudo guardar el registro, inténtalo nuevamente";
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

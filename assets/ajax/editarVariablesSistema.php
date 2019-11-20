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
        $usuario = new usuario($idUsuario,$mysqli);
        $permiso = $usuario->permiso("modificarVariablesSistema",$mysqli);
        if (!$permiso)
        {
            $response['mensaje'] = "No se pudo modificar este registro. Usuario con permisos insuficientes para realizar esta acción";
            $response['status'] = 0;
            responder($response, $mysqli);
        }
		require_once ("../php/query.class.php");
		$query = new Query();

		$clausulasContrato 		= $_POST['clausulas'];
		$idUsuario 				= $sesion->get('id');

		$rowIdSucursal = $query ->table("cat_usuarios")	->select("idSucursal")
														->where("id", "=", $idUsuario, "i")
														->limit(1)->execute();
		$idSucursal = $rowIdSucursal[0]["idSucursal"];

        $response = array(
            "status"                    => 0
        );

        if (!$nombre					= validarFormulario('s', $_POST['nombreSucursal'], 0))
        {
            $response['mensaje']        = "El campo Nombre de la Sucursal no puede estar en blanco";
            $response['focus']          = 'nombreSucursal';
            responder($response, $mysqli);
        }
		$lema                       	= validarFormulario('s', $_POST['lema'], FALSE);

        if (!$representante       		= validarFormulario('s', $_POST['nombreRepresentante'], 0))
        {
            $response['mensaje']        = "El campo Nombre del Representante no puede estar en blanco";
            $response['focus']          = 'nombreRepresentante';
            responder($response, $mysqli);
        }
		if (!$direccion1                = validarFormulario('s', $_POST['domicilio1'], 0))
		{
			$response['mensaje']        = "El campo domicilio no puede estar en blanco";
			$response['focus']          = 'domicilio1';
			responder($response, $mysqli);
		}
		if (!$direccion2                = validarFormulario('s',$_POST['domicilio2'], 0))
		{
			$response['mensaje']        = "El campo domicilio no puede estar en blanco";
			$response['focus']          = 'domicilio2';
			responder($response, $mysqli);
		}
		if (!$cp                        = validarFormulario('s', $_POST['cp'], 0))
		{
			$response['mensaje']        = "El campo código postal no puede estar en blanco";
			$response['focus']          = 'cp';
			responder($response, $mysqli);
		}
		if (!$estado                  	= validarFormulario('i', $_POST['estado']))
		{
			$response['mensaje']        = "El formato del campo Estado no es el correcto";
			$response['focus']          = 'estado';
			responder($response, $mysqli);
		}
		$telefono1                      = validarFormulario('s', $_POST['telefono1'], FALSE);
        $telefono2                      = validarFormulario('s', $_POST['telefono2'], FALSE);
		$celular                        = validarFormulario('s', $_POST['celular'], FALSE);
		$rfc                        	= validarFormulario('s', $_POST['rfc'], FALSE);
        $curp                        	= validarFormulario('s', $_POST['curp'], FALSE);
		$correo                      	= validarFormulario('s', $_POST['email'], FALSE);
        $idRegimenFiscal             	= validarFormulario('i', $_POST['regimen'], FALSE);
		if (!$periodoNomina				= validarFormulario('i', $_POST['periodoNomina']))
		{
			$response['mensaje']        = "El formato del campo Periodo n&oacute;mina no es el correcto";
			$response['focus']          = 'periodoNomina';
			responder($response, $mysqli);
		}
		if (!$tasaVenta                  	= validarFormulario('i', $_POST['tasaVentas']))
		{
			$response['mensaje']        = "El formato del campo Tasa default comisi&oacute;n ventas no es el correcto";
			$response['focus']          = 'tasaVentas';
			responder($response, $mysqli);
		}
		if (!$tasaCobranza              = validarFormulario('i', $_POST['tasaCobranza']))
		{
			$response['mensaje']        = "El formato del campo Tasa default comisi&oacute;n cobranza no es el correcto";
			$response['focus']          = 'tasaCobranza';
			responder($response, $mysqli);
		}
		$query ->table("cat_sucursales") ->update(compact(	"nombre", "lema", "representante", "direccion1", "direccion2",
															"cp", "estado", "telefono1", "telefono2", "celular", "rfc", "curp",
															"correo", "idRegimenFiscal", "periodoNomina", "tasaVenta", "tasaCobranza",
															"clausulasContrato"), "ssssssissssssiiiis")
										->execute();
		if ($query ->status() && $query ->affected_rows())
		{
			$ipUsuario 				= $sesion->get("ip");
			$pantalla				= "Variables del sistema";
			$descripcion			= "Se modificaron variables del sistema";
			$sql					= "CALL agregarEvento($idUsuario, '$ipUsuario', '$pantalla', '$descripcion', $idSucursal);";
			$mysqli					->query($sql);
			//////////////////////////////////////////////////
            $response['mensaje']        = "Informaci&oacute;n guardada exitosamente";
            $response['status']         = 1;
            responder($response, $mysqli);
		}
		else
		{
			$response['mensaje'] = "No modific&oacute; nada. Vuelve a intentarlo";
            responder($response, $mysqli);
		}
    }
?>

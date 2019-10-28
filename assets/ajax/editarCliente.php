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
        $permiso = $usuario->permiso("modificarCliente",$mysqli);
        if (!$permiso)
        {
            $response['mensaje'] = "No se pudo modificar este registro. Usuario con permisos insuficientes para realizar esta acción";
            $response['status'] = 0;
            responder($response, $mysqli);
        }
        $id                             = $_POST['inputIdEdit'];
        $nombres                        = $_POST['inputNombreEdit'];
        $apellidop                      = $_POST['inputApellidopEdit'];
        $apellidom                      = $_POST['inputApellidomEdit'];
        $domicilio1                     = $_POST['inputDomicilio1Edit'];
        $domicilio2                     = $_POST['inputDomicilio2Edit'];
        $cp                             = $_POST['inputCPEdit'];
        $idEstado                       = $_POST['inputEstadoEdit'];
        $rfc                            = $_POST['inputRFCEdit'];
        $fechaNac                       = $_POST['inputFechaNacEdit'];
        $telefono                       = $_POST['inputTelefonoEdit'];
        $celular                        = $_POST['inputCelularEdit'];
        $email                          = $_POST['inputEmailEdit'];
        $response = array(
            "status"                    => 1
        );
        if(!$id = validarFormulario('i', $id))
        {
            $response['mensaje']        = "El ID del cliente debe ser numérico";
            $response['status']         = 0;
            $response['focus']          = 'inputNombreEdit';
            responder($response, $mysqli);
        }
        if (!$nombres                   = validarFormulario('s',$nombres,0))
        {
            $response['mensaje']        = "El campo Nombre no cumple con el formato esperado y no puede estar en blanco";
            $response['status']         = 0;
            $response['focus']          = 'inputNombreEdit';
            responder($response, $mysqli);
        }
        if (!$apellidop                 = validarFormulario('s',$apellidop,0))
        {
            $response['mensaje']        = "El campo Apellido paterno no puede estar en blanco";
            $response['status']         = 0;
            $response['focus']          = 'inputApellidopEdit';
            responder($response, $mysqli);
        }
        $apellidom                      = validarFormulario('s', $apellidom, FALSE);
        if (!$domicilio1                = validarFormulario('s', $domicilio1, 0))
        {
            $response['mensaje']        = "El campo domicilio no puede estar en blanco";
            $response['status']         = 0;
            $response['focus']          = 'inputDomicilio1Edit';
            responder($response, $mysqli);
        }
        if (!$domicilio2                = validarFormulario('s',$domicilio2,0))
        {
            $response['mensaje']        = "El campo domicilio no puede estar en blanco";
            $response['status']         = 0;
            $response['focus']          = 'inputDomicilio2Edit';
            responder($response, $mysqli);
        }
        if (!$cp                        = validarFormulario('s',$cp,0))
        {
            $response['mensaje']        = "El campo código postal no puede estar en blanco";
            $response['status']         = 0;
            $response['focus']          = 'inputCPEdit';
            responder($response, $mysqli);
        }
        if (!$idEstado                  = validarFormulario('i',$idEstado))
        {
            $response['mensaje']        = "El formato del campo estado no es el correcto";
            $response['status']         = 0;
            $response['focus']          = 'inputEstadoEdit';
            responder($response, $mysqli);
        }
        $rfc                       		= validarFormulario('s',$rfc, FALSE);
        if (!$fechaNac                  = validarFormulario('d',$fechaNac))
        {
            $response['mensaje']        = "Elige una fecha válida. El formato de la fecha no es el correcto.";
            $response['status']         = 0;
            $response['focus']          = 'inputFechaNacEdit';
            responder($response, $mysqli);
        }
        $telefono                       = validarFormulario('s',$telefono,FALSE);
        $celular                        = validarFormulario('s',$celular,FALSE);
        $email                          = validarFormulario('s',$email,FALSE);

        $idUsuario      = $sesion->get('id');
        $sql            = "SELECT idSucursal FROM cat_usuarios WHERE id = $idUsuario LIMIT 1";
        $res_noSucursal = $mysqli->query($sql);
        $row_noSucursal = $res_noSucursal->fetch_assoc();
        $idSucursal     = $row_noSucursal['idSucursal'];
		if (strlen($rfc) > 0)
		{
	        $sql = "SELECT id FROM clientes WHERE rfc = '$rfc' AND activo = 1 AND idSucursal = $idSucursal AND id <> $id";
	        $res_rfc = $mysqli->query($sql);
	        if ($res_rfc->num_rows > 0)
	        {
	            $response['mensaje'] = "No se puede actualizar este registro porque ya existe un cliente en esta sucursal con el mismo RFC";
	            $response['status'] = 0;
	            $response['focus'] = 'rfc';
	            responder($response, $mysqli);
	        }
		}
        $sql = "UPDATE clientes
                SET nombres             = ?,
                    apellidop           = ?,
                    apellidom           = ?,
                    domicilio1          = ?,
                    domicilio2          = ?,
                    cp                  = ?,
                    idEstado            = ?,
                    rfc                 = ?,
                    fechaNac            = ?,
                    tel                 = ?,
                    cel                 = ?,
                    email               = ?
                WHERE id                = ?
                LIMIT 1";
        if($prepare                     = $mysqli->prepare($sql))
        {
            if(!$prepare->bind_param('ssssssisssssi', $nombres, $apellidop, $apellidom, $domicilio1, $domicilio2, $cp, $idEstado, $rfc, $fechaNac, $telefono, $celular, $email, $id))
            {
                $response['mensaje']    = "Error. No se pudo modificar la información. Falló el la vinculación de parámetros. Inténtalo nuevamente";
                $response['status']     = 0;
                responder($response, $mysqli);
            }
            if(!$prepare->execute())
            {
                $response['mensaje']    = "Error. No se pudo modificar la información. Falló el enlace a la base de datos. Inténtalo nuevamente";
                $response['status']     = 0;
                responder($response, $mysqli);
            }
            if($prepare->affected_rows  == 0)
            {
                $response['mensaje']    = "No se modificó nada";
                $response['status']     = 2;
                responder($response, $mysqli);
            }
			// Agregar evento en la bitácora de eventos ///////
			$idUsuario 				= $sesion->get("id");
			$ipUsuario 				= $sesion->get("ip");
			$nombreCliente			= "$nombres $apellidop $apellidom";
			$pantalla				= "Agregar cliente";
			$descripcion			= "Se modificó el cliente cliente ($nombreCliente) con id=$id del catálogo de clientes";
			$sql					= "CALL agregarEvento($idUsuario, '$ipUsuario', '$pantalla', '$descripcion', $idSucursal);";
			$mysqli					->query($sql);
            $response['mensaje']        = "El cliete '$nombreCliente' fue modificado exitosamente";
            $response['status']         = 1;
            responder($response, $mysqli);
        }
        else
        {
            $response['mensaje']        = "Error. No se pudo modificar. Falló la preparación de los datos. Vuelve a intentarlo nuevamente";
            $response['status']         = 0;
            responder($response, $mysqli);
        }
    }
?>

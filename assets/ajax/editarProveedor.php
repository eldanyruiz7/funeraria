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
        $usuario = new usuario($idUsuario,$mysqli);
        $permiso = $usuario->permiso("modificarProveedor",$mysqli);
        if (!$permiso)
        {
            $response['mensaje'] = "No se pudo modificar este registro. Usuario con permisos insuficientes para realizar esta acción";
            $response['status'] = 0;
            responder($response, $mysqli);
        }
        $id                             = $_POST['inputIdEdit'];
        $rsocial                        = $_POST['inputRsocialEdit'];
        $representante                  = $_POST['inputRepresentanteEdit'];
        $telefono                       = $_POST['inputTelefonoEdit'];
        $celular                        = $_POST['inputCelularEdit'];
        $domicilio1                     = $_POST['inputDomicilio1Edit'];
        $domicilio2                     = $_POST['inputDomicilio2Edit'];
        $cp                             = $_POST['inputCPEdit'];
        $idEstado                       = $_POST['inputEstadoEdit'];
        $rfc                            = $_POST['inputRFCEdit'];
        $email                      	= $_POST['inputEmailEdit'];

		$idUsuario      				= $sesion->get('id');
        $sql            				= "SELECT idSucursal FROM cat_usuarios WHERE id = $idUsuario LIMIT 1";
        $res_noSucursal 				= $mysqli->query($sql);
        $row_noSucursal 				= $res_noSucursal->fetch_assoc();
        $idSucursal     				= $row_noSucursal['idSucursal'];
        $response = array(
            "status"                    => 1
        );
        if(!$id = validarFormulario('i', $id))
        {
            $response['mensaje']        = "El ID del proveedor debe ser numérico";
            $response['status']         = 0;
            $response['focus']          = '';
            responder($response, $mysqli);
        }
        if (!$rsocial                   = validarFormulario('s',$rsocial,0))
        {
            $response['mensaje']        = "El campo Razón social no puede estar en blanco";
            $response['status']         = 0;
            $response['focus']          = 'inputRsocialEdit';
            responder($response, $mysqli);
        }
        if (!$representante             = validarFormulario('s',$representante,0))
        {
            $response['mensaje']        = "El campo Representante no puede estar en blanco";
            $response['status']         = 0;
            $response['focus']          = 'inputRepresentanteEdit';
            responder($response, $mysqli);
        }
        $telefono                       = validarFormulario('s', $telefono, FALSE);
        $celular                        = validarFormulario('s', $celular, FALSE);
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
        if (!$rfc                       = validarFormulario('s',$rfc,0))
        {
            $response['mensaje']        = "El campo RFC no puede estar en blanco";
            $response['status']         = 0;
            $response['focus']          = 'inputRFCEdit';
            responder($response, $mysqli);
        }
        $email                      = validarFormulario('s', $email, FALSE);
        $idUsuario      = $sesion->get('id');
        $sql = "SELECT id FROM cat_proveedores WHERE rfc = '$rfc' AND activo = 1 AND id <> $id";
        $res_rfc = $mysqli->query($sql);
        if ($res_rfc->num_rows > 0)
        {
            $response['mensaje'] = "No se puede actualizar este registro porque ya existe un provedor en esta sucursal con el mismo RFC";
            $response['status'] = 0;
            $response['focus'] = 'rfc';
            responder($response, $mysqli);
        }
        $sql = "UPDATE cat_proveedores
                SET rsocial             = ?,
                    representante       = ?,
                    telefono            = ?,
                    celular             = ?,
                    domicilio1          = ?,
                    domicilio2          = ?,
                    cp                  = ?,
                    idEstado            = ?,
                    rfc                 = ?,
                    email               = ?
                WHERE id                = ?
                LIMIT 1";
        if($prepare                     = $mysqli->prepare($sql))
        {
            if(!$prepare->bind_param('sssssssissi', $rsocial, $representante, $telefono, $celular, $domicilio1, $domicilio2, $cp, $idEstado, $rfc, $email, $id))
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
			// $insert_id              = $mysqli->insert_id;
			$ipUsuario 				= $sesion->get("ip");
			$pantalla				= "Editar proveedor";
			$descripcion			= "Se modificó el proveedor($rsocial) con id=$id.";
			$sql					= "CALL agregarEvento($idUsuario, '$ipUsuario', '$pantalla', '$descripcion', $idSucursal);";
			$mysqli					->query($sql);
			//////////////////////////////////////////////////
            $response['mensaje']        = "El proveedor '$rsocial' fue modificado exitosamente";
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

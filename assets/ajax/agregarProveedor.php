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
        $permiso = $usuario->permiso("agregarProveedor",$mysqli);
        if (!$permiso)
        {
            $response['mensaje'] = "No se pudo guardar este registro. Usuario con permisos insuficientes para realizar esta acción";
            $response['status'] = 0;
            responder($response, $mysqli);
        }

        $rsocial                        = $_POST['rsocial'];
        $representante                  = $_POST['representante'];
        $telefono                       = $_POST['telefono'];
        $celular                        = $_POST['celular'];
        $domicilio1                     = $_POST['domicilio1'];
        $domicilio2                     = $_POST['domicilio2'];
        $cp                             = $_POST['cp'];
        $idEstado                       = $_POST['estado'];
        $rfc                            = $_POST['rfc'];
        $email                          = $_POST['email'];
		$idUsuario      				= $sesion->get('id');
        $sql            				= "SELECT idSucursal FROM cat_usuarios WHERE id = $idUsuario LIMIT 1";
        $res_noSucursal 				= $mysqli->query($sql);
        $row_noSucursal 				= $res_noSucursal->fetch_assoc();
        $idSucursal     				= $row_noSucursal['idSucursal'];
        $response 						= array(
		            "status"                    => 1
		        );

        if (!$rsocial = validarFormulario('s',$rsocial,0))
        {
            $response['mensaje'] = "El campo Razón social no puede estar en blanco";
            $response['status'] = 0;
            $response['focus'] = 'rsocial';
            responder($response, $mysqli);
        }
        if (!$representante = validarFormulario('s',$representante,0))
        {
            $response['mensaje'] = "El campo Representante no puede estar en blanco";
            $response['status'] = 0;
            $response['focus'] = 'representante';
            responder($response, $mysqli);
        }
        $telefono = validarFormulario('s', $telefono, FALSE);
        $celular = validarFormulario('s', $celular, FALSE);
        if (!$domicilio1 = validarFormulario('s',$domicilio1,0))
        {
            $response['mensaje'] = "El campo domicilio no puede estar en blanco";
            $response['status'] = 0;
            $response['focus'] = 'domicilio1';
            responder($response, $mysqli);
        }
        if (!$domicilio2 = validarFormulario('s',$domicilio2,0))
        {
            $response['mensaje'] = "El campo domicilio no puede estar en blanco";
            $response['status'] = 0;
            $response['focus'] = 'domicilio2';
            responder($response, $mysqli);
        }
        if (!$cp = validarFormulario('s',$cp,0))
        {
            $response['mensaje'] = "El campo código postal no puede estar en blanco";
            $response['status'] = 0;
            $response['focus'] = 'cp';
            responder($response, $mysqli);
        }
        if (!$idEstado = validarFormulario('i',$idEstado))
        {
            $response['mensaje'] = "El formato del campo estado no es el correcto";
            $response['status'] = 0;
            $response['focus'] = 'estado';
            responder($response, $mysqli);
        }
        $email = validarFormulario('s',$email,0);
        if (!$rfc = validarFormulario('s',$rfc,0))
        {
            $response['mensaje'] = "El formato del campo rfc no puede estar en blanco";
            $response['status'] = 0;
            $response['focus'] = 'rfc';
            responder($response, $mysqli);
        }
        $sql = "SELECT id FROM cat_proveedores WHERE rfc = '$rfc' AND activo = 1";
        $res_rfc = $mysqli->query($sql);
        if ($res_rfc->num_rows > 0)
        {
            $response['mensaje'] = "No se puede guardar este nuevo registro porque ya existe un proveedor en esta sucursal con el mismo RFC";
            $response['status'] = 0;
            $response['focus'] = 'rfc';
            responder($response, $mysqli);
        }
        $sql            = "INSERT INTO cat_proveedores
                                (rsocial, representante, telefono, celular, domicilio1, domicilio2, cp, idEstado, rfc, email, usuario)
                            VALUES
                                (?,?,?,?,?,?,?,?,?,?,?)";
        if($prepare     = $mysqli->prepare($sql))
        {
            if(!$prepare->bind_param('sssssssissi', $rsocial, $representante, $telefono, $celular, $domicilio1, $domicilio2, $cp, $idEstado, $rfc, $email, $idUsuario))
            {
                $response['mensaje'] = "Error. No se pudo guardar la información. Falló el la vinculación de parámetros. Inténtalo nuevamente";
                $response['status'] = 0;
                responder($response, $mysqli);
            }
            if(!$prepare->execute())
            {
                $response['mensaje'] = "Error. No se pudo guardar la información. Falló el enlace a la base de datos. Inténtalo nuevamente";
                $response['status'] = 0;
                responder($response, $mysqli);
            }
            if($prepare->affected_rows == 0)
            {
                $response['mensaje']        = "No se modificó nada, no se pudo guardar el registro, inténtalo nuevamente";
                $response['status']         = 0;
                responder($response, $mysqli);
            }
            $insert_id                  = $mysqli->insert_id;
			// Agregar evento en la bitácora de eventos ///////
			$idUsuario 					= $sesion->get("id");
			$ipUsuario 					= $sesion->get("ip");
			$pantalla					= "Agregar proveedor";
			$descripcion				= "Se agregó un nuevo proveedor ($rsocial) con id=$insert_id.";
			$sql						= "CALL agregarEvento($idUsuario, '$ipUsuario', '$pantalla', '$descripcion', $idSucursal);";
			$mysqli						->query($sql);
            $response['mensaje']        = "$rsocial";
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

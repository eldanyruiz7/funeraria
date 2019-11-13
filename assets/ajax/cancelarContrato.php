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
        usleep(500000);
        require "../php/responderJSON.php";
        require ("../php/usuario.class.php");
        $usuario = new usuario($idUsuario,$mysqli);
        $permiso = $usuario->permiso("modificarContrato",$mysqli);
        if (!$permiso)
        {
            $response['mensaje'] = "No se pudo modificar este registro. Usuario con permisos insuficientes para realizar esta acción";
            $response['status'] = 0;
            responder($response, $mysqli);
        }
        $id                             = $_POST['idCliente'];
        $motivo                         = $_POST['motivo'];

		$idUsuario      = $sesion->get('id');
		$sql            = "SELECT idSucursal FROM cat_usuarios WHERE id = $idUsuario LIMIT 1";
		$res_noSucursal = $mysqli->query($sql);
		$row_noSucursal = $res_noSucursal->fetch_assoc();
		$idSucursal     = $row_noSucursal['idSucursal'];
		
        $response = array(
            "status"                    => 1
        );
        if(!$id = validarFormulario('i', $id))
        {
            $response['mensaje']        = "El ID del contrato no es el correcto";
            $response['status']         = 0;
            $response['focus']          = '';
            responder($response, $mysqli);
        }
        if(!$motivo = validarFormulario('i', $motivo))
        {
            $response['mensaje']        = "El ID del motivo de cancelación no es el correcto";
            $response['status']         = 0;
            $response['focus']          = '';
            responder($response, $mysqli);
        }
		$sql = "SELECT id, nombre FROM cat_motivosCancelacionContratos WHERE id = $motivo LIMIT 1";
		$res_motivo = $mysqli->query($sql);
		if ($res_motivo->num_rows == 0)
		{
			$response['mensaje']        = "El ID del motivo de cancelación no existe";
            $response['status']         = 0;
            $response['focus']          = '';
            responder($response, $mysqli);
		}
		$row_motivo 	= $res_motivo->fetch_assoc();
		$idMotivo		= $row_motivo['id'];
        // $motivo = $motivo > 5 || $motivo < 1 ? 1 : $motivo;
        $idUsuario      = $sesion->get('id');
        $sql = "SELECT id FROM contratos WHERE id = $id AND activo = 1 AND motivoCancelado = 0";
        $res_contrato = $mysqli->query($sql);
        if ($res_contrato->num_rows == 0)
        {
            $response['mensaje'] = "No se puede cancelar este contrato porque no existe o ya ha sido cancelado";
            $response['status'] = 0;
            $response['focus'] = '';
            responder($response, $mysqli);
        }

        $sql = "SELECT id FROM contratos WHERE id = $id AND activo = 1 AND enCurso = 0";
        $res_contrato_ = $mysqli->query($sql);
        if ($res_contrato_->num_rows > 0)
        {
            $response['mensaje'] = "No se puede cancelar este contrato porque ya está pagado";
            $response['status'] = 0;
            $response['focus'] = '';
            responder($response, $mysqli);
        }
        $row_contrato = $res_contrato->fetch_assoc();
        $idRecibo = $row_contrato['id'];
        $mysqli->autocommit(FALSE);
        $sql = "UPDATE contratos
                SET motivoCancelado     = ?
                WHERE id                = ?
                LIMIT 1";
        if($prepare                     = $mysqli->prepare($sql))
        {
            if(!$prepare->bind_param('ii', $idMotivo, $id))
            {
                $response['mensaje']    = "Error. No se pudo modificar la información. Falló el la vinculación de parámetros. Inténtalo nuevamente";
                $response['status']     = 0;
                $mysqli->rollback();
                responder($response, $mysqli);
            }
            if(!$prepare->execute())
            {
                $response['mensaje']    = "Error. No se pudo modificar la información. Falló el enlace a la base de datos. Inténtalo nuevamente";
                $response['status']     = 0;
                $mysqli->rollback();
                responder($response, $mysqli);
            }
            if($prepare->affected_rows  == 0)
            {
                $response['mensaje']    = "No se modificó nada. Vuelve a intentarlo";
                $response['status']     = 2;
                $mysqli->rollback();
                responder($response, $mysqli);
            }
            else
            {
				// Agregar evento en la bitácora de eventos ///////
				$idUsuario				= $sesion->get("id");
				$ipUsuario 				= $sesion->get("ip");
				$nombreMotivo			= $row_motivo['nombre'];
				$pantalla				= "Listar contratos";
				$descripcion			= "Se ha cancelado el contrato id=$id. Motivo de cancelación=$nombreMotivo";
				$sql					= "CALL agregarEvento($idUsuario, '$ipUsuario', '$pantalla', '$descripcion', $idSucursal);";
				$mysqli					->query($sql);
				//////////////////////////////////////////////////
                if($mysqli->commit())
                {
                    $response['mensaje']    = "Contrato cancelado correctamente";
                    $response['status']     = 1;
                    responder($response, $mysqli);
                }
                else
                {
					$mysqli->rollback();
                    $response['mensaje']    = "Error en commit. Ocurrió un rollback. No se modificó nada. Vuelve  intentarlo";
                    $response['status']     = 0;
                    responder($response, $mysqli);
                }
            }
        }
        else
        {
            $response['mensaje']        = "Error. No se pudo modificar. Falló la preparación de los datos. Vuelve a intentarlo nuevamente";
            $response['status']         = 0;
            responder($response, $mysqli);
        }
    }
?>

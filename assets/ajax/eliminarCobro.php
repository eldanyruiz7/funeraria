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
        //$permiso = $usuario->permiso("eliminarDifunto",$mysqli);
        if (!($usuario->tipo == 0 || $usuario->tipo == 1))
        {
            $response['mensaje'] = "No se pudo eliminar este registro. Usuario con permisos insuficientes para realizar esta acción";
            $response['status'] = 0;
            responder($response, $mysqli);
        }
        $id                             = $_POST['idCliente'];
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
            $response['mensaje']        = "El ID del registro del cobro no es el correcto";
            $response['status']         = 0;
            $response['focus']          = '';
            responder($response, $mysqli);
        }
        $idUsuario      = $sesion->get('id');
        $sql = "SELECT id, idFolio_cobranza FROM detalle_pagos_contratos WHERE id = $id AND activo = 1";
        $res_compra = $mysqli->query($sql);
        if ($res_compra->num_rows == 0)
        {
            $response['mensaje'] = "No se puede cancelar este registro porque no existe o ya ha sido cancelado";
            $response['status'] = 0;
            $response['focus'] = '';
            responder($response, $mysqli);
        }
        $row_recibo = $res_compra->fetch_assoc();
        $idFolio = $row_recibo['idFolio_cobranza'];
        $idRecibo = $row_recibo['id'];
        $mysqli->autocommit(FALSE);
        $sql = "UPDATE detalle_pagos_contratos
                SET activo              = 0
                WHERE id                = ?
                LIMIT 1";
        if($prepare                     = $mysqli->prepare($sql))
        {
            if(!$prepare->bind_param('i', $idRecibo))
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
                $sql = "UPDATE folios_cobranza_asignados SET asignado = 0 WHERE id = ? LIMIT 1";
                if($prepare_det                     = $mysqli->prepare($sql))
                {
                    if(!$prepare_det->bind_param('i', $idFolio))
                    {
                        $response['mensaje']    = "Error. No se pudo modificar el folio de cobranza asignado. Falló el la vinculación de parámetros. Inténtalo nuevamente";
                        $response['status']     = 0;
                        $mysqli->rollback();
                        responder($response, $mysqli);
                    }
                    if(!$prepare_det->execute())
                    {
                        $response['mensaje']    = "Error.  No se pudo modificar lel folio de cobranza asignado. Falló el enlace a la base de datos. Inténtalo nuevamente";
                        $response['status']     = 0;
                        $mysqli->rollback();
                        responder($response, $mysqli);
                    }
					// Agregar evento en la bitácora de eventos ///////
					$sql = "SELECT folio FROM folios_cobranza_asignados WHERE id=$idFolio LIMIT 1";
					$res_folio 					= $mysqli->query($sql);
					$row_folio					= $res_folio->fetch_assoc();
					$foloRecibo					= $row_folio['folio'];
					$ipUsuario 					= $sesion->get("ip");
					$pantalla					= "Reporte cobranza";
					$descripcion				= "Cobro cancelado. Folio=$foloRecibo, id=$idFolio";
					$sql						= "CALL agregarEvento($idUsuario, '$ipUsuario', '$pantalla', '$descripcion', $idSucursal);";
					$mysqli						->query($sql);
                    if($mysqli->commit())
                    {
                        $response['mensaje']    = "Este cobro ha sido cancelado correctamente";
                        $response['status']     = 1;
                        responder($response, $mysqli);
                    }
                    else
                    {
                        $response['mensaje']    = "Error en commit. Ocurrió un rollback. No se modificó nada. Vuelve  intentarlo";
                        $response['status']     = 0;
                        responder($response, $mysqli);
                    }

                }
                else
                {
                    $response['respuesta'] = "Error. No se pudo modificar el folio de cobranza asignado. No se pudo actualizar la información. Falló en la preparación de los datos. Inténtalo nuevamente";
                    $response['status'] = 0;
                    $mysqli->rollback();
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

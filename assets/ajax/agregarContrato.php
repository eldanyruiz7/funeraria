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
		$query 		= new Query();
        $usuario 	= new usuario($idUsuario,$mysqli);
        $permiso 	= $usuario->permiso("agregarContrato",$mysqli);
		$response 	= array("status" => 0);
        if (!$permiso)
        {
            $response['mensaje'] = "No se pudo guardar este registro. Usuario con permisos insuficientes para realizar esta acción";
            $response['status'] = 0;
            responder($response, $mysqli);
        }
        $folio                          = $_POST['folio'];
        $idCliente                      = $_POST['idClienteHidden'];
        $domicilio1                     = $_POST['domicilio1'];
        $domicilio2                     = $_POST['domicilio2'];
        $cp                             = $_POST['cp'];
        $idEstado                       = $_POST['estado'];
        $referencias                    = $_POST['referencias'];
        $idPlan                         = $_POST['idPlanHidden'];
        $frecuencia                     = $_POST['frecuencia'];
        $precio                         = $_POST['precio'];
        $descuentoDuplicacionInversion  = $_POST['descuentoDuplicacionInversion'];
        $descuentoCambioFuneraria       = $_POST['descuentoCambioFuneraria'];
        $descuentoAdicional             = $_POST['descuentoAdicional'];
        $anticipo                       = $_POST['anticipo'];
        $aportacion                     = $_POST['aportacion'];
        $fechaAportacion                = $_POST['fechaAportacion'];
        $idVendedor                     = $_POST['vendedor'];
        $observaciones                  = $_POST['observaciones'];

        $folio = validarFormulario('s',$folio);
        if (!$idCliente = validarFormulario('i',$idCliente))
        {
            $response['mensaje'] = "El formato del id del cliente no es el correcto. Elige un cliente de la lista para asignarlo a este contrato";
            $response['focus'] = 'nombre';
            responder($response, $mysqli);
        }
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
            $response['mensaje'] = "El formato del id del estado o es el correcto. Elige un estado";
            $response['focus'] = 'estado';
            responder($response, $mysqli);
        }
        $referencias = validarFormulario('s', $referencias, FALSE);
        if (!$idPlan = validarFormulario('i',$idPlan))
        {
            $response['mensaje'] = "Debes elegir un plan funerario para este contrato";
            $response['focus'] = 'plan';
            responder($response, $mysqli);
        }

        if (!$frecuencia = validarFormulario('i',$frecuencia))
        {
            $response['mensaje'] = "El formato para la frecuencia de pago no es el correcto";
            $response['focus'] = 'frecuencia';
            responder($response, $mysqli);
        }

        if (!$precio = validarFormulario('i',$precio))
        {
            $response['mensaje'] = "El campo Costo total del contrato no puede estar en blanco ni en cero (0)";
            $response['status'] = 0;
            $response['focus'] = 'precio';
            responder($response, $mysqli);
        }
        if (!$anticipo = validarFormulario('i',$anticipo))
        {
            $response['mensaje'] = "El campo Anticipo no puede estar en blanco ni en cero (0)";
            $response['focus'] = 'anticipo';
            responder($response, $mysqli);
        }

        if (!$aportacion = validarFormulario('i',$aportacion))
        {
            $response['mensaje'] = "El campo aportación no puede estar en blanco ni en cero (0)";
            $response['focus'] = 'aportacion';
            responder($response, $mysqli);
        }

        if (!$fechaAportacion = validarFormulario('d',$fechaAportacion))
        {
            $response['mensaje'] = "Elige una fecha válida. El formato de la fecha no es el correcto.";
            $response['focus'] = 'fechaAportacion';
            responder($response, $mysqli);
        }
        if (!$idVendedor = validarFormulario('i',$idVendedor))
        {
            $response['mensaje'] = "El formato del id del vendedor o es el correcto. Elige uno correcto";
            $response['focus'] = 'vendedor';
            responder($response, $mysqli);
        }
        $observaciones = validarFormulario('s', $observaciones, FALSE);
        $descuentoDuplicacionInversion  = validarFormulario("i", $_POST['descuentoDuplicacionInversion']);
        $descuentoCambioFuneraria       = validarFormulario("i", $_POST['descuentoCambioFuneraria']);
        $descuentoAdicional             = validarFormulario("i", $_POST['descuentoAdicional']);
        if (($anticipo + $aportacion + $descuentoDuplicacionInversion + $descuentoCambioFuneraria + $descuentoAdicional) > $precio)
        {
            $response['mensaje'] = "No se puede guardar porque la suma de la inversión más la primera aportación más los descuentos son mayores al costo total del contrato.";
            $response['focus'] = 'precio';
            responder($response, $mysqli);
        }
        $idUsuario      = $sesion->get('id');
		$idSucursal 	= $query ->table("cat_usuarios")	->select("idSucursal AS id")
														->where("id", "=", $idUsuario, "i")
														->limit(1)->execute(FALSE, OBJ);
		echo "ID=".$idSucursal->id;die;
        $sql            = "SELECT idSucursal FROM cat_usuarios WHERE id = $idUsuario LIMIT 1";
        $res_noSucursal = $mysqli->query($sql);
        $row_noSucursal = $res_noSucursal->fetch_assoc();
        $idSucursal     = $row_noSucursal['idSucursal'];

        $sql = "SELECT id FROM clientes WHERE id = ? AND activo = 1 LIMIT 1";
        $prepare_cliente = $mysqli-> prepare($sql);
        if (!$prepare_cliente ||
        	!$prepare_cliente -> bind_param('i', $idCliente) ||
        	!$prepare_cliente -> execute() ||
        	!$prepare_cliente -> store_result() ||
        	!$prepare_cliente -> bind_result($idCliente_) ||
        	!$prepare_cliente -> fetch() ||
            $prepare_cliente->num_rows == 0)
        {
            $response['mensaje'] = "No se puede asignar este cliente al contrato. Posiblemente haya sido eliminado del sistema. Elige otro distinto";
            $response['status'] = 0;
            $response['focus'] = 'nombre';
            responder($response, $mysqli);
        }

        $sql = "SELECT id FROM cat_planes WHERE id = ? AND activo = 1 LIMIT 1";
        $prepare_plan = $mysqli-> prepare($sql);
        if (!$prepare_plan ||
        	!$prepare_plan -> bind_param('i', $idPlan) ||
        	!$prepare_plan -> execute() ||
        	!$prepare_plan -> store_result() ||
        	!$prepare_plan -> bind_result($idPlan_) ||
        	!$prepare_plan -> fetch() ||
            $prepare_plan->num_rows == 0)
        {
            $response['mensaje'] = "No se puede asignar este plan. Posiblemente haya sido eliminado del sistema. Elige otro distinto";
            $response['status'] = 0;
            $response['focus'] = 'plan';
            responder($response, $mysqli);
        }
        $sql = "SELECT id, tasaComision FROM cat_usuarios WHERE id = ? AND activo = 1 LIMIT 1";
        $prepare_usr = $mysqli-> prepare($sql);
        if (!$prepare_usr ||
        	!$prepare_usr -> bind_param('i', $idVendedor) ||
        	!$prepare_usr -> execute() ||
        	!$prepare_usr -> store_result() ||
        	!$prepare_usr -> bind_result($idVendedor_, $tasaComision_) ||
        	!$prepare_usr -> fetch() ||
            $prepare_usr->num_rows == 0)
        {
            $response['mensaje'] = "No se puede asignar este vendedor. Posiblemente haya sido eliminado del sistema. Elige otro distinto";
            $response['status'] = 0;
            $response['focus'] = 'vendedor';
            responder($response, $mysqli);
        }
        $mysqli->autocommit(FALSE);
        $sql            = "INSERT INTO contratos
                                (folio, fechaPrimerAportacion, precio, descuentoDuplicacionInversion, descuentoCambioFuneraria, descuentoAdicional,
                                primerAnticipo, precioAportacion, direccion1, direccion2, cp, idEstado, referencias, formaPago, frecuenciaPago,
                                tasaComision, idTitular, idPlan, idVendedor, idSucursal, usuario, observaciones)
                            VALUES
                                (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
        if($prepare     = $mysqli->prepare($sql))
        {
            $formaPago = 1;
            $frecuencia = ($frecuencia < 1 || $frecuencia > 3) ? 1 : $frecuencia;
            if(!$prepare->bind_param('ssddddddsssisiiiiiiiis', $folio, $fechaAportacion, $precio, $descuentoDuplicacionInversion, $descuentoCambioFuneraria, $descuentoAdicional,
                                                            $anticipo, $aportacion, $domicilio1, $domicilio2, $cp, $idEstado, $referencias, $formaPago, $frecuencia,
                                                            $tasaComision_, $idCliente_, $idPlan_, $idVendedor_, $idSucursal, $idUsuario, $observaciones))
            {
                $response['mensaje'] = "Error. No se pudo guardar la información. Falló el la vinculación de parámetros. Inténtalo nuevamente";
                $response['status'] = 0;
                responder($response, $mysqli);
            }
            if(!$prepare->execute())
            {
                $response['mensaje'] = "Error. No se pudo guardar la información. Falló el enlace a la base de datos. Inténtalo nuevamente".$mysqli->error;
                $response['status'] = 0;
                responder($response, $mysqli);
            }
            $insert_id                  = $mysqli->insert_id;
            if($prepare->affected_rows == 0)
            {
                $response['mensaje']        = "No se modificó nada, no se pudo guardar el registro, inténtalo nuevamente";
                $response['status']         = 0;
                responder($response, $mysqli);
            }
            $sql = "SELECT idProducto, idServicio, cantidad FROM detalle_cat_planes WHERE idPlan = ? AND activo = 1";
            $prepare_det = $mysqli-> prepare($sql);
            if (!$prepare_det ||
            	!$prepare_det -> bind_param('i', $idPlan_) ||
            	!$prepare_det -> execute() ||
            	!$prepare_det -> store_result() ||
            	!$prepare_det -> bind_result($idProducto_det, $idServicio_det, $cantidad_det))
            {
                $mysqli->rollback();
                $response['mensaje'] = "Error. No se pudo guardar la información. Falló el la preparación de parámetros del detalle del plan. Inténtalo nuevamente";
                $response['status'] = 0;
                responder($response, $mysqli);
            }
            while ($prepare_det->fetch())
            {
                $sql_insert_det = "INSERT INTO detalle_contrato (idContrato, idProducto, idServicio, idSucursal, cantidad, usuario)
                                    VALUES (?,?,?,?,?,?)";
                $prepare_insert_det = $mysqli->prepare($sql_insert_det);
                if (!$prepare_insert_det ||
                	!$prepare_insert_det -> bind_param('iiiiii', $insert_id, $idProducto_det, $idServicio_det, $idSucursal, $cantidad_det,$idUsuario) ||
                	!$prepare_insert_det -> execute())
                {
                    $mysqli->rollback();
                    $response['mensaje'] = "Error. No se pudo guardar el detalle del contrato. Falló el la preparación de parámetros. Inténtalo nuevamente";
                    $response['status'] = 0;
                    responder($response, $mysqli);
                }
            }
			// Agregar evento en la bitácora de eventos ///////
			$idUsuario 				= $sesion->get("id");
			$ipUsuario 				= $sesion->get("ip");
			$pantalla				= "Agregar contrato";
			$descripcion			= "Se agregó un nuevo contrato con id=$insert_id. No de folio=$folio al catálogo de contratos.";
			$sql					= "CALL agregarEvento($idUsuario, '$ipUsuario', '$pantalla', '$descripcion', $idSucursal);";
			$mysqli					->query($sql);
			//////////////////////////////////////////////////
            if ($mysqli->commit())
            {
                $response['mensaje']        = $insert_id;
                $response['status']         = 1;
                responder($response, $mysqli);
            }
            else
            {
                $mysqli->rollback();
                $response['mensaje']        = "Error. No se pudo guardar. Falló en commit. Ocurrió un rollback. Vuelve a intentarlo nuevamente";
                $response['status']         = 0;
                responder($response, $mysqli);
            }
        }
        else
        {
            $mysqli->rollback();
            $response['mensaje']        = "Error. No se pudo guardar. Falló la preparación de los datos. Vuelve a intentarlo nuevamente";
            $response['status']         = 0;
            responder($response, $mysqli);
        }
    }
?>

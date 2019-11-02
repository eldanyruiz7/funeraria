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
		$query 				= new Query();
        $usuario 			= new usuario($idUsuario,$mysqli);
		$response 			= array(
			"status"        	=> 0 );
        $permiso = $usuario->permiso("agregarCompra",$mysqli);
        if (!$permiso)
        {
            $response['respuesta'] = "No se pudo guardar este registro. Usuario con permisos insuficientes para realizar esta acción";
            responder($response, $mysqli);
        }
        $idUsuario			= $sesion->get("id");
        $sql 				= "SELECT id, idSucursal FROM cat_usuarios WHERE id = ? LIMIT 1";
		$params				= array('i', $idUsuario);
		if ($query ->sentence($sql, $params))
		{
			if ($query->num_rows() == 0)
			{
                $response['respuesta']	= "Error. No existe el id de usuario en la Base de datos. No se guardó nada";
                responder($response, $mysqli);
            }
			else
			{
				$row_usr 	= $query->data();
				$idSucursal = $row_usr[0]['idSucursal'];
			}
        }
        else
        {
            $response['respuesta'] = "Error en el id de usuario. ".$query->mensaje().". Inténtalo nuevamente";
            responder($response, $mysqli);
        }
        $idProveedor 		= $_POST['proveedor'];
		if(!$idProveedor 	= validarFormulario('i', $idProveedor))
        {
            $response['respuesta'] = "El formato del id del proveedor no es el correcto. Inténtalo nuevamente.<br>Debes elegir un proveedor de la lista. <br> Error: El campo <b>'Proveedor'</b> no puede estar en blanco'";
            responder($response, $mysqli);
        }
        $sql = "SELECT id FROM cat_proveedores WHERE id = ? AND activo = 1 LIMIT 1";
		$params		= array('i',$idProveedor);
		if ($query 	->sentence($sql, $params))
		{
            if($query->num_rows() == 0)
            {
                $response['respuesta'] = "Error. No existe el id <b>($idProveedor)</b> de proveedor en la Base de datos. Posiblemente ya fue eliminado. No se guardó nada";
                responder($response, $mysqli);
            }
            $row_prov 		= $query->data();
            $idProveedor 	= $row_prov[0]['id'];
        }
        else
        {
            $response['respuesta'] = "Error en el id del proveedor. ".$query->mensaje().". Inténtalo nuevamente";
            responder($response, $mysqli);
        }
        $arrayProductos             = json_decode($_POST['arrayProductos']);
        if (sizeof($arrayProductos) == 0)
        {
            $response['respuesta']  = "La lista de productos no puede estar vacía. Agrega al menos un producto para poder guardar la compra";
            responder($response, $mysqli);
        }
        $mysqli->autocommit(FALSE);
        $sql = "INSERT INTO compras (usuario, idProveedor, idSucursal) VALUES (?,?,?)";
		$params = array('iii',$idUsuario, $idProveedor, $idSucursal);
		if ($query ->sentence($sql, $params))
		{
            if($query ->affected_rows() == 0)
            {
				$mysqli ->rollback();
                $response['respuesta']        = "No se modificó nada, no se pudo registrar la compra, inténtalo nuevamente";
                responder($response, $mysqli);
            }
        }
        else
        {
            $response['respuesta'] 	= "Error al registrar la compra. No se pudo guardar la información. ".$query->mensaje().". Inténtalo nuevamente";
            responder($response, $mysqli);
        }

			$idCompra = $query->insert_id();
            foreach ($arrayProductos as $esteProducto)
            {
                $nombreProducto		=   $esteProducto    ->nombre;
                $codigoProducto		=   $esteProducto    ->codigo;
                if (!$idProducto 	= validarFormulario('i',$esteProducto->id, 0))
                {
                    $mysqli->rollback();
                    $response['respuesta'] = "El formato del id <b>$idProducto->$nombreProducto</b> no es el correcto. Ocurrió un rollback. <br>No se guardó nada. <br>Por favor vuelve a intentarlo";
                    $response['status'] = 0;
                    responder($response, $mysqli);
                    break;
                }
                if (!$cantidadProducto = validarFormulario('i',$esteProducto->cantidad, 0))
                {
                    $mysqli->rollback();
                    $response['respuesta'] = "El formato del parámetro 'cantidad' del producto: <b>$idProducto->$nombreProducto</b> no es el correcto y no puede ser menor o igual que cero (0). Ocurrió un rollback. <br>No se guardó nada. <br>Por favor vuelve a intentarlo";
                    $response['status'] = 0;
                    responder($response, $mysqli);
                    break;
                }
                if (!$precioProducto = validarFormulario('i',$esteProducto->precio, 0))
                {
                    $mysqli->rollback();
                    $response['respuesta'] = "El formato del parámetro 'precio' del producto: <b>$idProducto->$nombreProducto</b> no es el correcto y no puede ser menor o igual que cero (0). Ocurrió un rollback. <br>No se guardó nada. <br>Por favor vuelve a intentarlo";
                    $response['status'] = 0;
                    responder($response, $mysqli);
                    break;
                }
                $idServicio 		= 0;
                $sql = "INSERT INTO
                            detalle_compras (idCompra, idProducto, idServicio, precioCompra, cantidad, idSucursal, usuario)
                        VALUES
                            (?,?,?,?,?,?,?)";
				$params				= array('iiidiii',$idCompra, $idProducto, $idServicio, $precioProducto,$cantidadProducto, $idSucursal, $idUsuario);
				if (!$query ->sentence($sql, $params))
				{
					$mysqli ->rollback();
					$response['respuesta'] = "Error en el detalle de la compra. No se pudo guardar la información. Falló el la preparación de parámetros. Inténtalo nuevamente";
					responder($response, $mysqli);
                }
                $sql =  "UPDATE cat_productos SET precioCompra = ? WHERE id = ? LIMIT 1";
				$params	= array('ii',$precioProducto, $idProducto);
				if (!$query ->sentence($sql, $params))
				{
                    $mysqli ->rollback();
                    $response['respuesta'] = "Error al registrar precio del producto. Error en el id de producto: <b>$idProducto</b>. No se pudo guardar la información. Falló la vinculación de parámetros. Inténtalo nuevamente";
                    responder($response, $mysqli);
                }
                $sql =  "SELECT id FROM detalle_existenciasproductos WHERE idProducto = ? AND idSucursal = ?";
				$params = array('ii',$precioProducto, $idProducto);
				if ($query ->sentence($sql, $params))
				{
                    if ($query->num_rows() == 0)
                    {
                        $sql = "INSERT INTO detalle_existenciasproductos
                                    (idProducto, idSucursal, existencias, usuario)
                                VALUES (?,?,?,?)";
						$params	= array('iiii',$idProducto, $idSucursal, $cantidadProducto, $idUsuario);
						if (!$query ->sentence($sql, $params))
						{
							$mysqli ->rollback();
							$response['respuesta'] = "Error al consultar la existencia del producto. ".$query->mensaje().". Error en el id de producto: <b>$idProducto</b>. Inténtalo nuevamente";
							responder($response, $mysqli);
                        }
                    }
                    else
                    {
                        $sql = "UPDATE detalle_existenciasproductos
                                SET existencias = existencias + ?, usuario = ?
                                WHERE idSucursal = ? AND idProducto = ? LIMIT 1";
						$params = array('iiii',$cantidadProducto, $idUsuario, $idSucursal, $idProducto);
						if (!$query ->sentence($sql, $params))
						{
							$mysqli ->rollback();
							$response['respuesta'] = "Error al actualizar la existencia del producto. ".$query->mensaje().". Error en el id de producto: <b>$idProducto</b>. Inténtalo nuevamente";
							responder($response, $mysqli);
                        }
                    }
                }
                else
                {
                    $mysqli->rollback();
                    $response['respuesta'] = "Error al preparar los parámetros de las existencias.".$query->mensaje().". Inténtalo nuevamente";
                    responder($response, $mysqli);
                }
            }
			// Agregar evento en la bitácora de eventos ///////
			$idUsuario 				= $sesion->get("id");
			$ipUsuario 				= $sesion->get("ip");
			$pantalla				= "Agregar compra";
			$descripcion			= "Se agregó una nueva compra con id=$idCompra al catálogo de compras";
			$sql					= "CALL agregarEvento($idUsuario, '$ipUsuario', '$pantalla', '$descripcion', $idSucursal);";
			$mysqli					->query($sql);
			//////////////////////////////////////////////////
            if($mysqli->commit())
            {
                $idTicket               = $idCompra;
                $response['status']     = 1;
                $response['respuesta']  = "La compra se ha generado correctamente. </br>No. de compra: <strong>$idTicket</strong>
                                            </br> <strong><a href='listarCompras.php' class='orange'>Lista de compras</a></strong>
                                            </br> <strong><a target='_blank' href='assets/pdf/comprobanteCompra.php?idCompra=$idTicket' class='orange'>Imprimir</a></strong>";
                responder($response, $mysqli);
            }
            else
            {
                $mysqli->rollback();
                $response['respuesta']  = "Ocurrió un error. No se pudo guardar. Error en commit. Vuelve a intentarlo";
                responder($response, $mysqli);
            }
    }
?>

<?php
    require_once ('../connect/bd.php');
    require_once ("../connect/sesion.class.php");
    $sesion = new sesion();
    require_once ("../connect/cerrarOtrasSesiones.php");
    require_once ("../connect/usuarioLogeado.php");
    require_once ("../php/funcionesVarias.php");
    require_once ("../php/hash.php");
    if( logueado($idSesion,$idUsuario,$mysqli) == false || $idSesion == false)
    {
        header("Location: salir.php");
    }
    else
    {
		require_once ("../php/query.class.php");
		$query = new Query();
        require "../php/responderJSON.php";
        require ("../php/usuario.class.php");
        $usuario = new usuario($idUsuario,$mysqli);
        $permiso = $usuario->permiso("modificarUsuario",$mysqli);
        if (!$permiso)
        {
            $response['mensaje'] = "No se pudo modificar este registro. Usuario con permisos insuficientes para realizar esta acción";
            $response['status'] = 0;
            responder($response, $mysqli);
        }

        $response = array(
            "status"                    => 0
        );

		if (!$nombres = validarFormulario('s',$_POST['nombre'],0))
		{
			$response['mensaje'] = "El campo Nombre no cumple con el formato esperado y no puede estar en blanco";
			$response['focus'] = 'nombre';
			responder($response, $mysqli);
		}
		if (!$apellidop = validarFormulario('s',$_POST['apellidop'],0))
		{
			$response['mensaje'] = "El campo Apellido paterno no puede estar en blanco";
			$response['focus'] = 'apellidop';
			responder($response, $mysqli);
		}
		$apellidom = validarFormulario('s', $_POST['apellidom'], FALSE);
		if (!$direccion1 = validarFormulario('s',$_POST['direccion1'],0))
		{
			$response['mensaje'] = "El campo domicilio no puede estar en blanco";
			$response['focus'] = 'direccion1';
			responder($response, $mysqli);
		}
		if (!$direccion2 = validarFormulario('s',$_POST['direccion2'],0))
		{
			$response['mensaje'] = "El campo domicilio no puede estar en blanco";
			$response['focus'] = 'direccion2';
			responder($response, $mysqli);
		}
		if (!$estado = validarFormulario('i',$_POST['estado']))
		{
			$response['mensaje'] = "El formato del campo estado no es el correcto";
			$response['focus'] = 'estado';
			responder($response, $mysqli);
		}
		$telefono   = validarFormulario('s', $_POST['telefono'], FALSE);
		$celular    = validarFormulario('s', $_POST['celular'], FALSE);
		$email      = validarFormulario('s', $_POST['email'], FALSE);
		if (!$departamento = validarFormulario('i',$_POST['departamento']))
        {
            $response['mensaje'] = "El formato del campo departamento no es el correcto";
            $response['focus'] = 'departamento';
            responder($response, $mysqli);
        }
		if ( $_POST['tasaComision'] > 99 || !$tasaComision = validarFormulario('i',$_POST['tasaComision']))
		{
			$response['mensaje'] = "El formato del campo tasa comisión por ventas no es el correcto y no puede ser menor que cero (0) ni mayor a 99";
			$response['focus'] = 'tasaComision';
			responder($response, $mysqli);
		}
		if ( $_POST['tasaComisionCobranza'] > 99 || !$tasaComisionCobranza = validarFormulario('i',$_POST['tasaComisionCobranza']))
		{
			$response['mensaje'] = "El formato del campo tasa comisión cobranza no es el correcto y no puede ser menor que cero (0) ni mayor a 99";
			$response['focus'] = 'tasaComisionCobranza';
			responder($response, $mysqli);
		}
		if (!$tipo = validarFormulario('i',$_POST['perfil']))
		{
			$response['mensaje'] = "El formato del campo perfil no es el correcto";
			$response['focus'] = 'perfil';
			responder($response, $mysqli);
		}
		$tipo = $tipo > 4 || $tipo < 0 ? 3 : $tipo;
		if (!$nickName = validarFormulario('s',$_POST['nickname'],4))
		{
			$response['mensaje'] = "El campo nick name no puede estar en blanco y debe conformarse por al menos 5 caracteres";
			$response['focus'] = 'nickname';
			responder($response, $mysqli);
		}
        if (!$idUsuario_m = validarFormulario('i',$_POST['hiddenIdProducto']))
        {
            $response['mensaje'] = "El formato del id del usuario no es el correcto";
            $response['focus'] = '';
            responder($response, $mysqli);
        }
		$query  ->table("cat_usuarios")->select("id, nickName")
				->where("nickName", "=", $nickName, "s")->and()
				->where("id", "<>", $idUsuario_m, "i")
				->limit(1)->execute();
        if($query->num_rows())
        {
            $response['mensaje'] = "Error, ya exite un usuario con el nick <b>$nickName</b><br>Elige otro distinto";
            $response['focus'] = 'nickname';
            responder($response, $mysqli);
        }
        //////////////////////////////////// CACHAR CHECKBOX'S PRIVILEGIOS /////////////////////////////////////////////
        $listarContratos        = 1;
        $agregarContrato        = 0;
        $modificarContrato      = 0;
        $eliminarContrato       = 0;
        $listarVentas           = 0;
        $agregarVenta           = 0;
        $modificarVenta         = 0;
        $eliminarVenta          = 0;
        $listarProveedores      = 0;
        $agregarProveedor       = 0;
        $modificarProveedor     = 0;
        $eliminarProveedor      = 0;
        $listarClientes         = 0;
        $agregarCliente         = 0;
        $modificarCliente       = 0;
        $eliminarCliente        = 0;
        $listarDifuntos         = 0;
        $agregarDifunto         = 0;
        $modificarDifunto       = 0;
        $eliminarDifunto        = 0;
        $listarProductos        = 0;
        $agregarProducto        = 0;
        $modificarProducto      = 0;
        $eliminarProducto       = 0;
        $listarServicios        = 0;
        $agregarServicio        = 0;
        $modificarServicio      = 0;
        $eliminarServicio       = 0;
        $listarCompras          = 0;
        $agregarCompra          = 0;
        $modificarCompra        = 0;
        $eliminarCompra         = 0;
        $listarPlanes           = 0;
        $agregarPlan            = 0;
        $modificarPlan          = 0;
        $eliminarPlan           = 0;
        $listarUsuarios         = 0;
        $agregarUsuario         = 0;
        $modificarUsuario       = 0;
        $eliminarUsuario        = 0;

		$listarVariablesSistema = 0;
        $modificarVariablesSistema= 0;

        if (isset($_POST['agregarContrato']))
        {
            $agregarContrato = 1;
            if (isset($_POST['modificarContrato']))
            {
                $modificarContrato = 1;
                if (isset($_POST['eliminarContrato']))
                {
                    $eliminarContrato = 1;
                }
            }
        }
        if (isset($_POST['listarVentas']))
        {
            $listarVentas = 1;
            if (isset($_POST['agregarVenta']))
            {
                $agregarVenta = 1;
                if (isset($_POST['modificarVenta']))
                {
                    $modificarVenta = 1;
                    if (isset($_POST['eliminarVenta']))
                    {
                        $eliminarVenta = 1;
                    }
                }
            }
        }
        if (isset($_POST['listarClientes']))
        {
            $listarClientes = 1;
            if (isset($_POST['agregarCliente']))
            {
                $agregarCliente = 1;
                if (isset($_POST['modificarCliente']))
                {
                    $modificarCliente = 1;
                    if (isset($_POST['eliminarCliente']))
                    {
                        $eliminarCliente = 1;
                    }
                }
            }
        }
        if (isset($_POST['listarProveedores']))
        {
            $listarProveedores = 1;
            if (isset($_POST['agregarProveedor']))
            {
                $agregarProveedor = 1;
                if (isset($_POST['modificarProveedor']))
                {
                    $modificarProveedor = 1;
                    if (isset($_POST['eliminarProveedor']))
                    {
                        $eliminarProveedor = 1;
                    }
                }
            }
        }
        if (isset($_POST['listarDifuntos']))
        {
            $listarDifuntos = 1;
            if (isset($_POST['agregarDifunto']))
            {
                $agregarDifunto = 1;
                if (isset($_POST['modificarDifunto']))
                {
                    $modificarDifunto = 1;
                    if (isset($_POST['eliminarDifunto']))
                    {
                        $eliminarDifunto = 1;
                    }
                }
            }
        }
        if (isset($_POST['listarProductos']))
        {
            $listarProductos = 1;
            if (isset($_POST['agregarProducto']))
            {
                $agregarProducto = 1;
                if (isset($_POST['modificarProducto']))
                {
                    $modificarProducto = 1;
                    if (isset($_POST['eliminarProducto']))
                    {
                        $eliminarProducto = 1;
                    }
                }
            }
        }
        if (isset($_POST['listarServicios']))
        {
            $listarServicios = 1;
            if (isset($_POST['agregarServicio']))
            {
                $agregarServicio = 1;
                if (isset($_POST['modificarServicio']))
                {
                    $modificarServicio = 1;
                    if (isset($_POST['eliminarServicio']))
                    {
                        $eliminarServicio = 1;
                    }
                }
            }
        }
        if (isset($_POST['listarCompras']))
        {
            $listarCompras = 1;
            if (isset($_POST['agregarCompra']))
            {
                $agregarCompra = 1;
                if (isset($_POST['modificarCompra']))
                {
                    $modificarCompra = 1;
                    if (isset($_POST['eliminarCompra']))
                    {
                        $eliminarCompra = 1;
                    }
                }
            }
        }
        if (isset($_POST['listarPlanes']))
        {
            $listarPlanes = 1;
            if (isset($_POST['agregarPlan']))
            {
                $agregarPlan = 1;
                if (isset($_POST['modificarPlan']))
                {
                    $modificarPlan = 1;
                    if (isset($_POST['eliminarPlan']))
                    {
                        $eliminarPlan = 1;
                    }
                }
            }
        }
        if (isset($_POST['listarUsuarios']))
        {
            $listarUsuarios = 1;
            if (isset($_POST['agregarUsuario']))
            {
                $agregarUsuario = 1;
                if (isset($_POST['modificarUsuario']))
                {
                    $modificarUsuario = 1;
                    if (isset($_POST['eliminarUsuario']))
                    {
                        $eliminarUsuario = 1;
                    }
                }
            }
        }
		if (isset($_POST['listarVariablesSistema']))
        {
            $listarVariablesSistema = 1;
			if (isset($_POST['modificarVariablesSistema']))
			{
			    $modificarVariablesSistema = 1;
			}
        }
        //////////////////////////////////////////////////////////////////////////////////////////////////
        $idUsuario      = $sesion->get('id');
		$res_noSucursal = $query->table("cat_usuarios")->select("idSucursal")->where("id", "=", $idUsuario, "i")->limit(1)->execute();
        $idSucursal     = $res_noSucursal[0]['idSucursal'];
        $query			->autocommit(FALSE);
		$query->table("cat_usuarios")->update(compact( "nombres", "apellidop", "apellidom", "direccion1", "direccion2", "estado", "nickName",
														"telefono", "celular", "email", "tipo", "departamento", "tasaComision", "tasaComisionCobranza", "idSucursal"), "sssssissssiiiii")
									 ->where("id", "=", $idUsuario_m, "i")->limit(1)->execute();
		if ($query->status())
		{
			// Agregar evento en la bitácora de eventos ///////
			$ipUsuario 					= $sesion->get("ip");
			$pantalla					= "Agregar/Modificar usuario";
			$nombreInsert				= "$nombres $apellidop $apellidom";
			$descripcion				= "Se modificó un usuario ($nombreInsert) con id=$idUsuario_m.";
			$sql						= "CALL agregarEvento($idUsuario, '$ipUsuario', '$pantalla', '$descripcion', $idSucursal);";
			$mysqli						->query($sql);

			$query ->table("cat_permisos") ->update(compact("listarContratos", "agregarContrato", "modificarContrato", "eliminarContrato",
															"listarVentas", "agregarVenta", "modificarVenta", "eliminarVenta",
															"listarProveedores", "agregarProveedor", "modificarProveedor", "eliminarProveedor",
															"listarClientes", "agregarCliente", "modificarCliente", "eliminarCliente",
															"listarDifuntos", "agregarDifunto", "modificarDifunto", "eliminarDifunto",
															"listarProductos", "agregarProducto", "modificarProducto", "eliminarProducto",
															"listarServicios", "agregarServicio", "modificarServicio", "eliminarServicio",
															"listarCompras", "agregarCompra", "modificarCompra", "eliminarCompra",
															"listarPlanes", "agregarPlan", "modificarPlan", "eliminarPlan",
															"listarUsuarios", "agregarUsuario", "modificarUsuario", "eliminarUsuario",
															"listarVariablesSistema", "modificarVariablesSistema"), "iiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiii")
											->where("idUsuario", "=", $idUsuario_m, "i")->limit(1)->execute();
			if ($query ->status())
			{
                if ($query->commit())
                {
                    $response['mensaje']        = "$nombres $apellidop $apellidom";
                    $response['status']         = 1;
                    responder($response, $mysqli);
                }
                else
                {
                    $query->rollback();
                    $response['mensaje']        = "Error en commit, no se modificó nada, inténtalo nuevamente";
                    responder($response, $mysqli);
                }
            }
            else
            {
                $query->rollback();
                $response['mensaje']        = "Error. No se pudo modificar el detalle del usuario. Falló la preparación de los datos. Vuelve a intentarlo nuevamente";
                responder($response, $mysqli);
            }
        }
        else
        {
            $query->rollback();
            $response['mensaje']        = "Error. No se pudo modificar. Falló la preparación de los datos. Vuelve a intentarlo nuevamente";
            responder($response, $mysqli);
        }
    }
?>

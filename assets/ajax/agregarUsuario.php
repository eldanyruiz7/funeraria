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
        require "../php/responderJSON.php";
        require ("../php/usuario.class.php");
        $usuario = new usuario($idUsuario,$mysqli);
        $permiso = $usuario->permiso("agregarUsuario",$mysqli);
        if (!$permiso)
        {
            $response['mensaje'] = "No se pudo guardar este registro. Usuario con permisos insuficientes para realizar esta acción";
            $response['status'] = 0;
            responder($response, $mysqli);
        }

        $nombres                        = $_POST['nombre'];
        $apellidop                      = $_POST['apellidop'];
        $apellidom                      = $_POST['apellidom'];
        $domicilio1                     = $_POST['direccion1'];
        $domicilio2                     = $_POST['direccion2'];
        $idEstado                       = $_POST['estado'];
        $nickName                       = $_POST['nickname'];
        $telefono                       = $_POST['telefono'];
        $celular                        = $_POST['celular'];
        $email                          = $_POST['email'];
        $tasaComision                   = $_POST['tasaComision'];
        $tipo                           = $_POST['perfil'];
        $password1                      = $_POST['password1'];
        $password2                      = $_POST['password2'];

        $response = array(
            "status"                    => 1
        );

        if (!$nombres = validarFormulario('s',$nombres,0))
        {
            $response['mensaje'] = "El campo Nombre no cumple con el formato esperado y no puede estar en blanco";
            $response['status'] = 0;
            $response['focus'] = 'nombre';
            responder($response, $mysqli);
        }
        if (!$apellidop = validarFormulario('s',$apellidop,0))
        {
            $response['mensaje'] = "El campo Apellido paterno no puede estar en blanco";
            $response['status'] = 0;
            $response['focus'] = 'apellidop';
            responder($response, $mysqli);
        }
        $apellidom = validarFormulario('s', $apellidom, FALSE);
        if (!$domicilio1 = validarFormulario('s',$domicilio1,0))
        {
            $response['mensaje'] = "El campo domicilio no puede estar en blanco";
            $response['status'] = 0;
            $response['focus'] = 'direccion1';
            responder($response, $mysqli);
        }
        if (!$domicilio2 = validarFormulario('s',$domicilio2,0))
        {
            $response['mensaje'] = "El campo domicilio no puede estar en blanco";
            $response['status'] = 0;
            $response['focus'] = 'direccion2';
            responder($response, $mysqli);
        }
        if (!$idEstado = validarFormulario('i',$idEstado))
        {
            $response['mensaje'] = "El formato del campo estado no es el correcto";
            $response['status'] = 0;
            $response['focus'] = 'estado';
            responder($response, $mysqli);
        }
        $telefono   = validarFormulario('s', $telefono, FALSE);
        $celular    = validarFormulario('s', $celular, FALSE);
        $email      = validarFormulario('s', $email, FALSE);
        if ( $tasaComision > 99 || !$tasaComision = validarFormulario('i',$tasaComision))
        {
            $response['mensaje'] = "El formato del campo tasa comisión por ventas no es el correcto y no puede ser menor que cero (0) ni mayor a 99";
            $response['status'] = 0;
            $response['focus'] = 'tasaComision';
            responder($response, $mysqli);
        }
        if (!$tipo = validarFormulario('i',$tipo))
        {
            $response['mensaje'] = "El formato del campo perfil no es el correcto";
            $response['status'] = 0;
            $response['focus'] = 'perfil';
            responder($response, $mysqli);
        }
        $tipo = $tipo > 3 || $tipo < 0 ? 3 : $tipo;
        if (!$nickName = validarFormulario('s',$nickName,4))
        {
            $response['mensaje'] = "El campo nick name no puede estar en blanco y debe conformarse por al menos 5 caracteres";
            $response['status'] = 0;
            $response['focus'] = 'nickname';
            responder($response, $mysqli);
        }
        $sql = "SELECT id, nickName FROM cat_usuarios WHERE nickName = ? LIMIT 1";
        $prepare_usr = $mysqli->prepare($sql);
        if ($prepare_usr &&
            $prepare_usr->bind_param("s",$nickName) &&
            $prepare_usr->execute() &&
            $prepare_usr->store_result() &&
            $prepare_usr->num_rows > 0)
            {
                $response['mensaje'] = "Error, ya exite un usuario con el nick <b>$nickName</b><br>Elige otro distinto";
                $response['status'] = 0;
                $response['focus'] = 'nickname';
                responder($response, $mysqli);
            }
        if (!$password1 = validarFormulario('s',$password1, 5))
        {
            $response['mensaje'] = "El campo Contraseña no puede estar en blanco y debe contener al menos 6 caracteres";
            $response['status'] = 0;
            $response['focus'] = 'password1';
            responder($response, $mysqli);
        }
        if (!$password2 = validarFormulario('s',$password2, 5))
        {
            $response['mensaje'] = "El campo Contraseña no puede estar en blanco y debe contener al menos 6 caracteres";
            $response['status'] = 0;
            $response['focus'] = 'password2';
            responder($response, $mysqli);
        }
        if ($password1 != $password2)
        {
            $response['mensaje'] = "Las contraseñas deben coincidir. Escribe la misma contraseña en ambos campos.";
            $response['status'] = 0;
            $response['focus'] = 'password1';
            responder($response, $mysqli);
        }
        $password 				= new password;
        $hash 					= $password->hash($password1);
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
        $sql            = "SELECT idSucursal FROM cat_usuarios WHERE id = $idUsuario LIMIT 1";
        $res_noSucursal = $mysqli->query($sql);
        $row_noSucursal = $res_noSucursal->fetch_assoc();
        $idSucursal     = $row_noSucursal['idSucursal'];
        $mysqli         ->autocommit(FALSE);
        $sql            = "INSERT INTO cat_usuarios
                                (nombres, apellidop, apellidom, direccion1, direccion2, estado, nickName,
                                    cntrsn, telefono, celular, email, tipo, tasaComision, idSucursal, usuario)
                            VALUES
                                (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
        if($prepare     = $mysqli->prepare($sql))
        {
            if(!$prepare->bind_param('sssssisssssiiii', $nombres, $apellidop, $apellidom, $domicilio1, $domicilio2, $idEstado, $nickName,
                                                        $hash, $telefono, $celular, $email, $tipo, $tasaComision, $idSucursal, $idUsuario))
            {
                $mysqli->rollback();
                $response['mensaje'] = "Error. No se pudo guardar la información. Falló el la vinculación de parámetros. Inténtalo nuevamente";
                $response['status'] = 0;
                responder($response, $mysqli);
            }
            if(!$prepare->execute())
            {
                $mysqli->rollback();
                $response['mensaje'] = "Error. No se pudo guardar la información. Falló el enlace a la base de datos. Inténtalo nuevamente";
                $response['status'] = 0;
                responder($response, $mysqli);
            }
            if($prepare->affected_rows == 0)
            {
                $mysqli->rollback();
                $response['mensaje']        = "No se modificó nada, no se pudo guardar el registro, inténtalo nuevamente";
                $response['status']         = 0;
                responder($response, $mysqli);
            }
            $insert_id                  = $mysqli->insert_id;
			// Agregar evento en la bitácora de eventos ///////
			$idUsuario 					= $sesion->get("id");
			$ipUsuario 					= $sesion->get("ip");
			$pantalla					= "Agregar usuario";
			$nombreInsert				= "$nombres $apellidop $apellidom";
			$descripcion				= "Se agregó un nuevo usuario ($nombreInsert) con id=$insert_id.";
			$sql						= "CALL agregarEvento($idUsuario, '$ipUsuario', '$pantalla', '$descripcion', $idSucursal);";
			$mysqli						->query($sql);
            $sql = "INSERT INTO cat_permisos
                        (idUsuario, listarContratos, agregarContrato, modificarContrato, eliminarContrato,
                        listarVentas, agregarVenta, modificarVenta, eliminarVenta,
                        listarProveedores, agregarProveedor, modificarProveedor, eliminarProveedor,
                        listarClientes, agregarCliente, modificarCliente, eliminarCliente,
                        listarDifuntos, agregarDifunto, modificarDifunto, eliminarDifunto,
                        listarProductos, agregarProducto, modificarProducto, eliminarProducto,
                        listarServicios, agregarServicio, modificarServicio, eliminarServicio,
                        listarCompras, agregarCompra, modificarCompra, eliminarCompra,
                        listarPlanes, agregarPlan, modificarPlan, eliminarPlan,
                        listarUsuarios, agregarUsuario, modificarUsuario, eliminarUsuario,
						listarVariablesSistema, modificarVariablesSistema)
                    VALUES
                        (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
            if($prepare_perm     = $mysqli->prepare($sql))
            {
                if(!$prepare_perm->bind_param('iiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiii', $insert_id,
                        $listarContratos, $agregarContrato, $modificarContrato, $eliminarContrato,
                        $listarVentas, $agregarVenta, $modificarVenta, $eliminarVenta,
                        $listarProveedores, $agregarProveedor, $modificarProveedor, $eliminarProveedor,
                        $listarClientes, $agregarCliente, $modificarCliente, $eliminarCliente,
                        $listarDifuntos, $agregarDifunto, $modificarDifunto, $eliminarDifunto,
                        $listarProductos, $agregarProducto, $modificarProducto, $eliminarProducto,
                        $listarServicios, $agregarServicio, $modificarServicio, $eliminarServicio,
                        $listarCompras, $agregarCompra, $modificarCompra, $eliminarCompra,
                        $listarPlanes, $agregarPlan, $modificarPlan, $eliminarPlan,
                        $listarUsuarios, $agregarUsuario, $modificarUsuario, $eliminarUsuario,
						$listarVariablesSistema, $modificarVariablesSistema))
                {
                    $mysqli->rollback();
                    $response['mensaje'] = "Error. No se pudo guardar la información del detalle del usuario. Falló el la vinculación de parámetros. Inténtalo nuevamente";
                    $response['status'] = 0;
                    responder($response, $mysqli);
                }
                if(!$prepare_perm->execute())
                {
                    $mysqli->rollback();
                    $response['mensaje'] = "Error. No se pudo guardar la información del detalle del usuario. Falló el enlace a la base de datos. Inténtalo nuevamente";
                    $response['status'] = 0;
                    responder($response, $mysqli);
                }
                if($prepare_perm->affected_rows == 0)
                {
                    $mysqli->rollback();
                    $response['mensaje']        = "Error. No se pudo guardar la información del detalle del usuario, inténtalo nuevamente";
                    $response['status']         = 0;
                    responder($response, $mysqli);
                }
                if ($mysqli->commit())
                {
                    $response['mensaje']        = "$nombres $apellidop $apellidom";
                    $response['status']         = 1;
                    responder($response, $mysqli);
                }
                else
                {
                    $mysqli->rollback();
                    $response['mensaje']        = "Error en commit, no se guardó nada, inténtalo nuevamente";
                    $response['status']         = 0;
                    responder($response, $mysqli);
                }
            }
            else
            {
                $mysqli->rollback();
                $response['mensaje']        = "Error. No se pudo guardar el detalle del usuario. Falló la preparación de los datos. Vuelve a intentarlo nuevamente";
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

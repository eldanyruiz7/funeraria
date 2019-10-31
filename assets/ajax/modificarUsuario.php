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
        $permiso = $usuario->permiso("modificarUsuario",$mysqli);
        if (!$permiso)
        {
            $response['mensaje'] = "No se pudo modificar este registro. Usuario con permisos insuficientes para realizar esta acción";
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
        $idUsuario_m                    = $_POST['hiddenIdProducto'];

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
        if (!$idUsuario_m = validarFormulario('i',$idUsuario_m))
        {
            $response['mensaje'] = "El formato del id del usuario no es el correcto";
            $response['status'] = 0;
            $response['focus'] = '';
            responder($response, $mysqli);
        }
        $sql = "SELECT id, nickName FROM cat_usuarios WHERE nickName = ?  AND id <> ? LIMIT 1";
        $prepare_usr = $mysqli->prepare($sql);
        if ($prepare_usr &&
            $prepare_usr->bind_param("si",$nickName,$idUsuario_m) &&
            $prepare_usr->execute() &&
            $prepare_usr->store_result() &&
            $prepare_usr->num_rows > 0)
            {
                $response['mensaje'] = "Error, ya exite un usuario con el nick <b>$nickName</b><br>Elige otro distinto";
                $response['status'] = 0;
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
        //////////////////////////////////////////////////////////////////////////////////////////////////
        $idUsuario      = $sesion->get('id');
        $sql            = "SELECT idSucursal FROM cat_usuarios WHERE id = $idUsuario LIMIT 1";
        $res_noSucursal = $mysqli->query($sql);
        $row_noSucursal = $res_noSucursal->fetch_assoc();
        $idSucursal     = $row_noSucursal['idSucursal'];
        $mysqli         ->autocommit(FALSE);
        $sql            = "UPDATE cat_usuarios
                            SET nombres = ?, apellidop = ?, apellidom = ?, direccion1 = ?, direccion2 = ?, estado = ?, nickName = ?,
                                telefono = ?, celular = ?, email = ?, tipo = ?, tasaComision = ?, idSucursal = ?
                            WHERE id = ? LIMIT 1";
        if($prepare     = $mysqli->prepare($sql))
        {
            if(!$prepare->bind_param('sssssissssiiii', $nombres, $apellidop, $apellidom, $domicilio1, $domicilio2, $idEstado, $nickName,
                                                        $telefono, $celular, $email, $tipo, $tasaComision, $idSucursal, $idUsuario_m))
            {
                $mysqli->rollback();
                $response['mensaje'] = "Error. No se pudo actualizar la información. Falló el la vinculación de parámetros. Inténtalo nuevamente";
                $response['status'] = 0;
                responder($response, $mysqli);
            }
            if(!$prepare->execute())
            {
                $mysqli->rollback();
                $response['mensaje'] = "Error. No se pudo actualizar la información. Falló el enlace a la base de datos. Inténtalo nuevamente";
                $response['status'] = 0;
                responder($response, $mysqli);
            }
			// Agregar evento en la bitácora de eventos ///////
			$ipUsuario 					= $sesion->get("ip");
			$pantalla					= "Agregar/Modificar usuario";
			$nombreInsert				= "$nombres $apellidop $apellidom";
			$descripcion				= "Se modificó un usuario ($nombreInsert) con id=$idUsuario_m.";
			$sql						= "CALL agregarEvento($idUsuario, '$ipUsuario', '$pantalla', '$descripcion', $idSucursal);";
			$mysqli						->query($sql);
            $sql = "UPDATE cat_permisos
                    SET listarContratos = ?, agregarContrato = ?, modificarContrato = ?, eliminarContrato = ?,
                        listarVentas = ?, agregarVenta = ?, modificarVenta = ?, eliminarVenta = ?,
                        listarProveedores = ?, agregarProveedor = ?, modificarProveedor = ?, eliminarProveedor = ?,
                        listarClientes = ?, agregarCliente = ?, modificarCliente = ?, eliminarCliente = ?,
                        listarDifuntos = ?, agregarDifunto = ?, modificarDifunto = ?, eliminarDifunto = ?,
                        listarProductos = ?, agregarProducto = ?, modificarProducto = ?, eliminarProducto = ?,
                        listarServicios = ?, agregarServicio = ?, modificarServicio = ?, eliminarServicio = ?,
                        listarCompras = ?, agregarCompra = ?, modificarCompra = ?, eliminarCompra = ?,
                        listarPlanes = ?, agregarPlan = ?, modificarPlan = ?, eliminarPlan = ?,
                        listarUsuarios = ?, agregarUsuario = ?, modificarUsuario = ?, eliminarUsuario = ?
                    WHERE idUsuario = ? LIMIT 1";
            if($prepare_perm     = $mysqli->prepare($sql))
            {
                if(!$prepare_perm->bind_param('iiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiii',
                        $listarContratos, $agregarContrato, $modificarContrato, $eliminarContrato,
                        $listarVentas, $agregarVenta, $modificarVenta, $eliminarVenta,
                        $listarProveedores, $agregarProveedor, $modificarProveedor, $eliminarProveedor,
                        $listarClientes, $agregarCliente, $modificarCliente, $eliminarCliente,
                        $listarDifuntos, $agregarDifunto, $modificarDifunto, $eliminarDifunto,
                        $listarProductos, $agregarProducto, $modificarProducto, $eliminarProducto,
                        $listarServicios, $agregarServicio, $modificarServicio, $eliminarServicio,
                        $listarCompras, $agregarCompra, $modificarCompra, $eliminarCompra,
                        $listarPlanes, $agregarPlan, $modificarPlan, $eliminarPlan,
                        $listarUsuarios, $agregarUsuario, $modificarUsuario, $eliminarUsuario, $idUsuario_m))
                {
                    $mysqli->rollback();
                    $response['mensaje'] = "Error. No se pudo modificar la información del detalle del usuario. Falló el la vinculación de parámetros. Inténtalo nuevamente";
                    $response['status'] = 0;
                    responder($response, $mysqli);
                }
                if(!$prepare_perm->execute())
                {
                    $mysqli->rollback();
                    $response['mensaje'] = "Error. No se pudo modificar la información del detalle del usuario. Falló el enlace a la base de datos. Inténtalo nuevamente";
                    $response['status'] = 0;
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
                    $response['mensaje']        = "Error en commit, no se modificó nada, inténtalo nuevamente";
                    $response['status']         = 0;
                    responder($response, $mysqli);
                }
            }
            else
            {
                $mysqli->rollback();
                $response['mensaje']        = "Error. No se pudo modificar el detalle del usuario. Falló la preparación de los datos. Vuelve a intentarlo nuevamente";
                $response['status']         = 0;
                responder($response, $mysqli);
            }
        }
        else
        {
            $mysqli->rollback();
            $response['mensaje']        = "Error. No se pudo modificar. Falló la preparación de los datos. Vuelve a intentarlo nuevamente";
            $response['status']         = 0;
            responder($response, $mysqli);
        }
    }
?>

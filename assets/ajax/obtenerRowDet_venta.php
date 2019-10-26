<?php
    require_once ('../connect/bd.php');
    require_once ("../connect/sesion.class.php");
    $sesion = new sesion();
    require_once ("../connect/cerrarOtrasSesiones.php");
    require_once ("../connect/usuarioLogeado.php");
    if( logueado($idSesion,$idUsuario,$mysqli) == false || $idSesion == false)
    {
        header("Location: salir.php");
    }
    else
    {
        require "../php/responderJSON.php";
        require ("../php/usuario.class.php");
        $usuario = new usuario($idUsuario,$mysqli);
        $permiso = $usuario->permiso("listarVentas",$mysqli);
        if (!$permiso)
        {
            $response['mensaje'] = "No se pudo mostrar este registro. Usuario con permisos insuficientes para realizar esta acción";
            $response['status'] = 0;
            responder($response, $mysqli);
        }

        $idVenta = $_POST['idCliente'];
        // $idSucursal = $_POST['idSucursal'];

        $response = array(
            "status"        => 1
        );
        if(is_numeric($idVenta) == FALSE && $idVenta <= 0)
        {
            $response['mensaje'] = "El formato del id de la venta  no es el correcto";
            $response['status'] = 0;
            responder($response, $mysqli);
        }
        // if(is_numeric($idSucursal) == FALSE && $idSucursal <= 0)
        // {
        //     $response['mensaje'] = "El formato del id de la sucursal no es el correcto";
        //     $response['status'] = 0;
        //     responder($response, $mysqli);
        // }
        $sql = "SELECT id FROM ventas WHERE id = ?";
        if ($prepare_venta = $mysqli->prepare($sql))
        {
            if (!$prepare_venta->bind_param('i', $idVenta))
            {
                $response['mensaje'] = "Error al enlazar los parámetros del Id de la venta. Por favor vuelve a intentarlo";
                $response['status'] = 0;
                responder($response, $mysqli);
            }
            if (!$prepare_venta->execute())
            {
                $response['mensaje'] = "Error al ejecutar los parámetros del Id de la venta. Por favor vuelve a intentarlo";
                $response['status'] = 0;
                responder($response, $mysqli);
            }
            $res_venta = $prepare_venta->get_result();
            if ($res_venta->num_rows == 0)
            {
                $response['mensaje'] = "No existe informaci&oacute;n para esta venta. Posiblemente ya ha sido eliminada o cancelada.";
                $response['status'] = 0;
                responder($response, $mysqli);
            }
            else
            {
                $row_venta = $res_venta->fetch_assoc();
            }
        }
        else
        {
            $response['mensaje'] = "Error al preparar los parámetros del Id de la venta. Por favor vuelve a intentarlo";
            $response['status'] = 0;
            responder($response, $mysqli);
        }
        $sql = "SELECT
                    detalle_ventas.idProducto               AS idProducto,
                    detalle_ventas.cantidad                 AS cantidad,
                    detalle_ventas.precioVenta              AS precio,
                    detalle_ventas.fechaCreacion            AS fechaCreacion,
                    detalle_ventas.activo                   AS activo,
                    cat_productos.nombre                	AS nombreProducto,
                    cat_usuarios.nombres                    AS nombreUsuario,
                    cat_usuarios.apellidop                  AS apellidopUsuario
                FROM detalle_ventas
                INNER JOIN cat_productos
                ON detalle_ventas.idProducto = cat_productos.id
                INNER JOIN cat_usuarios
                ON detalle_ventas.usuario = cat_usuarios.id
                WHERE detalle_ventas.idVenta = ?";
        if($prepare         = $mysqli->prepare($sql))
        {
            $prepare        ->bind_param('i', $idVenta);
            $prepare        ->execute();
            $res_prod        = $prepare->get_result();
        }
        else
        {
            $response['mensaje'] = "Ocurrió un error en la consulta de la lista de productos a la Base de datos.";
            $response['status'] = 0;
            responder($response, $mysqli);
        }
        $sql = "SELECT
                    detalle_ventas.idServicio           AS idProducto,
                    detalle_ventas.cantidad             AS cantidad,
                    detalle_ventas.precioVenta               AS precio,
                    detalle_ventas.fechaCreacion        AS fechaCreacion,
                    detalle_ventas.activo               AS activo,
                    cat_servicios.nombre                	AS nombreProducto,
                    cat_usuarios.nombres                    AS nombreUsuario,
                    cat_usuarios.apellidop                  AS apellidopUsuario
                FROM detalle_ventas
                INNER JOIN cat_servicios
                ON detalle_ventas.idServicio = cat_servicios.id
                INNER JOIN cat_usuarios
                ON detalle_ventas.usuario = cat_usuarios.id
                WHERE detalle_ventas.idVenta = ?";
        if($prepare_serv         = $mysqli->prepare($sql))
        {
            $prepare_serv   ->bind_param('i', $idVenta);
            $prepare_serv   ->execute();
            $res_serv       = $prepare_serv->get_result();
        }
        else
        {
            $response['mensaje'] = "Ocurrió un error en la consulta de la lista de servicios a la Base de datos.";
            $response['status'] = 0;
            responder($response, $mysqli);
        }

        $response['htmlDetalle']        = "";
        $response['htmlDetalle_hist']   = "";
        $total = 0;
        while ($row_prod = $res_prod->fetch_assoc())
        {
            $activo             = ($row_prod['activo']) ? TRUE : FALSE;
            $fechaReg           = date_create($row_prod['fechaCreacion']);
            $fechaReg           = date_format($fechaReg, 'd/m/Y h:i:s p');
            $idProducto         = $row_prod['idProducto'];
            $cantidad           = $row_prod['cantidad'];
            $precio             = $row_prod['precio'];
            $subTotal           = $cantidad * $precio;
            $nombre             = $row_prod['nombreProducto'];
            $nombreUsuario      = $row_prod['nombreUsuario']." ".$row_prod['apellidopUsuario'];

            $tipo               = ' <span class="label label-info label-white">
                                        <i class="ace-icon fa fa-tag bigger-120"></i>
                                        Producto
                                    </span>';

            if ($activo)
            {
                $response['htmlDetalle']    .= "<tr>
                                                    <td>$idProducto</td>
                                                    <td>$nombre</td>
                                                    <td class='text-center'>$tipo</td>
                                                    <td class='text-right'>$".number_format($precio,2,".",",")."</td>
                                                    <td class='text-right'>$cantidad</td>
                                                    <td class='text-right'>$".number_format($subTotal,2,".",",")."</td>
                                                </tr>";
            }
            else
            {
                $response['htmlDetalle_hist'] .= "<tr>
                                                    <td>$idProducto</td>
                                                    <td>$nombre</td>
                                                    <td class='text-center'>$tipo</td>
                                                    <td class='text-right'>$".number_format($precio,2,".",",")."</td>
                                                    <td class='text-right'>$cantidad</td>
                                                    <td class='text-right'>$".number_format($subTotal,2,".",",")."</td>
                                                    <td class='text-center'>$fechaReg</td>
                                                    <td>$nombreUsuario</td>
                                                </tr>";
            }
        }
        while ($row_serv = $res_serv->fetch_assoc())
        {
            $activo             = ($row_serv['activo']) ? TRUE : FALSE;
            $fechaReg           = date_create($row_serv['fechaCreacion']);
            $fechaReg           = date_format($fechaReg, 'd/m/Y h:i:s p');
            $idProducto         = $row_serv['idProducto'];
            $cantidad           = $row_serv['cantidad'];
            $precio             = $row_serv['precio'];
            $subTotal           = $cantidad * $precio;
            $nombre             = $row_serv['nombreProducto'];
            $nombreUsuario      = $row_serv['nombreUsuario']." ".$row_serv['apellidopUsuario'];

            $tipo               = ' <span class="label label-purple label-white">
										<i class="ace-icon fa fa-cube bigger-120"></i>
										Servicio
									</span>';

            if ($activo)
            {
                $response['htmlDetalle']    .= "<tr>
                                                    <td>$idProducto</td>
                                                    <td>$nombre</td>
                                                    <td class='text-center'>$tipo</td>
                                                    <td class='text-right'>$".number_format($precio,2,".",",")."</td>
                                                    <td class='text-right'>$cantidad</td>
                                                    <td class='text-right'>$".number_format($subTotal,2,".",",")."</td>
                                                </tr>";
            }
            else
            {
                $response['htmlDetalle_hist'] .= "<tr>
                                                    <td>$idProducto</td>
                                                    <td>$nombre</td>
                                                    <td class='text-center'>$tipo</td>
                                                    <td class='text-right'>$".number_format($precio,2,".",",")."</td>
                                                    <td class='text-right'>$cantidad</td>
                                                    <td class='text-right'>$".number_format($subTotal,2,".",",")."</td>
                                                    <td class='text-center'>$fechaReg</td>
                                                    <td>$nombreUsuario</td>
                                                </tr>";
            }
        }
        $response['status']                 = 1;
        $response['id']                 = $row_venta['id'];
        responder($response, $mysqli);
    }
?>

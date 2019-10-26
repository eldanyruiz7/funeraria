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
        $permiso = $usuario->permiso("listarPlanes",$mysqli);
        if (!$permiso)
        {
            $response['mensaje'] = "No se pudo mostrar este registro. Usuario con permisos insuficientes para realizar esta acción";
            $response['status'] = 0;
            responder($response, $mysqli);
        }

        $idPlan = $_POST['idCliente'];
        $idSucursal = $_POST['idSucursal'];

        $response = array(
            "status"        => 1
        );
        if(is_numeric($idPlan) == FALSE && $idPlan <= 0)
        {
            $response['mensaje'] = "El formato del id del plan no es el correcto";
            $response['status'] = 0;
            responder($response, $mysqli);
        }
        if(is_numeric($idSucursal) == FALSE && $idSucursal <= 0)
        {
            $response['mensaje'] = "El formato del id de la sucursal no es el correcto";
            $response['status'] = 0;
            responder($response, $mysqli);
        }
        $sql = "SELECT id, imagen FROM cat_planes WHERE id = ? AND activo = 1";
        if ($prepare_plan = $mysqli->prepare($sql))
        {
            if (!$prepare_plan->bind_param('i', $idPlan))
            {
                $response['mensaje'] = "Error al enlazar los parámetros del Id del plan. Por favor vuelve a intentarlo";
                $response['status'] = 0;
                responder($response, $mysqli);
            }
            if (!$prepare_plan->execute())
            {
                $response['mensaje'] = "Error al ejecutar los parámetros del Id del plan. Por favor vuelve a intentarlo";
                $response['status'] = 0;
                responder($response, $mysqli);
            }
            $res_plan = $prepare_plan->get_result();
            if ($res_plan->num_rows == 0)
            {
                $response['mensaje'] = "No existe informaci&oacute;n para este plan funerario. Posiblemente ya ha sido eliminado.";
                $response['status'] = 0;
                responder($response, $mysqli);
            }
            else
            {
                $row_plan = $res_plan->fetch_assoc();
            }
        }
        else
        {
            $response['mensaje'] = "Error al preparar los parámetros del Id del plan. Por favor vuelve a intentarlo";
            $response['status'] = 0;
            responder($response, $mysqli);
        }
        $sql = "SELECT
                    detalle_cat_planes.idProducto           AS idProducto,
                    detalle_cat_planes.cantidad             AS cantidad,
                    detalle_cat_planes.precio               AS precio,
                    detalle_cat_planes.fechaCreacion        AS fechaCreacion,
                    detalle_cat_planes.activo               AS activo,
                    cat_productos.nombre                	AS nombreProducto,
                    cat_usuarios.nombres                    AS nombreUsuario,
                    cat_usuarios.apellidop                  AS apellidopUsuario
                FROM detalle_cat_planes
                INNER JOIN cat_productos
                ON detalle_cat_planes.idProducto = cat_productos.id
                INNER JOIN cat_usuarios
                ON detalle_cat_planes.usuario = cat_usuarios.id
                WHERE detalle_cat_planes.idPlan = ?
                AND detalle_cat_planes.idSucursal = ?";
        if($prepare         = $mysqli->prepare($sql))
        {
            $prepare        ->bind_param('ii', $idPlan, $idSucursal);
            $prepare        ->execute();
            $res_prod        = $prepare->get_result();
        }
        else
        {
            $response['mensaje'] = "Ocurrió un error en la consulta de la lista de productos a la Base de datos.".$mysqli->errno . " " . $mysqli->error;
            $response['status'] = 0;
            responder($response, $mysqli);
        }
        $sql = "SELECT
                    detalle_cat_planes.idServicio           AS idProducto,
                    detalle_cat_planes.cantidad             AS cantidad,
                    detalle_cat_planes.precio               AS precio,
                    detalle_cat_planes.fechaCreacion        AS fechaCreacion,
                    detalle_cat_planes.activo               AS activo,
                    cat_servicios.nombre                	AS nombreProducto,
                    cat_usuarios.nombres                    AS nombreUsuario,
                    cat_usuarios.apellidop                  AS apellidopUsuario
                FROM detalle_cat_planes
                INNER JOIN cat_servicios
                ON detalle_cat_planes.idServicio = cat_servicios.id
                INNER JOIN cat_usuarios
                ON detalle_cat_planes.usuario = cat_usuarios.id
                WHERE detalle_cat_planes.idPlan = ?
                AND detalle_cat_planes.idSucursal = ?";
        if($prepare_serv         = $mysqli->prepare($sql))
        {
            $prepare_serv   ->bind_param('ii', $idPlan, $idSucursal);
            $prepare_serv   ->execute();
            $res_serv       = $prepare_serv->get_result();
        }
        else
        {
            $response['mensaje'] = "Ocurrió un error en la consulta de la lista de servicios a la Base de datos.".$mysqli->errno . " " . $mysqli->error;
            $response['status'] = 0;
            responder($response, $mysqli);
        }
        if (strlen($row_plan['imagen'] > 0))
        {

            $im = file_get_contents("../images/avatars/planes/".$row_plan['imagen'].".jpg");
            $imdata = base64_encode($im);
            $imgSrc                         = "data:image/jpeg;base64,$imdata";
        }
        else
        {
            $imgSrc = "";
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
                                                    <td class='text-right'>$$precio</td>
                                                    <td class='text-right'>$cantidad</td>
                                                    <td class='text-right'>$$subTotal</td>
                                                </tr>";
            }
            else
            {
                $response['htmlDetalle_hist'] .= "<tr>
                                                    <td>$idProducto</td>
                                                    <td>$nombre</td>
                                                    <td class='text-center'>$tipo</td>
                                                    <td class='text-right'>$$precio</td>
                                                    <td class='text-right'>$cantidad</td>
                                                    <td class='text-right'>$$subTotal</td>
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
                                                    <td class='text-right'>$$precio</td>
                                                    <td class='text-right'>$cantidad</td>
                                                    <td class='text-right'>$$subTotal</td>
                                                </tr>";
            }
            else
            {
                $response['htmlDetalle_hist'] .= "<tr>
                                                    <td>$idProducto</td>
                                                    <td>$nombre</td>
                                                    <td class='text-center'>$tipo</td>
                                                    <td class='text-right'>$$precio</td>
                                                    <td class='text-right'>$cantidad</td>
                                                    <td class='text-right'>$$subTotal</td>
                                                    <td class='text-center'>$fechaReg</td>
                                                    <td>$nombreUsuario</td>
                                                </tr>";
            }
        }
        $response['imgSrc']             = $imgSrc;
        $response['status']                 = 1;
        $response['id']                 = $row_plan['id'];
        responder($response, $mysqli);
    }
?>

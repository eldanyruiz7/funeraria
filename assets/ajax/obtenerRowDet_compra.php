<?php
    // error_reporting(E_ALL);
    // ini_set('display_errors', '1');
    require_once ('../connect/bd.php');
    require_once ("../connect/sesion.class.php");
    $sesion = new sesion();
    require_once ("../connect/cerrarOtrasSesiones.php");
    require_once ("../connect/usuarioLogeado.php");
    if( logueado($idSesion,$idUsuario,$mysqli) == false || $idSesion == false)
    {
		header("Location: ".dirname(__FILE__)."../../salir.php");
    }
    else
    {
        require "../php/responderJSON.php";
        require ("../php/usuario.class.php");
        $usuario = new usuario($idUsuario,$mysqli);
        $permiso = $usuario->permiso("listarCompras",$mysqli);
        if (!$permiso)
        {
            $response['mensaje'] = "No se pudo mostrar este registro. Usuario con permisos insuficientes para realizar esta acción";
            $response['status'] = 0;
            responder($response, $mysqli);
        }
        $response = array(
            "status"        => 1
        );
        $idCompra = $_POST['idCliente'];
        $sql = "SELECT
                    detalle_compras.activo              AS activo,
                    detalle_compras.idProducto          AS idProducto,
                    detalle_compras.precioCompra        AS precioCompra,
                    detalle_compras.cantidad            AS cantidad,
                    detalle_compras.fechaCreacion       AS fechaCreacion,
                    cat_usuarios.nombres                AS nombreUsuario,
                    cat_usuarios.apellidop              AS apellidopUsuario,
                    cat_productos.nombre                AS nombre
                FROM detalle_compras
                INNER JOIN cat_productos
                ON detalle_compras.idProducto = cat_productos.id
                INNER JOIN cat_usuarios
                ON detalle_compras.usuario = cat_usuarios.id
                WHERE detalle_compras.idCompra = ?";
        if ($prepare = $mysqli->prepare($sql))
        {
            if (!$prepare->bind_param('i',$idCompra))
            {
                $response['mensaje'] = "Error. No se pudo consultar la información. Falló el la vinculación de parámetros. Inténtalo nuevamente";
                $response['status'] = 0;
                responder($response, $mysqli);
            }
            if (!$prepare->execute())
            {
                $response['mensaje'] = "Error. No se pudo consultar la información. Falló el enlace a la base de datos. Inténtalo nuevamente";
                $response['status'] = 0;
                responder($response, $mysqli);
            }
            $res                    = $prepare->get_result();
            if ($res->num_rows == 0)
            {
                $response['idCompra'] = $idCompra;
                $response['status'] = 0;
                $response['mensaje'] = "No existe información para esta compra. Posiblemente ya haya sido eliminada del sistema";
                responder($response, $mysqli);
            }
            else
            {
                $response['htmlDetalle']        = "";
                $response['htmlDetalle_hist']   = "";

                while($row                      = $res->fetch_assoc())
                {
                    $activo                     = ($row['activo']) ? TRUE : FALSE;
                    $idProducto                 = $row['idProducto'];
                    $nombreProducto             = $row['nombre'];
                    $precioCompra               = number_format($row['precioCompra'],2,".",",");
                    $cantidad                   = $row['cantidad'];
                    $subTotal                   = number_format($row['cantidad'] * $row['precioCompra'],2,".",",");
                    $fechaReg                   = date_create($row['fechaCreacion']);
                    $fechaReg                   = date_format($fechaReg, 'd/m/Y H:i:s');
                    $usuario                    = $row['nombreUsuario']." ".$row['apellidopUsuario'];
                    if ($activo)
                    {
                        $response['htmlDetalle']    .= "<tr>
                                                            <td>$idProducto</td>
                                                            <td>$nombreProducto</td>
                                                            <td class='text-right'>$$precioCompra</td>
                                                            <td class='text-right'>$cantidad</td>
                                                            <td class='text-right'>$$subTotal</td>
                                                        </tr>";
                    }
                    else
                    {
                        $response['htmlDetalle_hist'] .= "<tr>
                                                            <td>$idProducto</td>
                                                            <td>$nombreProducto</td>
                                                            <td class='text-right'>$$precioCompra</td>
                                                            <td class='text-right'>$cantidad</td>
                                                            <td class='text-right'>$$subTotal</td>
                                                            <td class='text-center'>$fechaReg</td>
                                                            <td>$usuario</td>
                                                        </tr>";
                    }
                }
                $response['status']                 = 1;
                responder($response, $mysqli);
            }
        }
        else
        {
            $response['mensaje'] = "Error. No se pudo consultar la información. Error en la consulta. Inténtalo nuevamente".$mysqli->errno . " " . $mysqli->error;
            $response['status'] = 0;
            responder($response, $mysqli);
        }
    }
?>

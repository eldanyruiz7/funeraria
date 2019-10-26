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
        header("Location: salir.php");
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
        $idProducto = $_POST['idProducto'];
        if (isset($_POST['agregarPlan']) && $_POST['agregarPlan'] == 1)
        {
            $agregarPlan = TRUE;
        }
        else {
            $agregarPlan = FALSE;
        }
        if (isset($_POST['servicio'])&& $_POST['servicio'] == 1)
        {

            $sql = "SELECT
                        cat_servicios.id                    AS idServicio,
                        cat_servicios.nombre                AS nombreSericio,
                        cat_servicios.precio                AS precioVenta
                    FROM cat_servicios
                    WHERE cat_servicios.id = ? LIMIT 1";
            if ($prepare = $mysqli->prepare($sql))
            {
                if (!$prepare->bind_param('i',$idProducto))
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
                // $prepare->store_result();
                $res                    = $prepare->get_result();
                if ($res->num_rows == 0)
                {
                    $response['status'] = 0;
                    $response['respuesta'] = "El servicio ya no existe. Posiblemente ya haya sido eliminado del sistema";
                    responder($response, $mysqli);
                }
                else
                {
                    $row                    = $res->fetch_assoc();
                    $idProducto_            = $row['idServicio'];
                    $nombreProducto         = $row['nombreSericio'];
                    $precioVenta            = number_format($row['precioVenta'],2,".",",");
                    $precioCompra           = '--';
                    $response['status']     = 1;
                    $response['idProducto'] = $idProducto_;
                    $response['servicio']   = 1;
                    $response['nombreProd'] = $nombreProducto;
                    $response['precioVenta'] = $row['precioVenta'];
                    $response['precioCompra'] = $precioCompra;
                    $response['productoOserv'] =    '<span class="label label-info label-white">
                                                        <i class="ace-icon fa fa-tag bigger-120"></i>
                                                        Producto
                                                    </span>';
                    $subTotal = $row['precioVenta'] * 1;
                    $response['rowProd']    = " <tr class='trProductoAgregar' servicio='1' name='$idProducto_'>
                                                    <td> $idProducto_</td>
                                                    <td> $nombreProducto</td>
                                                    <td class='text-right'> $<input type='number' class='text-right inputP_Compra' style='width:80px;border-style:hidden' min='1' step='1' value='".$subTotal."'/></td>
                                                    <td class='text-right'>
                                                        <input type='number' class='text-right inputCantidad' min='1' value='1' style='width:80px;border-style:hidden'>
                                                    </td>
                                                    <td class='text-right'> $<span class='spanSubTotal'>".$subTotal."</span></td>

                                                    <td class='text-center pointer tdEliminarProd' servicio='1' data-rel='tooltip' title='Quitar de esta lista' idProd='$idProducto_'> <i class='fa fa-times red bigger-160' aria-hidden='true'></i></td>
                                                </tr>";
                    responder($response, $mysqli);
                }
            }
            else
            {
                $response['mensaje'] = "Error. No se pudo consultar la información. Error en la consulta. Inténtalo nuevamente".$mysqli->errno . " " . $mysqli->error;
                $response['status'] = 0;
                $response['idProducto'] = "$sql";
                responder($response, $mysqli);
            }
        }
        else
        {

            $sql = "SELECT
                        cat_productos.id                    AS idProducto,
                        cat_productos.nombre                AS nombreProducto,
                        cat_productos.precio                AS precioVenta,
                        cat_productos.precioCompra          AS precioCompra
                    FROM cat_productos
                    WHERE cat_productos.id = ? LIMIT 1";
            if ($prepare = $mysqli->prepare($sql))
            {
                if (!$prepare->bind_param('i',$idProducto))
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
                // $prepare->store_result();
                $res                    = $prepare->get_result();
                if ($res->num_rows == 0)
                {
                    $response['status'] = 0;
                    $response['respuesta'] = "El producto ya no existe. Posiblemente ya haya sido eliminado del sistema";
                    responder($response, $mysqli);
                }
                else
                {
                    $row                    = $res->fetch_assoc();
                    $idProducto_            = $row['idProducto'];
                    $nombreProducto         = $row['nombreProducto'];
                    $precioVenta            = number_format($row['precioVenta'],2,".",",");
                    $precioCompra           = number_format($row['precioCompra'],2,".","");
                    $response['status']     = 1;
                    $response['idProducto'] = $idProducto_;
                    $response['servicio']   = 0;
                    $response['nombreProd'] = $nombreProducto;
                    $response['precioVenta'] = $row['precioVenta'];
                    $response['precioCompra'] = $row['precioCompra'];
                    $response['productoOserv'] =    '<span class="label label-info label-white">
                                                        <i class="ace-icon fa fa-tag bigger-120"></i>
                                                        Producto
                                                    </span>';
                    if ($agregarPlan)
                        $subTotal = $row['precioVenta'] * 1;
                    else
                        $subTotal = $row['precioCompra'] * 1;
                    $response['rowProd']    = " <tr class='trProductoAgregar' servicio='0' name='$idProducto_'>
                                                    <td> $idProducto_</td>
                                                    <td> $nombreProducto</td>
                                                    <td class='text-right'> $<input type='number' class='text-right inputP_Compra' style='width:80px;border-style:hidden' min='1' step='1' value='".$subTotal."'/></td>
                                                    <td class='text-right'>
                                                        <input type='number' class='text-right inputCantidad' min='1' value='1' style='width:80px;border-style:hidden'>
                                                    </td>
                                                    <td class='text-right'> $<span class='spanSubTotal'>".$subTotal."</span></td>

                                                    <td class='text-center pointer tdEliminarProd' servicio='0' data-rel='tooltip' title='Quitar de esta lista' idProd='$idProducto_'> <i class='fa fa-times red bigger-160' aria-hidden='true'></i></td>
                                                </tr>";
                    responder($response, $mysqli);
                }
            }
            else
            {
                $response['mensaje'] = "Error. No se pudo consultar la información. Error en la consulta. Inténtalo nuevamente".$mysqli->errno . " " . $mysqli->error;
                $response['status'] = 0;
                $response['idProducto'] = "$sql";
                responder($response, $mysqli);
            }
        }
    }
?>

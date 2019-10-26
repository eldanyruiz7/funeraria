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
        $response = array(
            "status"        => 1
        );
        $num_servicios = 0;
        if (isset($_POST['listarServicios']))
        {
            $listarServicios = ($_POST['listarServicios'] == 1) ? TRUE : FALSE;
            if ($listarServicios)
            {
                $sql = "SELECT
                                cat_servicios.id AS idServicio,
                                cat_servicios.nombre AS nombreServicio,
                                cat_servicios.precio AS precioServicio
                        FROM cat_servicios
                        WHERE cat_servicios.activo = 1";
                $res_servicios = $mysqli->query($sql);
                $num_servicios = $res_servicios->num_rows;
                while ($row = $res_servicios->fetch_assoc())
                {

                    $InfoData[] = array(
                        'id'                => $row['idServicio'],
                        'servicio'          => 1,
                        'nombreProducto'    => $row['nombreServicio'],
                        'existencias'       => '--',
                        'productoOserv'     => '<span class="label label-purple label-white">
    												<i class="ace-icon fa fa-cube bigger-120"></i>
    												Servicio
    											</span>',
                        'precioVenta'       => "$".number_format($row['precioServicio'],2,".",","),
                        'precioCompra'      => '--',
                        'btns'              => '<div class="hidden-sm hidden-xs action-buttons">
                                                    <a class="blue pointer aAgregarProd" servicio="1" name="'.$row['idServicio'].'" data-rel="tooltip" title="Agregar producto">
                                                        <i class="ace-icon fa fa-arrow-down bigger-130"></i>
                                                    </a>
                								</div>');
                }
            }
        }
        $sql = "SELECT
                    cat_productos.id                            AS idProducto_,
                    cat_productos.nombre                        AS nombreProducto,
                    cat_productos.precio                        AS precioVenta,
                    cat_productos.precioCompra                  AS precioCompra,
                    (SELECT IFNULL(SUM(detalle_existenciasproductos.existencias),0)
                     FROM detalle_existenciasproductos
                     WHERE detalle_existenciasproductos.idProducto = idProducto_)
                                                                AS existencias
                FROM cat_productos
                WHERE cat_productos.activo = 1";
        $res_productos = $mysqli->query($sql);
        $num_productos = $res_productos->num_rows;
        if ($num_productos == 0 && $num_servicios == 0)
        {
            $json_data = [
                "data"   => 0
            ];
        }
        else
        {
            while ($row = $res_productos->fetch_assoc())
            {

                $InfoData[] = array(
                    'id'                => $row['idProducto_'],
                    'servicio'          => 0,
                    'nombreProducto'    => $row['nombreProducto'],
                    'existencias'       => $row['existencias'],
                    'productoOserv'     => '<span class="label label-info label-white">
                                                <i class="ace-icon fa fa-tag bigger-120"></i>
                                                Producto
                                            </span>',
                    'precioVenta'       => "$".number_format($row['precioVenta'],2,".",","),
                    'precioCompra'      => "$".number_format($row['precioCompra'],2,".",","),
                    'btns'              => '<div class="hidden-sm hidden-xs action-buttons">
                                                <a class="blue pointer aAgregarProd" servicio="0" name="'.$row['idProducto_'].'" data-rel="tooltip" title="Agregar producto">
                                                    <i class="ace-icon fa fa-arrow-down bigger-130"></i>
                                                </a>
            								</div>');
            }
        //$data[] = $InfoData;
            $json_data = [
                "data"   => $InfoData
            ];
        }
        echo json_encode($json_data);
    }
?>

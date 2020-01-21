<?php
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
        $permiso = $usuario->permiso("listarVentas",$mysqli);
        if (!$permiso)
        {
            $json_data = [
                "data"   => 0
            ];
            echo json_encode($json_data);
            die;
        }
        $response = array(
            "status"        => 1
        );
        $fInicio = $_GET['fechaInicio'];
        $fInicio_e = explode('-',$fInicio);
        $Y_ini = intval($fInicio_e[0]);
        $m_ini = intval($fInicio_e[1]);
        $d_ini = intval($fInicio_e[2]);
        // var_dump($_GET);
        $fFin = $_GET['fechaFin'];
        $fFin_e = explode('-',$fFin);
        $Y_fin = intval($fFin_e[0]);
        $m_fin = intval($fFin_e[1]);
        $d_fin = intval($fFin_e[2]);
        // var_dump($fInicio_e);
        if(checkdate($m_ini,$d_ini,$Y_ini) == FALSE || checkdate($m_fin,$d_fin,$Y_fin) == FALSE)
        {
            // $InfoData = [
            //     "data"   => 0
            // ];
            $json_data = [
                "data"   => 0
            ];
            echo json_encode($json_data);
            die;
        }
        $fInicio       .= " 00:00:00";
        $fFin          .= " 23:59:59";
        $sql = "SELECT
                    ventas.activo               AS activo,
                    ventas.id                   AS idVenta,
                    ventas.fechaCreacion        AS fechaCreacion,
                    ventas.idSucursal           AS idSucursal,
                    ventas.idFactura            AS idFactura,
                    cat_usuarios.nombres        AS nombreUsuario,
                    cat_usuarios.apellidop      AS apellidopUsuario,
                    cat_sucursales.nombre       AS nombreSucursal,
                    cat_sucursales.direccion2   AS direccionSucursal,
                    clientes.nombres            AS nombreCliente,
                    clientes.apellidop          AS apellidopCliente,
                    clientes.apellidom          AS apellidomCliente

                FROM ventas
                INNER JOIN cat_usuarios
                ON ventas.usuario = cat_usuarios.id
                INNER JOIN cat_sucursales
                ON ventas.idSucursal = cat_sucursales.id
                INNER JOIN clientes
                ON ventas.idCliente = clientes.id
                WHERE ventas.fechaCreacion BETWEEN '$fInicio' AND '$fFin'";

        $res_ = $mysqli->query($sql);
        $num = $res_->num_rows;
        if ($num == 0)
        {
            $json_data = [
                "data"   => 0
            ];
        }
        else
        {
            while ($row = $res_->fetch_assoc())
            {
                $idVenta = $row['idVenta'];
                $sql = "SELECT precioVenta, cantidad FROM detalle_ventas WHERE idVenta = $idVenta AND activo = 1";
                $res_detalle = $mysqli->query($sql);
                $totalVenta = 0;
                while ($row_detalle = $res_detalle->fetch_assoc())
                {
                    $precioVenta = $row_detalle['precioVenta'];
                    $cantidad = $row_detalle['cantidad'];
                    $totalVenta += $precioVenta * $cantidad;
                }
                $fechaReg = date_create($row['fechaCreacion']);
                $fechaReg = date_format($fechaReg, 'd-m-Y H:i:s');
                $htmlBtns = '<div class="action-buttons">';
                if ($row['idFactura'] != 0)
                {
                    $htmlBtns.=             '<a class="green pointer fPdf" id="'.$row['idFactura'].'" target="_blank" href="assets/pdf/facturaPDF.php?idFactura='.$row['idFactura'].'" data-rel="tooltip" title="PDF factura">
                                                <i class="ace-icon fa fa-file-pdf-o bigger-130"></i>
                                            </a>
                                            <a class="green pointer aXml" id="" target="_blank" href="assets/ws/descargarXML.php?xml='.$row['idFactura'].'" data-rel="tooltip" title="Descargar XML">
                                                <i class="ace-icon fa fa-file-code-o bigger-130"></i>
                                            </a>';
                }
                else
                {
                    $htmlBtns.=             '<a class="pink pointer crearFactura" id="'.$row['idVenta'].'" href="agregarFactura.php?idVenta='.$row['idVenta'].'" data-rel="tooltip" title="Agegar factura">
                                                <i class="ace-icon fa fa-bolt bigger-130"></i>
                                            </a>';
                }
                $htmlBtns .=                '<a class="blue pointer aPdf" id="'.$row['idVenta'].'" target="_blank" href="assets/pdf/comprobanteVenta.php?idVenta='.$row['idVenta'].'" data-rel="tooltip" title="PDF venta">
                                                <i class="ace-icon fa fa-file-pdf-o bigger-130"></i>
                                            </a>
                                            <a class="purple pointer aEdit" id="'.$row['idVenta'].'" href="agregarVenta.php?idVenta='.$row['idVenta'].'" data-rel="tooltip" title="Editar">
                                                <i class="ace-icon fa fa-pencil-square-o bigger-130"></i>
                                            </a>
                                            <a class="btnEliminar pointer red" idCliente='.$row['idVenta'].' data-rel="tooltip" title="Eliminar">
                                                <i class="ace-icon fa fa-ban bigger-130"></i>
                                            </a>
                                        </div>';
                $InfoData[] = array(
                    'id'                => str_pad($idVenta, 7, "0", STR_PAD_LEFT),
                    'fechaCreacion'     => $fechaReg,
                    'cliente'           => $row['nombreCliente']." ".$row['apellidopCliente']." ".$row['apellidomCliente'],
                    'totalVenta'       => "<b class='blue'>$".number_format($totalVenta,2,".",",")."</b>",
                    'nombreUsuario'     => $row['nombreUsuario']." ".$row['apellidopUsuario'],
                    'nombreSucursal'    => $row['idSucursal'].".-".$row['nombreSucursal'].", ".$row['direccionSucursal'],
                    'status'            => ($row['activo'] == 1) ? '<span class="label label-info label-white middle"><i class="fa fa-check-circle" aria-hidden="true"></i> Activa</span>' : '<span class="label label-danger label-white middle"><i class="fa fa-ban" aria-hidden="true"></i> Cancelada</span>',
                    // 'precio'            => "$".number_format($row['precio'],2,".",","),
                    // 'existencias'       => $row['existencias'],
                    'btns'              => $htmlBtns);
            }
            //$data[] = $InfoData;
            $json_data = [
                "data"   => $InfoData
            ];
        }
        echo json_encode($json_data);
    }
?>

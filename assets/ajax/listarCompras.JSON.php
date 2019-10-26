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
                    compras.activo              AS activo,
                    compras.id                  AS idCompra,
                    compras.fechaCreacion       AS fechaCreacion,
                    compras.idSucursal          AS idSucursal,
                    cat_usuarios.nombres        AS nombreUsuario,
                    cat_usuarios.apellidop      AS apellidopUsuario,
                    cat_sucursales.nombre       AS nombreSucursal,
                    cat_sucursales.direccion2   AS direccionSucursal,
                    cat_proveedores.rsocial     AS proveedor
                FROM compras
                INNER JOIN cat_usuarios
                ON compras.usuario = cat_usuarios.id
                INNER JOIN cat_sucursales
                ON compras.idSucursal = cat_sucursales.id
                INNER JOIN cat_proveedores
                ON compras.idProveedor = cat_proveedores.id
                WHERE compras.fechaCreacion BETWEEN '$fInicio' AND '$fFin'";

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
                $idCompra = $row['idCompra'];
                $sql = "SELECT precioCompra, cantidad FROM detalle_compras WHERE idCompra = $idCompra AND activo = 1";
                $res_detalle = $mysqli->query($sql);
                $totalCompra = 0;
                while ($row_detalle = $res_detalle->fetch_assoc())
                {
                    $precioCompra = $row_detalle['precioCompra'];
                    $cantidad = $row_detalle['cantidad'];
                    $totalCompra += $precioCompra * $cantidad;
                }
                $fechaReg = date_create($row['fechaCreacion']);
                $fechaReg = date_format($fechaReg, 'd/m/Y H:i:s');
                $InfoData[] = array(
                    'id'                => str_pad($row['idCompra'], 7, "0", STR_PAD_LEFT),
                    'fechaCreacion'     => $fechaReg,
                    'proveedor'         => $row['proveedor'],
                    'totalCompra'       => "$".number_format($totalCompra,2,".",","),
                    'nombreUsuario'     => $row['nombreUsuario']." ".$row['apellidopUsuario'],
                    'nombreSucursal'    => $row['idSucursal'].".-".$row['nombreSucursal'].", ".$row['direccionSucursal'],
                    'status'            => ($row['activo'] == 1) ? '<span class="label label-info label-white middle"><i class="fa fa-check-circle" aria-hidden="true"></i> Activa</span>' : '<span class="label label-danger label-white middle"><i class="fa fa-ban" aria-hidden="true"></i> Cancelada</span>',
                    // 'precio'            => "$".number_format($row['precio'],2,".",","),
                    // 'existencias'       => $row['existencias'],
                    'btns'              => '<div class="hidden-sm hidden-xs action-buttons">
                                                <a class="blue pointer aPdf" id="'.$row['idCompra'].'" target="_blank" href="assets/pdf/comprobanteCompra.php?idCompra='.$row['idCompra'].'" data-rel="tooltip" title="Exportar a PDF">
                                                    <i class="ace-icon fa fa-file-pdf-o bigger-130"></i>
                                                </a>
                                                <a class="purple pointer aEdit" id="'.$row['idCompra'].'" href="agregarCompra.php?idCompra='.$row['idCompra'].'" data-rel="tooltip" title="Editar">
            									    <i class="ace-icon fa fa-pencil-square-o bigger-130"></i>
            									</a>
            									<a class="btnEliminar pointer red" idCliente='.$row['idCompra'].' data-rel="tooltip" title="Eliminar">
            										<i class="ace-icon fa fa-ban bigger-130"></i>
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

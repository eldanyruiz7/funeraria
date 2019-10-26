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
        require ("../php/factura.class.php");
        // $usuario = new usuario($idUsuario,$mysqli);
        // $permiso = $usuario->permiso("listarVentas",$mysqli);
        // if (!$permiso)
        // {
        //     $json_data = [
        //         "data"   => 0
        //     ];
        //     echo json_encode($json_data);
        //     die;
        // }
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
                    id FROM facturas
                WHERE facturas.activo = 1 AND facturas.timestamp BETWEEN '$fInicio' AND '$fFin'";
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
                $idFactura = $row['id'];
                $factura    = new factura($idFactura,$mysqli);
                // $fechaReg = date_create($row['fechaCreacion']);
                // $fechaReg = date_format($fechaReg, 'd-m-Y H:i:s');
                $htmlBtns = '<div class="action-buttons">';
                $htmlBtns .=                '<a class="blue pointer aPdf" id="'.$factura->id.'" target="_blank" href="assets/pdf/facturaPDF.php?idFactura='.$factura->id.'" data-rel="tooltip" title="PDF factura">
                                                <i class="ace-icon fa fa-file-pdf-o bigger-130"></i>
                                            </a>
                                            <a class="green pointer aXml" id="" target="_blank" href="assets/ws/descargarXML.php?xml='.$factura->id.'" data-rel="tooltip" title="PDF factura">
                                                <i class="ace-icon fa fa-file-code-o bigger-130"></i>
                                            </a>
                                        </div>';
                $InfoData[] = array(
                    'id'                => str_pad($factura->id, 10, "0", STR_PAD_LEFT),
                    'fechaCreacion'     => $factura->fechaCreacion(),
                    'rfcReceptor'       => $factura->rfcReceptor,
                    'razonReceptor'     => $factura->razonReceptor,
                    'subTotal'          => "<b class='green'>$".$factura->subTotal()."</b>",
                    'iva'               => "<b class='grey'>$".$factura->totalIVA()."</b>",
                    'total'             => "<b class='blue'>$".$factura->total()."</b>",
                    'nombreSucursal'    => $factura->nombreSucursal.", ".$factura->direccion2Sucursal,
                    'btns'              => $htmlBtns);
            }
            $json_data = [
                "data"   => $InfoData
            ];
        }
        echo json_encode($json_data);
    }
?>

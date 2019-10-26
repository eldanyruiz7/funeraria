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
        require ("../php/usuario.class.php");
        require "../php/responderJSON.php";
        $response = array(
            "status"        => 1
        );
        $fInicio = $_GET['fechaInicio'];
        $fInicio_e = explode('-',$fInicio);
        $Y_ini = intval($fInicio_e[0]);
        $m_ini = intval($fInicio_e[1]);
        $d_ini = intval($fInicio_e[2]);
        $fFin = $_GET['fechaFin'];
        $fFin_e = explode('-',$fFin);
        $Y_fin = intval($fFin_e[0]);
        $m_fin = intval($fFin_e[1]);
        $d_fin = intval($fFin_e[2]);
        if(checkdate($m_ini,$d_ini,$Y_ini) == FALSE || checkdate($m_fin,$d_fin,$Y_fin) == FALSE)
        {
            $json_data = [
                "data"   => 0
            ];
            echo json_encode($json_data);
            die;
        }
        $sql = "SELECT id FROM cat_usuarios WHERE activo = 1";

                // WHERE detalle_pagos_contratos.fechaCreacion BETWEEN '$fInicio' AND '$fFin'";
                // echo $sql;
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
                $idUsr = $row['id'];
                $usuario = new usuario($idUsr,$mysqli);
                // $fechaReg = date_create($row['fechaCreacion']);
                // $fechaReg = date_format($fechaReg, 'd/m/Y H:i:s');
                // $idVendedor = $row['idVendedor'];
                // $sql = "SELECT nombres, apellidop, apellidom FROM cat_usuarios WHERE id = $idVendedor LIMIT 1";
                // $res_vend = $mysqli->query($sql);
                // $row_vend = $res_vend->fetch_assoc();
                // $nombreVendedor = $row_vend['nombres']." ".$row_vend['apellidop']." ".$row_vend['apellidom'];
                // $btns = '<div class="action-buttons">
                //             <a href="assets/pdf/recibo.php?idRecibo='.$row['idRecibo'].'" target="_blank" class="orange pointer" data-rel="tooltip" title="Imprimir recibo de pago">
                //                 <i class="ace-icon fa fa-print bigger-130"></i>
                //             </a>
				// 			<a class="btnEliminar pointer red" idCliente='.$row['idRecibo'].' data-rel="tooltip" title="Eliminar">
				// 				<i class="ace-icon fa fa-ban bigger-130"></i>
				// 		    </a>
				// 		</div>';
                $array = $usuario->obtener_cobranza_vendedor($mysqli, $fInicio, $fFin);
                // if ($array['cobrado'] <= 0) {
                //     continue;
                // }
                $InfoData[] = array(
                    // 'nombre'        => str_pad($row['idContrato'], 10, "0", STR_PAD_LEFT),
                    'nombre'            => $usuario->nombres,
                    'monto'             => number_format($array['totalCobranza'],2,".",""),
                    'comision'          => number_format($array['totalComisionGanada'],2,".","")
                    );
            }
            //$data[] = $InfoData;
            $json_data = [
                "data"   => $InfoData
            ];
        }
        echo json_encode($json_data);
    }
?>

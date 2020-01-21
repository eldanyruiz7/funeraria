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
        $fInicio       .= " 00:00:00";
        $fFin          .= " 23:59:59";
        $sql = "SELECT
                    detalle_pagos_contratos.id                  AS idRecibo,
                    detalle_pagos_contratos.idContrato          AS idContrato,
                    detalle_pagos_contratos.fechaCreacion       AS fechaCreacion,
                    detalle_pagos_contratos.monto               AS monto,
                    detalle_pagos_contratos.usuario_cobro       AS idUsuarioCobro,
                    detalle_pagos_contratos.idFolio_cobranza    AS idFolioCobranza,
                    detalle_pagos_contratos.activo              AS activo,
                    cat_usuarios.nombres                        AS nombreUsuarioCobro,
                    cat_usuarios.apellidop                      AS apellidopUsuarioCobro,
                    cat_usuarios.apellidom                      AS apellidomUsuarioCobro,
                    folios_cobranza_asignados.folio             AS folioCobranza,
                    cat_sucursales.nombre                       AS nombreSucursal,
                    cat_sucursales.direccion2                   AS direccionSucursal,
                    contratos.idVendedor                        AS idVendedor,
                    contratos.folio                             AS folioContrato
                FROM detalle_pagos_contratos
                INNER JOIN cat_usuarios
                ON detalle_pagos_contratos.usuario_cobro        = cat_usuarios.id
                INNER JOIN folios_cobranza_asignados
                ON detalle_pagos_contratos.idFolio_cobranza     = folios_cobranza_asignados.id
                INNER JOIN cat_sucursales
                ON folios_cobranza_asignados.idSucursal         = cat_sucursales.id
                INNER JOIN contratos
                ON detalle_pagos_contratos.idContrato = contratos.id
                WHERE detalle_pagos_contratos.fechaCreacion BETWEEN '$fInicio' AND '$fFin'";
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
                $fechaReg = date_create($row['fechaCreacion']);
                $fechaReg = date_format($fechaReg, 'd/m/Y H:i:s');
                $idVendedor = $row['idVendedor'];
                $sql = "SELECT nombres, apellidop, apellidom FROM cat_usuarios WHERE id = $idVendedor LIMIT 1";
                $res_vend = $mysqli->query($sql);
                $row_vend = $res_vend->fetch_assoc();
                $nombreVendedor = $row_vend['nombres']." ".$row_vend['apellidop']." ".$row_vend['apellidom'];
                $btns = '<div class="action-buttons">
                            <a href="assets/pdf/recibo.php?idRecibo='.$row['idRecibo'].'" target="_blank" class="orange pointer" data-rel="tooltip" title="Imprimir recibo de pago">
                                <i class="ace-icon fa fa-print bigger-130"></i>
                            </a>
							<a class="btnEliminar pointer red" idCliente='.$row['idRecibo'].' data-rel="tooltip" title="Eliminar">
								<i class="ace-icon fa fa-ban bigger-130"></i>
						    </a>
						</div>';


                $InfoData[] = array(
                    'idContrato'        => str_pad($row['idContrato'], 10, "0", STR_PAD_LEFT),
                    'folioContrato'     => $row['folioContrato'],
                    'recibo'            => $row['folioCobranza'],
                    'fechaCreacion'     => $fechaReg,
                    'usuarioCobro'      => $row['nombreUsuarioCobro']." ".$row['apellidopUsuarioCobro']." ".$row['apellidomUsuarioCobro'],
                    'vendedor'          => $nombreVendedor,
                    'sucursal'          => $row['nombreSucursal'].". ".$row['direccionSucursal'],
                    'monto'             => $row['activo'] ? number_format($row['monto'],2,".","") : "<b class='red'>".number_format($row['monto'],2,".","")."</b>",
                    'status'            => $row['activo'] ? '<span class="label label-info label-white middle">Activo</span>' : '<span class="label label-danger label-white middle">Cancelado</span>',
                    'btns'              => $btns);
            }
            //$data[] = $InfoData;
            $json_data = [
                "data"   => $InfoData
            ];
        }
        echo json_encode($json_data);
    }
?>

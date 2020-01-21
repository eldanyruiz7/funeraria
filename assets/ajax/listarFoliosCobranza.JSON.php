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

        $sql = "SELECT
                    folios_cobranza_asignados.id                    AS id,
                    folios_cobranza_asignados.idUsuario_asignado    AS idUsuarioAsignado,
                    folios_cobranza_asignados.folio                 AS folio,
                    folios_cobranza_asignados.idSucursal            AS idSucursal,
                    folios_cobranza_asignados.fechaCreacion         AS fechaCreacion,
                    folios_cobranza_asignados.idUsuario             AS idUsuario,
                    folios_cobranza_asignados.activo                AS activo,
                    folios_cobranza_asignados.asignado              AS asignado,
                    cat_usuarios.nombres                            AS nombreUsuarioAsignado,
                    cat_usuarios.apellidop                          AS apellidopUsuarioAsignado,
                    cat_usuarios.apellidom                          AS apellidomUsuarioAsignado,
                    cat_sucursales.nombre                           AS nombreSucursal,
                    cat_sucursales.direccion2                       AS direccion2Sucursal
                FROM folios_cobranza_asignados
                INNER JOIN cat_usuarios
                ON folios_cobranza_asignados.idUsuario_asignado = cat_usuarios.id
                INNER JOIN cat_sucursales
                ON folios_cobranza_asignados.idSucursal = cat_sucursales.id";
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
                // $fechaNac = date_create($row['fechaNacCliente']);
                // $fechaNac = date_format($fechaNac, 'd/m/Y');
                $fechaReg = date_create($row['fechaCreacion']);
                $fechaReg = date_format($fechaReg, 'd/m/Y H:i:s');
                $idUsuario = $row['idUsuario'];
                $sql = "SELECT nombres, apellidop, apellidom FROM cat_usuarios WHERE id = $idUsuario LIMIT 1";
                $res_usr = $mysqli->query($sql);
                $row_usr = $res_usr->fetch_assoc();
                if ($row['activo'] == 1)
                {
                    if ($row['asignado'] != 0)
                    {
                        $noRecibo = $row['asignado'];
                        $sql = "SELECT idContrato FROM detalle_pagos_contratos WHERE id = $noRecibo LIMIT 1";
                        $res_cont = $mysqli->query($sql);
                        $row_cont = $res_cont->fetch_assoc();
                        $code = str_pad($row_cont['idContrato'], 10, "0", STR_PAD_LEFT);
                        $ubicacion = '<span class="label label-success">
    									<i class="ace-icon fa fa-file-text-o bigger-120"></i>
    									<b>'.$code.'</b>
    								 </span>';
                    }
                    else
                    {
                        $ubicacion = '<span class="label label-info"><i class="ace-icon fa fa-user-o bigger-120"></i> En cobrador</span>';
                    }
                }
                else
                {
                    $ubicacion = '<span class="label label-danger">
									<i class="ace-icon fa fa-ban bigger-120"></i>
									Cancelado
								 </span>';
                }
                $InfoData[] = array(
                    'id'                => $row['id'],
                    'folio'             => $row['folio'],
                    'usuarioAsignado'   => $row['nombreUsuarioAsignado']." ".$row['apellidopUsuarioAsignado']." ".$row['apellidomUsuarioAsignado'],
                    'sucursal'          => $row['nombreSucursal'].", ".$row['direccion2Sucursal'],
                    'fechaReg'          => $fechaReg,
                    'status'            => $ubicacion,
                    'usuarioAsigno'     => $row_usr['nombres']." ".$row_usr['apellidop']." ".$row_usr['apellidom'],
                    'btns'              => '<div class="hidden-sm hidden-xs action-buttons">
            									<a class="btnEliminar pointer red" idCliente='.$row['id'].' data-rel="tooltip" title="Cancelar">
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

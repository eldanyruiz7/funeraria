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

        $sql = "SELECT
                    cat_difuntos.id                             AS idDifunto,
                    cat_difuntos.idCliente                      AS idCliente,
                    cat_difuntos.idContrato                     AS idContrato,
                    cat_difuntos.idVenta                        AS idVenta,
                    cat_difuntos.nombres                        AS nombres,
                    cat_difuntos.apellidop                      AS apellidop,
                    cat_difuntos.apellidom                      AS apellidom,
                    cat_difuntos.rfc                            AS rfc,
                    cat_difuntos.domicilio1_part                AS direccion1,
                    cat_difuntos.domicilio2_part                AS direccion2,
                    cat_difuntos.cp_part                        AS cp,
                    cat_difuntos.fechaHrDefuncion               AS fechaHrDefuncion,
                    cat_difuntos.activo                         AS activo,
                    cat_sucursales.id                           AS idSucursal,
                    cat_sucursales.nombre                       AS nombreSucursal,
                    cat_sucursales.direccion2                   AS direccionSucursal,
                    cat_usuarios.nombres                        AS nombreUsuario,
                    cat_usuarios.apellidop                      AS apellidopUsuario,
                    cat_estados.estado                          AS nombreEstado,
                    cat_estados.id                              AS idEstado
                FROM cat_difuntos
                INNER JOIN cat_sucursales
                ON cat_difuntos.idSucursal = cat_sucursales.id
                INNER JOIN cat_usuarios
                ON cat_difuntos.usuario = cat_usuarios.id
                INNER JOIN cat_estados
                ON cat_difuntos.idEstado_part = cat_estados.id
                WHERE cat_difuntos.activo = 1
                AND cat_difuntos.idCliente = 0
                AND cat_difuntos.idContrato = 0
                AND cat_difuntos.idVenta = 0";

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
                // $idCompra = $row['idCompra'];
                $fechaDef = date_create($row['fechaHrDefuncion']);
                $fechaDef = date_format($fechaDef, 'd/m/Y h:i a');
                // if ($row['idLugarDefuncion'] == 0)
                // {
                //     $lugarDefuncion = $row['domicilioParticularDefuncion'];
                // }
                // else
                // {
                //     $idLugarDef = $row['idLugarDefuncion'];
                //     $sql = "SELECT nombre, direccion FROM cat_lugares_defuncion WHERE id = $idLugarDef LIMIT 1";
                //     $res_lugar = $mysqli->query($sql);
                //     $row_lugar = $res_lugar->fetch_assoc()
                //     $lugarDefuncion = $row_lugar['nombre']
                // }
                if (!$row['idContrato'] && !$row['idVenta'])
                {
                    $asignado = '<span class="label label-white middle">No asignado</span>';
                }
                else
                {
                    if ($row['idContrato'])
                    {
                        $asignado = '<span class="label label-info middle"><i class="menu-icon fa fa-file-text"></i> Id Plan: <b>'.$row['idContrato'].'</b></span>';
                    }
                    else
                    {
                        $asignado = '<span class="label label-success middle"><i class="menu-icon fa fa-ticket"></i> Id venta: <b>'.$row['idVenta'].'</b></span>';

                    }
                }
                $InfoData[] = array(
                    'id'                => str_pad($row['idDifunto'], 7, "0", STR_PAD_LEFT),
                    'nombreDifunto'     => $row['nombres']." ".$row['apellidop']." ".$row['apellidom'],
                    'rfc'               => $row['rfc'],
                    'fechaDefuncion'    => $fechaDef,
                    'nombreUsuario'     => $row['nombreUsuario']." ".$row['apellidopUsuario'],
                    'nombreSucursal'    => $row['idSucursal'].".-".$row['nombreSucursal'].", ".$row['direccionSucursal'],
                    'direccion'         => $row['direccion1'].", ".$row['direccion2'].", ".$row['cp'].", ".$row['nombreEstado'],
                    // 'precio'            => "$".number_format($row['precio'],2,".",","),
                    // 'existencias'       => $row['existencias'],
                    'btnAgregar'        => '<div class="action-buttons">
                                                <a class="blue pointer aAgregarDifunto"
                                                    id="'.$row['idDifunto'].'" nombre-difunto="'.$row['nombres'].' '.$row['apellidop'].' '.$row['apellidom'].'"
                                                    domicilio1="'.$row['direccion1'].'"
                                                    domicilio2="'.$row['direccion2'].'"
                                                    id-estado="'.$row['idEstado'].'"
                                                    cp="'.$row["cp"].'" data-rel="tooltip" title="Agregar">
                								    <i class="ace-icon fa fa-2x fa-share-square-o bigger-130"></i>
                								</a>
                							</div>');
                    // 'btns'              => '<div class="hidden-sm hidden-xs action-buttons">
                    //                             <a class="purple pointer aEdit" id="'.$row['idDifunto'].'" href="agregarDifunto.php?idDifunto='.$row['idDifunto'].'" data-rel="tooltip" title="Editar">
            		// 							    <i class="ace-icon fa fa-pencil-square-o bigger-130"></i>
            		// 							</a>
            		// 							<a class="btnEliminar pointer red" idCliente='.$row['idDifunto'].' data-rel="tooltip" title="Eliminar">
            		// 								<i class="ace-icon fa fa-ban bigger-130"></i>
            		// 						    </a>
            		// 						</div>');
            }
            //$data[] = $InfoData;
            $json_data = [
                "data"   => $InfoData
            ];
        }
        echo json_encode($json_data);
    }
?>

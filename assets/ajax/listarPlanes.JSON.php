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
        // require ("../php/usuario.class.php");
        // $usuario = new usuario($idUsuario,$mysqli);
        // $permiso = $usuario->permiso("listarPlanes",$mysqli);
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

        $sql = "SELECT
                    cat_planes.id               AS idPlan,
                    cat_planes.nombre           AS nombrePlan,
                    cat_planes.descripcion      AS descripcion,
                    cat_planes.precio           AS precio,
                    cat_planes.fechaCreacion    AS fechaCreacion,
                    cat_planes.idSucursal       AS idSucursal,
                    cat_sucursales.nombre       AS nombreSucursal,
                    cat_sucursales.direccion2   AS direccion2Sucursal,
                    cat_usuarios.nombres        AS nombreUsuario,
                    cat_usuarios.apellidop      AS apellidopUsuario
                FROM cat_planes
                INNER JOIN cat_sucursales
                ON cat_planes.idSucursal = cat_sucursales.id
                INNER JOIN cat_usuarios
                ON cat_planes.usuario = cat_usuarios.id
                WHERE cat_planes.activo = 1";

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
                $idPlan = $row['idPlan'];
                $idSucursal = $row['idSucursal'];
                $sql = "SELECT
                            detalle_cat_planes.precio AS estePrecio,
                            detalle_cat_planes.cantidad AS esteCantidad
                            FROM detalle_cat_planes
                            WHERE detalle_cat_planes.idPlan = $idPlan
                            AND detalle_cat_planes.idSucursal = $idSucursal
                            AND detalle_cat_planes.activo = 1";
                $res_sum = $mysqli->query($sql);
                $totalCalculado = 0;
                while ($row_sum = $res_sum->fetch_assoc())
                    $totalCalculado += $row_sum['estePrecio'] * $row_sum['esteCantidad'];
                $fechaReg = date_create($row['fechaCreacion']);
                $fechaReg = date_format($fechaReg, 'd/m/Y H:i:s');
                $InfoData[] = array(
                    'id'                => $row['idPlan'],
                    'nombrePlan'        => $row['nombrePlan'],
                    'descripcion'       => (strlen($row['descripcion']) > 0) ? $row['descripcion'] : "--",
                    'precioCalculado'   => "<span class='green'>$".number_format($totalCalculado,2,".",",")."</span>",
                    'precio_number'     => $row['precio'],
                    'precio'            => "<span class='blue'>$".number_format($row['precio'],2,".",",")."</span>",
                    'idSucursal'        => $row['idSucursal'],
                    'sucursal'          => $row['idSucursal'].".- ".$row['nombreSucursal']." ".$row['direccion2Sucursal'],
                    'fechaCreacion'     => $fechaReg,
                    'usuario'           => $row['nombreUsuario']." ".$row['apellidopUsuario'],
                    'btnAgregar'        => '<div class="action-buttons">
                                                <a class="blue pointer aAgregarPlan"
                                                    id="'.$row['idPlan'].'"
                                                    precio="'.$row['precio'].'"
                                                    nombre-plan="'.$row['nombrePlan'].'"
                                                    data-rel="tooltip" title="Editar">
                								    <i class="ace-icon fa fa-2x fa-share-square-o bigger-130"></i>
                								</a>
                							</div>',
                    'btns'              => '<div class="hidden-sm hidden-xs action-buttons">
                                                <a class="purple pointer aEdit" id="'.$row['idPlan'].'" href="agregarPlan.php?idPlan='.$row['idPlan'].'" data-rel="tooltip" title="Editar">
            									    <i class="ace-icon fa fa-pencil-square-o bigger-130"></i>
            									</a>
            									<a class="btnEliminar pointer red" idCliente='.$row['idPlan'].' data-rel="tooltip" title="Eliminar">
            										<i class="ace-icon fa fa-trash-o bigger-130"></i>
            								    </a>
            								</div>');
            }
            $json_data = [
                "data"   => $InfoData
            ];
        }
        echo json_encode($json_data);
    }
?>

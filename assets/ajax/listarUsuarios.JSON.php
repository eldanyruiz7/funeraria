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
        $usuario = new usuario($idUsuario,$mysqli);
        $permiso = $usuario->permiso("listarUsuarios",$mysqli);
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
        $sql = "SELECT
                    cat_usuarios.id                 AS id,
                    cat_usuarios.nombres            AS nombre,
                    cat_usuarios.apellidop          AS apellidop,
                    cat_usuarios.apellidom          AS apellidom,
                    cat_usuarios.nickName           AS nickName,
                    cat_usuarios.tasaComision       AS tasaComision,
                    cat_usuarios.fechaCreacion      AS fechaCreacion,
                    cat_usuarios.idSucursal         AS idSucursal,
                    cat_usuarios.tipo               AS tipoUsuario
                FROM cat_usuarios
                WHERE cat_usuarios.activo = 1 AND cat_usuarios.tipo > 0";

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
                $usuario = new usuario($row['id'], $mysqli);
                // $fechaReg = ;
                // $fechaReg = date_format($fechaReg, 'd/m/Y H:i:s');
                // $tipoUsuario = ;
                // switch ($row['tipoUsuario'])
                // {
                //     case 1:
                //         $tipoUsuario = '<span class="badge badge-info">Admin</span>';
                //         break;
                //     case 2:
                //         $tipoUsuario = '<span class="badge badge-success">Secetario/a</span>';
                //         break;
                //     case 3:
                //         $tipoUsuario = '<span class="badge badge-yellow">Visitante</span>';
                //         break;
                //
                //     default:
                //         $tipoUsuario = '<span class="badge badge-yellow">Visitante</span>';
                //         break;
                // }
                $InfoData[] = array(
                    'id'                => $usuario->id,//$row['id'],
                    'nombre'            => $usuario->nombres,//$row['nombre']." ".$row['apellidop']." ".$row['apellidom'],
                    'tasaComision'      => $usuario->tasaComision." %",
                    'nickName'          => $usuario->nickName,
                    'fechaReg'          => $usuario->fechaCreacion(),
                    'tipo'              => $usuario->tipoUsuario(TRUE),
                    'idSucursal'        => $usuario->idSucursal,//$row['idSucursal'],
                    'btns'              => '<div class="hidden-sm hidden-xs action-buttons">
                                                <a id-usuario = "'.$usuario->id.'" nombre-usuario="'.$usuario->nombres.'" class="blue pointer aAsignarFolios" data-rel="tooltip" title="Asignar folios de cobranza">
                                                    <i class="ace-icon fa fa-ticket bigger-130"></i>
                                                </a>
                                                <a href="agregarUsuario.php?idUsuario='.$usuario->id.'" class="purple pointer aEdit" data-rel="tooltip" title="Editar">
            									    <i class="ace-icon fa fa-pencil-square-o bigger-130"></i>
            									</a>
            									<a class="btnEliminar pointer red" idCliente='.$usuario->id.' data-rel="tooltip" title="Eliminar">
            										<i class="ace-icon fa fa-trash-o bigger-130"></i>
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

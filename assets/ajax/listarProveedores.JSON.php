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
                    cat_proveedores.id                 AS idProveedor,
                    cat_proveedores.rsocial            AS rsocial,
                    cat_proveedores.representante      AS representante,
                    cat_proveedores.rfc                AS rfc,
                    cat_proveedores.fechaCreacion      AS fechaRegistro
                FROM cat_proveedores
                WHERE cat_proveedores.activo = 1";

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
                $fechaReg = date_create($row['fechaRegistro']);
                $fechaReg = date_format($fechaReg, 'd/m/Y H:i:s');

                $InfoData[] = array(
                    'id'                => $row['idProveedor'],
                    'rsocial'           => $row['rsocial'],
                    'representante'     => $row['representante'],
                    'rfc'               => $row['rfc'],
                    'fechaReg'          => $fechaReg,
                    'btns'              => '<div class="hidden-sm hidden-xs action-buttons">
                                                <a class="purple pointer aEdit" id="'.$row['idProveedor'].'" data-rel="tooltip" title="Editar">
            									    <i class="ace-icon fa fa-pencil-square-o bigger-130"></i>
            									</a>
            									<a class="btnEliminar pointer red" idCliente='.$row['idProveedor'].' data-rel="tooltip" title="Eliminar">
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

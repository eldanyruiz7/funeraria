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
                    cat_productos.id                AS idProducto_,
                    cat_productos.nombre            AS nombreProducto,
                    cat_productos.precio            AS precio,
                    (SELECT IFNULL(SUM(detalle_existenciasproductos.existencias),0)
                     FROM detalle_existenciasproductos
                     WHERE detalle_existenciasproductos.idProducto = idProducto_)
                                                    AS existencias
                FROM cat_productos
                WHERE cat_productos.activo = 1";

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

                $InfoData[] = array(
                    'id'                => $row['idProducto_'],
                    'nombreProducto'    => $row['nombreProducto'],
                    'precio'            => "$".number_format($row['precio'],2,".",","),
                    'existencias'       => $row['existencias'],
                    'btns'              => '<div class="hidden-sm hidden-xs action-buttons">
                                                <a class="purple pointer aEdit" id="'.$row['idProducto_'].'" href="agregarProducto.php?idProducto='.$row['idProducto_'].'" data-rel="tooltip" title="Editar">
            									    <i class="ace-icon fa fa-pencil-square-o bigger-130"></i>
            									</a>
            									<a class="btnEliminar pointer red" idCliente='.$row['idProducto_'].' data-rel="tooltip" title="Eliminar">
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

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
        $permiso = $usuario->permiso("listarProductos",$mysqli);
        if (!$permiso)
        {
            $response['mensaje'] = "No se pudo mostrar este registro. Usuario con permisos insuficientes para realizar esta acción";
            $response['status'] = 0;
            responder($response, $mysqli);
        }

        $idCliente = $_POST['idCliente'];

        $response = array(
            "status"        => 1
        );
        if(is_numeric($idCliente) == FALSE)
        {
            $response['mensaje'] = "El formato del id del producto no es el correcto";
            $response['status'] = 0;
            responder($response, $mysqli);
        }
        $sql = "SELECT
                    cat_productos.id                AS idProducto_,
                    cat_productos.nombre            AS nombreProducto,
                    cat_productos.descripcion       AS descripcion,
                    cat_productos.precio            AS precio,
                    cat_productos.imagen            AS imagen,
                    cat_productos.fechaCreacion     AS fechaCreacion,
                    (SELECT IFNULL(SUM(detalle_existenciasproductos.existencias),0)
                     FROM detalle_existenciasproductos
                     WHERE detalle_existenciasproductos.idProducto = idProducto_)
                                                    AS existencias
                FROM cat_productos
                WHERE cat_productos.id = ?";
        if($prepare         = $mysqli->prepare($sql))
        {
            $prepare        ->bind_param('i',$idCliente);
            $prepare        ->execute();
            $res            = $prepare->get_result();
        }
        else
        {
            $response['mensaje'] = "Ocurrió un error en la consulta a la Base de datos.".$mysqli->errno . " " . $mysqli->error;
            $response['status'] = 0;
            responder($response, $mysqli);
        }
        if($res->num_rows == 0)
        {
            $response['mensaje'] = "No existe informaci&oacute;n para este producto. Posiblemente ya ha sido eliminado.";
            $response['status'] = 0;
            responder($response, $mysqli);
        }
        $row                            = $res->fetch_assoc();
        $id                             = $row['idProducto_'];
        $nombre                         = $row['nombreProducto'];
        $descripcion                    = $row['descripcion'];
        $precio                         = number_format($row['precio'],2,".",",");
        $fechaCreacion                  = date_create($row['fechaCreacion']);
        $fechaCreacion                  = date_format($fechaCreacion, 'd/m/Y');
        if (strlen($row['imagen'] > 0))
        {

            $im = file_get_contents("../images/avatars/productos/".$row['imagen'].".jpg");
            $imdata = base64_encode($im);
            $imgSrc                         = "data:image/jpeg;base64,$imdata";
        }
        else
        {
            $imgSrc = "";
        }
        $sql = "SELECT id, nombre, direccion2 FROM cat_sucursales WHERE activo = 1";
        $res_suc = $mysqli->query($sql);
        $response['existenciasXsuc']    ='<table width="100%">';
        $response['existenciasXsuc']    .=' <tr>';
        $response['existenciasXsuc']    .='     <td style="text-align:center" colspan="2"><b>Existencias por sucursal</b>';
        $response['existenciasXsuc']    .='     </td>';
        $response['existenciasXsuc']    .=' </tr>';

        while ($row_suc = $res_suc->fetch_assoc())
        {
            $idEstaSuc                  = $row_suc['id'];
            $nombreEstaSuc              = $row_suc['nombre'];
            $direccionEstaSuc           = $row_suc['direccion2'];
            $sql = "SELECT existencias FROM detalle_existenciasproductos WHERE idProducto = $id AND idSucursal = $idEstaSuc";
            $res_exist = $mysqli->query($sql);
            $row_exist = $res_exist->fetch_assoc();
            $existenciasEstaSuc           = $row_exist['existencias'];
            $response['existenciasXsuc'] .= "<tr>";
            $response['existenciasXsuc'] .= "   <td>$nombreEstaSuc - $direccionEstaSuc </td>";
            $response['existenciasXsuc'] .= "   <td style='text-align:right'>$existenciasEstaSuc </td>";
            $response['existenciasXsuc'] .=' </tr>';
        }
        $response['existenciasXsuc']    .='</table>';

        $response['id']                 = $id;
        $response['nombre']             = $nombre;
        $response['descripcion']        = $descripcion;
        $response['precio']             = $precio;
        $response['fechaCreacion']      = $fechaCreacion;
        $response['imgSrc']             = $imgSrc;
        $response['status']             = 1;
        $prepare                        ->close();
        responder($response, $mysqli);
    }
?>

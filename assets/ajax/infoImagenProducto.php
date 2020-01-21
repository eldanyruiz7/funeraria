<?php
usleep(100000);
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
                    cat_productos.imagen            AS imagen,
                    cat_productos.nombre            AS nombre
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
            $response['mensaje'] = "Imagen no disponible. Posiblemente ya ha sido eliminada.";
            $response['status'] = 0;
            responder($response, $mysqli);
        }
        $row                            = $res->fetch_assoc();
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
        $response['imgSrc']             = "<img class='profile-picture' width='100%' src='$imgSrc'/>";
        $response['caption']            = "Vista previa para <b>".$row['nombre']."</b>";
        $response['status']             = 1;
        $prepare                        ->close();
        responder($response, $mysqli);
    }
?>

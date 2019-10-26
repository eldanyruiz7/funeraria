<?php
    require_once ('../connect/bd.php');
    require_once ("../connect/sesion.class.php");
    $sesion = new sesion();
    require_once ("../connect/cerrarOtrasSesiones.php");
    require_once ("../connect/usuarioLogeado.php");
    require_once ("../php/funcionesVarias.php");
    if( logueado($idSesion,$idUsuario,$mysqli) == false || $idSesion == false)
    {
        header("Location: salir.php");
    }
    else
    {
        require "../php/responderJSON.php";
        require ("../php/usuario.class.php");
        $usuario = new usuario($idUsuario,$mysqli);
        $permiso = $usuario->permiso("agregarProducto",$mysqli);
        if (!$permiso)
        {
            $response['mensaje'] = "No se pudo guardar este registro. Usuario con permisos insuficientes para realizar esta acción";
            $response['status'] = 0;
            responder($response, $mysqli);
        }
        $nombres                        = $_POST['nombre'];
        $descripcion                    = $_POST['descripcion'];
        $precio                         = $_POST['precio'];
        $imagenBinario                  = $_POST['hiddenImgBinario'];
        $response = array(
            "status"                    => 1
        );

        if (!$nombres = validarFormulario('s',$nombres,0))
        {
            $response['mensaje'] = "El campo Nombre no cumple con el formato esperado y no puede estar en blanco";
            $response['status'] = 0;
            $response['focus'] = 'nombre';
            responder($response, $mysqli);
        }
        $descripcion = validarFormulario('s', $descripcion, FALSE);
        if (!$precio = validarFormulario('i',$precio,0))
        {
            $response['mensaje'] = "El campo Precio no cumple con el formato esperado y no puede estar en blanco ni puede ser menor o igual que cero (0)";
            $response['status'] = 0;
            $response['focus'] = 'precio';
            responder($response, $mysqli);
        }
        $imagenBinario = validarFormulario('s', $imagenBinario, FALSE);

        $idUsuario      = $sesion->get('id');
        $sql            = "SELECT idSucursal FROM cat_usuarios WHERE id = $idUsuario LIMIT 1";
        $res_noSucursal = $mysqli->query($sql);
        $row_noSucursal = $res_noSucursal->fetch_assoc();
        $idSucursal     = $row_noSucursal['idSucursal'];
        $mysqli->autocommit(FALSE);
        $sql            = "INSERT INTO cat_productos
                                (nombre, descripcion, precio, idSucursal, usuario)
                            VALUES
                                (?,?,?,?,?)";
        if($prepare     = $mysqli->prepare($sql))
        {
            if(!$prepare->bind_param('ssiii', $nombres, $descripcion, $precio, $idSucursal, $idUsuario))
            {
                $response['mensaje'] = "Error. No se pudo guardar la información. Falló el la vinculación de parámetros. Inténtalo nuevamente";
                $response['status'] = 0;
                responder($response, $mysqli);
            }
            if(!$prepare->execute())
            {
                $response['mensaje'] = "Error. No se pudo guardar la información. Falló el enlace a la base de datos. Inténtalo nuevamente";
                $response['status'] = 0;
                responder($response, $mysqli);
            }
            if($prepare->affected_rows == 0)
            {
                $response['mensaje']        = "No se modificó nada, no se pudo guardar el registro, inténtalo nuevamente";
                $response['status']         = 0;
                responder($response, $mysqli);
            }
            else
            {
                $insert_id                  = $mysqli->insert_id;
                if (strlen($imagenBinario) > 0)
                {
                    $insert_id_left = str_pad($insert_id, 10, "0", STR_PAD_LEFT);
                    $sql            = "UPDATE cat_productos SET imagen = '$insert_id_left' WHERE id = $insert_id LIMIT 1";
                    if ($mysqli     ->query($sql))
                    {
                        $imagen_d   = base64_decode($imagenBinario); // decode an image
                        $im         = imagecreatefromstring($imagen_d); // php function to create image from string
                        if ($im     !== false)
                        {
                            $resp   = imagejpeg($im, $_SERVER['DOCUMENT_ROOT']."/funeraria/ace-master/assets/images/avatars/productos/$insert_id_left.jpg");
                            imagedestroy($im);
                        }
                    }
                }
                $mysqli->commit();
                $response['mensaje']        = "$nombres";
                $response['status']         = 1;
                responder($response, $mysqli);
            }
        }
        else
        {
            $mysqli->rollback();
            $response['mensaje']        = "Error. No se pudo modificar. Falló la preparación de los datos. Vuelve a intentarlo nuevamente";
            $response['status']         = 0;
            responder($response, $mysqli);
        }
    }
?>

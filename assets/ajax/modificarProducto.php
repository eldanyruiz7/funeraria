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
        $permiso = $usuario->permiso("modificarProducto",$mysqli);
        if (!$permiso)
        {
            $response['mensaje'] = "No se pudo modificar este registro. Usuario con permisos insuficientes para realizar esta acción";
            $response['status'] = 0;
            responder($response, $mysqli);
        }

        $idProducto                     = $_POST['hiddenIdProducto'];
        $nombres                        = $_POST['nombre'];
        $descripcion                    = $_POST['descripcion'];
        $precio                         = $_POST['precio'];
        $imagenBinario                  = $_POST['hiddenImgBinario'];
        $response = array(
            "status"                    => 1
        );
        if (!$idProducto = validarFormulario('i',$idProducto,0))
        {
            $response['mensaje'] = "El formato del ID del producto no cumple con el formato esperado";
            $response['status'] = 0;
            $response['focus'] = '';
            responder($response, $mysqli);
        }
        $sql = "SELECT id FROM cat_productos WHERE id = ? AND activo = 1 LIMIT 1";
        if ($res = $mysqli->prepare($sql))
        {
            $res->bind_param('i',$idProducto);
            $res->execute();
            $res->store_result();
            if ($res->num_rows == 0)
            {
                $response['mensaje'] = "El producto con ID = <b>$idProduto</b> no se puede modificar ya que no se encuentra en la base de datos o posiblemente ya ha sido eliminado.";
                $response['status'] = 0;
                $response['focus'] = '';
                responder($response, $mysqli);
            }
        }
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
        $sql            = "UPDATE cat_productos
                           SET nombre = ?,
                               descripcion = ?,
                               precio = ?
                            WHERE id = ? LIMIT 1";
        if($prepare     = $mysqli->prepare($sql))
        {
            if(!$prepare->bind_param('ssii', $nombres, $descripcion, $precio, $idProducto))
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
            $insert_id                  = $idProducto;
            $insert_id_left = str_pad($insert_id, 10, "0", STR_PAD_LEFT);
            if (file_exists($_SERVER['DOCUMENT_ROOT']."/funeraria/ace-master/assets/images/avatars/productos/$insert_id_left.jpg"))
            {
                unlink($_SERVER['DOCUMENT_ROOT']."/funeraria/ace-master/assets/images/avatars/productos/$insert_id_left.jpg");
            }
            if (strlen($imagenBinario) == 0)
            {
                $sql            = "UPDATE cat_productos SET imagen = '' WHERE id = $insert_id LIMIT 1";
                if (!$mysqli     ->query($sql))
                {
                    $response['mensaje'] = "Error. No se pudo actualizar el campo imagen del registro. Inténtalo nuevamente";
                    $response['status'] = 0;
                    $mysqli->rollback();
                    responder($response, $mysqli);
                }
            }
            else
            {
                $sql            = "UPDATE cat_productos SET imagen = '$insert_id_left' WHERE id = $insert_id LIMIT 1";
                if ($mysqli     ->query($sql))
                {
                    $imagen_d   = base64_decode($imagenBinario); // decode an image
                    $im         = imagecreatefromstring($imagen_d); // php function to create image from string
                    // condition check if valid conversion
                    if ($im     !== false)
                    {

                        $resp   = imagejpeg($im, $_SERVER['DOCUMENT_ROOT']."/funeraria/ace-master/assets/images/avatars/productos/$insert_id_left.jpg");
                        imagedestroy($im);
                    }
                }
            }
			// Agregar evento en la bitácora de eventos ///////
			$ipUsuario 				= $sesion->get("ip");
			$pantalla				= "Agregar/Modificar producto";
			$descripcion			= "Se modificó un producto ($nombres) con id=$idProducto, precio=$$precio";
			$sql					= "CALL agregarEvento($idUsuario, '$ipUsuario', '$pantalla', '$descripcion', $idSucursal);";
			$mysqli					->query($sql);
            $mysqli->commit();
            $response['mensaje']	= "$nombres";
            $response['status']		= 1;
            responder($response, $mysqli);
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

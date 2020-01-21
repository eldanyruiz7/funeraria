<?php
    require_once ('../connect/bd.php');
    require_once ("../connect/sesion.class.php");
    $sesion = new sesion();
    require_once ("../connect/cerrarOtrasSesiones.php");
    require_once ("../connect/usuarioLogeado.php");
    require_once ("../php/funcionesVarias.php");
    if( logueado($idSesion,$idUsuario,$mysqli) == false || $idSesion == false)
    {
		header("Location: ".dirname(__FILE__)."../../salir.php");
    }
    else
    {
        require "../php/responderJSON.php";
        require ("../php/usuario.class.php");
        $usuario = new usuario($idUsuario,$mysqli);
        $permiso = $usuario->permiso("agregarPlan",$mysqli);
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
        $arrayProductos = json_decode($_POST['arrayProductos']);
        if (sizeof($arrayProductos) == 0)
        {
            $response['status']     = 0;
            $response['mensaje']  = "La lista de productos y servicios no puede estar vacía. Agrega al menos un producto o servicio para poder guardar el nuevo plan";
            responder($response, $mysqli);
        }
        $mysqli->autocommit(FALSE);
        $sql            = "INSERT INTO cat_planes
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
				// Agregar evento en la bitácora de eventos ///////
				$idUsuario 				= $sesion->get("id");
				$ipUsuario 				= $sesion->get("ip");
				$pantalla				= "Agregar/Modificar plan";
				$descripcion			= "Se agregó un nuevo plan funerario ($nombres) con id=$insert_id.";
				$sql					= "CALL agregarEvento($idUsuario, '$ipUsuario', '$pantalla', '$descripcion', $idSucursal);";
				$mysqli					->query($sql);
				//////////////////////////////////////////////////
                if (strlen($imagenBinario) > 0)
                {
                    $insert_id_left = str_pad($insert_id, 10, "0", STR_PAD_LEFT);
                    $sql            = "UPDATE cat_planes SET imagen = '$insert_id_left' WHERE id = $insert_id LIMIT 1";
                    if ($mysqli     ->query($sql))
                    {
                        $imagen_d   = base64_decode($imagenBinario); // decode an image
                        $im         = imagecreatefromstring($imagen_d); // php function to create image from string
                        if ($im     !== false)
                        {
                            $resp   = imagejpeg($im, $_SERVER['DOCUMENT_ROOT']."/funeraria/dev/assets/images/avatars/planes/$insert_id_left.jpg");
                            imagedestroy($im);
                        }
                    }
                }
                $idPlan = $insert_id;
                foreach ($arrayProductos as $esteProducto)
                {
                    $nombreProducto         =   $esteProducto    ->nombre;
                    $servicio               =   ($esteProducto    ->servicio == 0) ? 0 : 1;
                    if (!$idProducto = validarFormulario('i',$esteProducto->id, 0))
                    {
                        $mysqli->rollback();
                        $response['mensaje'] = "El formato del id <b>$idProducto->$nombreProducto</b> no es el correcto. Ocurrió un rollback. <br>No se guardó nada. <br>Por favor vuelve a intentarlo";
                        $response['status'] = 0;
                        responder($response, $mysqli);
                        break;
                    }
                    if (!$cantidadProducto = validarFormulario('i',$esteProducto->cantidad, 0))
                    {
                        $mysqli->rollback();
                        $response['mensaje'] = "El formato del parámetro 'cantidad' del producto: <b>$idProducto->$nombreProducto</b> no es el correcto y no puede ser menor o igual que cero (0). Ocurrió un rollback. <br>No se guardó nada. <br>Por favor vuelve a intentarlo";
                        $response['status'] = 0;
                        responder($response, $mysqli);
                        break;
                    }
                    if (!$precioProducto = validarFormulario('i',$esteProducto->precio, 0))
                    {
                        $mysqli->rollback();
                        $response['mensaje'] = "El formato del parámetro 'precio' del producto: <b>$idProducto->$nombreProducto</b> no es el correcto y no puede ser menor o igual que cero (0). Ocurrió un rollback. <br>No se guardó nada. <br>Por favor vuelve a intentarlo";
                        $response['status'] = 0;
                        responder($response, $mysqli);
                        break;
                    }
                    if ($servicio)
                    {
                        $idServicio = $idProducto;
                        $idProducto = 0;
                    }
                    else
                    {
                        $idServicio = 0;
                    }
                    $sql = "INSERT INTO
                                detalle_cat_planes (idPlan, idProducto, idServicio, idSucursal, cantidad, precio, usuario)
                            VALUES
                                (?,?,?,?,?,?,?)";
                    if($prepare_det = $mysqli->prepare($sql))
                    {
                        if (!$prepare_det->bind_param('iiiiidi',$idPlan, $idProducto, $idServicio, $idSucursal, $cantidadProducto, $precioProducto, $idUsuario ))
                        {
                            $mysqli->rollback();
                            $response['mensaje'] = "Error al registrar el detalle del plan. Error en el id de producto: <b>$idProducto</b>. No se pudo guardar la información. Falló la vinculación de parámetros. Inténtalo nuevamente";
                            $response['status'] = 0;
                            responder($response, $mysqli);
                        }
                        if (!$prepare_det->execute())
                        {
                            $mysqli->rollback();
                            $response['mensaje'] = "Error en el detalle del plan. No se pudo guardar la información. Error en el id de producto: <b>$idProducto</b>. Falló el enlace a la base de datos. Inténtalo nuevamente";
                            $response['status'] = 0;
                            responder($response, $mysqli);
                        }
                    }
                    else
                    {
                        $mysqli->rollback();
                        $response['mensaje'] = "Error en el detalle del plan. No se pudo guardar la información. Falló el la preparación de parámetros. Inténtalo nuevamente";
                        $response['status'] = 0;
                        responder($response, $mysqli);
                    }
                }
                if($mysqli->commit())
                {
                    $response['mensaje']        = "$nombres";
                    $response['status']         = 1;
                    responder($response, $mysqli);
                }
                else
                {
                    $response['mensaje']        = "Fallo en commit. No se pudo guardar. Inténtalo nuevamente.";
                    $response['status']         = 0;
                    responder($response, $mysqli);
                }
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

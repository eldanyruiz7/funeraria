<?php
    // error_reporting(E_ALL);
    // ini_set('display_errors', '1');
    //error_reporting(0);
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
        $permiso = $usuario->permiso("modificarPlan",$mysqli);
        if (!$permiso)
        {
            $response['respuesta'] = "No se pudo modificar este registro. Usuario con permisos insuficientes para realizar esta acción";
            $response['status'] = 0;
            responder($response, $mysqli);
        }
        $response = array(
            "status"        => 1
        );
        $imagenBinario              = $_POST['hiddenImgBinario'];
        $idUsuario                  = $sesion->get("id");
        $sql = "SELECT id, idSucursal FROM cat_usuarios WHERE id = ? LIMIT 1";
        if($prepare = $mysqli->prepare($sql))
        {
            if (!$prepare->bind_param('i',$idUsuario))
            {
                $response['respuesta'] = "Error en el Id de usuario. Falló la vinculación de parámetros. Inténtalo nuevamente";
                $response['status'] = 0;
                responder($response, $mysqli);
            }
            if (!$prepare->execute())
            {
                $response['respuesta'] = "Error en el Id de usuario. No se pudo consultar la información. Falló el enlace a la base de datos. Inténtalo nuevamente";
                $response['status'] = 0;
                responder($response, $mysqli);
            }
            $res_usr                 = $prepare->get_result();
            if($res_usr->num_rows == 0)
            {
                $response['respuesta']        = "Error. No existe el id de usuario en la Base de datos. No se guardó nada";
                $response['status']         = 0;
                responder($response, $mysqli);
            }
            $row_usr = $res_usr->fetch_assoc();
            $idSucursal = $row_usr['idSucursal'];
        }
        else
        {
            $response['respuesta'] = "Error en el id de usuario. Fallo en la preparación de parámetros. Inténtalo nuevamente";
            $response['status'] = 0;
            responder($response, $mysqli);
        }
        $idPlan   = $_POST['idPlan'];
        if (strlen($idPlan) == 0)
        {
            $response['respuesta'] = "El formato del id del plan no es el correcto";
            $response['status'] = 0;
            responder($response, $mysqli);
        }
        if (!is_numeric($idPlan) || $idPlan <= 0)
        {
            $response['respuesta'] = "El formato del id del plan no es el correcto. Inténtalo nuevamente";
            $response['status'] = 0;
            responder($response, $mysqli);
        }
        $sql = "SELECT id FROM cat_planes WHERE id = ? AND activo = 1 LIMIT 1";
        if($prepare_plan = $mysqli->prepare($sql))
        {
            if (!$prepare_plan->bind_param('i',$idPlan))
            {
                $response['respuesta'] = "Error en el Id del plan. Falló la vinculación de parámetros. Inténtalo nuevamente";
                $response['status'] = 0;
                responder($response, $mysqli);
            }
            if (!$prepare_plan->execute())
            {
                $response['respuesta'] = "Error en el Id del plan. No se pudo consultar la información. Falló el enlace a la base de datos. Inténtalo nuevamente";
                $response['status'] = 0;
                responder($response, $mysqli);
            }
            $res_plan                 = $prepare_plan->get_result();
            if($res_plan->num_rows == 0)
            {
                $response['respuesta']        = "Error. No existe el id <b>($idPlan)</b> del plan funerario en la Base de datos. Posiblemente ya fue eliminado o cancelado.
                                                <br><b>Este plan no se puede modificar.</b> <br>No se guardó nada
                                                </br> <strong><a href='listarPlanes.php' class='orange'>Lista de planes funerarios</a></strong>";
                $response['status']         = 0;
                responder($response, $mysqli);
            }
        }
        $row_plan       = $res_plan->fetch_assoc();
        $idPlan         = $row_plan['id'];
        $nombre         = $_POST['nombre'];
        $descripcion    = $_POST['descripcion'];
        $precio         = $_POST['precio'];
        if (!$nombre = validarFormulario('s',$nombre,0))
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
        $mysqli->autocommit(FALSE);
        $sql = "UPDATE cat_planes SET nombre = ?, descripcion = ?, precio = ? WHERE id = ? LIMIT 1";
        if ($prepare_update_plan = $mysqli->prepare($sql))
        {
            if (!$prepare_update_plan->bind_param('ssdi',$nombre, $descripcion, $precio, $idPlan))
            {
                $response['respuesta'] = "Error al vincular parámetros del plan. No se pudo actualizar la información. Inténtalo nuevamente";
                $response['status'] = 0;
                $mysqli->rollback();
                responder($response, $mysqli);
            }
            if (!$prepare_update_plan->execute())
            {
                $response['respuesta'] = "Error al ejecutar los parámetros del plan. No se pudo actualizar la información. Inténtalo nuevamente";
                $response['status'] = 0;
                $mysqli->rollback();
                responder($response, $mysqli);
            }
        }
        else
        {
            $response['respuesta'] = "Error al preparar los parámetros del plan. No se pudo actualizar la información. Inténtalo nuevamente";
            $response['status'] = 0;
            $mysqli->rollback();
            responder($response, $mysqli);
        }
        $arrayProductos             = json_decode($_POST['arrayProductos']);
        if (sizeof($arrayProductos) == 0)
        {
            $response['status']     = 0;
            $response['respuesta']  = "La lista de productos y servicios no puede estar vacía. Agrega al menos un producto o servicio para poder guardar la compra";
            responder($response, $mysqli);
        }

            // Cancelar registros antíguos de detalle del plan
        $sql = "UPDATE detalle_cat_planes SET activo = 0 WHERE idPlan = ? AND activo = 1";
        if($prepare_cancelar = $mysqli->prepare($sql))
        {
            if (!$prepare_cancelar->bind_param('i',$idPlan))
            {
                $response['respuesta'] = "Error al cancelar entradas antíguas del detalle del plan. No se pudo actualizar la información. Falló en la vinculación de los datos. Inténtalo nuevamente";
                $response['status'] = 0;
                $mysqli->rollback();
                responder($response, $mysqli);
            }
            if (!$prepare_cancelar->execute())
            {
                $response['respuesta'] = "Error al cancelar entradas antíguas del detalle del plan. No se pudo actualizar la información. Falló en la ejecución de los parámetros. Inténtalo nuevamente";
                $response['status'] = 0;
                $mysqli->rollback();
                responder($response, $mysqli);
            }
        }
        else
        {
            $response['respuesta'] = "Error al cancelar entradas antíguas del detalle del plan. No se pudo actualizar la información. Falló en la preparación de los parámetros. Inténtalo nuevamente";
            $response['status'] = 0;
            $mysqli->rollback();
            responder($response, $mysqli);
        }
        foreach ($arrayProductos as $esteProducto)
        {
            $nombreProducto         =   $esteProducto    ->nombre;
            $servicio               =   ($esteProducto    ->servicio == 1) ? 1 : 0;
            if (!$idProducto = validarFormulario('i',$esteProducto->id, 0))
            {
                $response['respuesta'] = "El formato del id <b>$idProducto->$nombreProducto</b> no es el correcto. Ocurrió un rollback. <br>No se guardó nada. <br>Por favor vuelve a intentarlo";
                $response['status'] = 0;
                $mysqli->rollback();
                responder($response, $mysqli);
                break;
            }
            if (!$cantidadProducto = validarFormulario('i',$esteProducto->cantidad, 0))
            {
                $response['respuesta'] = "El formato del parámetro 'cantidad' del producto: <b>$idProducto->$nombreProducto</b> no es el correcto y no puede ser menor o igual que cero (0). Ocurrió un rollback. <br>No se guardó nada. <br>Por favor vuelve a intentarlo";
                $response['status'] = 0;
                $mysqli->rollback();
                responder($response, $mysqli);
                break;
            }
            if (!$precioProducto = validarFormulario('i',$esteProducto->precio, 0))
            {
                $response['respuesta'] = "El formato del parámetro 'precio' del producto: <b>$idProducto->$nombreProducto</b> no es el correcto y no puede ser menor o igual que cero (0). Ocurrió un rollback. <br>No se guardó nada. <br>Por favor vuelve a intentarlo";
                $response['status'] = 0;
                $mysqli->rollback();
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
                    $response['respuesta'] = "Error al registrar el detalle del plan. Error en el id de producto o servicio: <b>$idProducto->$nombreProducto</b>. No se pudo guardar la información. Falló la vinculación de parámetros. Inténtalo nuevamente";
                    $response['status'] = 0;
                    $mysqli->rollback();
                    responder($response, $mysqli);
                }
                if (!$prepare_det->execute())
                {
                    $response['respuesta'] = "Error en el detalle del plan. No se pudo guardar la información. Error en el id de producto: <b>$idProducto->$nombreProducto</b>. Falló el enlace a la base de datos. Inténtalo nuevamente";
                    $response['status'] = 0;
                    $mysqli->rollback();
                    responder($response, $mysqli);
                }
            }
            else
            {
                $response['respuesta'] = "Error en el detalle del plan. No se pudo guardar la información. Falló el la preparación de parámetros. Inténtalo nuevamente";
                $response['status'] = 0;
                $mysqli->rollback();
                responder($response, $mysqli);
            }
        }
            $response['array'] = $_POST['arrayProductos'];
            // $sql =  "UPDATE cat_productos SET precioCompra = ? WHERE id = ? LIMIT 1";
            // if($prepare_precio = $mysqli->prepare($sql))
            // {
            //     if (!$prepare_precio->bind_param('ii',$precioProducto, $idProducto))
            //     {
            //         $response['respuesta'] = "Error al registrar precio del producto. Error en el id de producto: <b>$idProducto</b>. No se pudo guardar la información. Falló la vinculación de parámetros. Inténtalo nuevamente";
            //         $response['status'] = 0;
            //         $mysqli->rollback();
            //         responder($response, $mysqli);
            //     }
            //     if (!$prepare_precio->execute())
            //     {
            //         $response['respuesta'] = "Error al registrar el preio del producto. No se pudo guardar la información. Error en el id de producto: <b>$idProducto</b>. Falló el enlace a la base de datos. Inténtalo nuevamente";
            //         $response['status'] = 0;
            //         $mysqli->rollback();
            //         responder($response, $mysqli);
            //     }
            // }
            // else
            // {
            //     $response['respuesta'] = "Error al registrar precio del producto. Error en el id de producto: <b>$idProducto</b>. No se pudo guardar la información. Falló la vinculación de parámetros. Inténtalo nuevamente";
            //     $response['status'] = 0;
            //     $mysqli->rollback();
            //     responder($response, $mysqli);
            // }
        //
        $imagenBinario = validarFormulario('s', $imagenBinario, FALSE);
        $insert_id                  = $idPlan;
        $insert_id_left = str_pad($insert_id, 10, "0", STR_PAD_LEFT);
        if (file_exists($_SERVER['DOCUMENT_ROOT']."/funeraria/dev/assets/images/avatars/planes/$insert_id_left.jpg"))
        {
            unlink($_SERVER['DOCUMENT_ROOT']."/funeraria/dev/assets/images/avatars/planes/$insert_id_left.jpg");
        }
        if (strlen($imagenBinario) == 0)
        {
            $sql            = "UPDATE cat_planes SET imagen = '' WHERE id = $insert_id LIMIT 1";
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
            $sql            = "UPDATE cat_planes SET imagen = '$insert_id_left' WHERE id = $insert_id LIMIT 1";
            if ($mysqli     ->query($sql))
            {
                $imagen_d   = base64_decode($imagenBinario); // decode an image
                $im         = imagecreatefromstring($imagen_d); // php function to create image from string
                // condition check if valid conversion
                if ($im     !== false)
                {

                    $resp   = imagejpeg($im, $_SERVER['DOCUMENT_ROOT']."/funeraria/dev/assets/images/avatars/planes/$insert_id_left.jpg");
                    imagedestroy($im);
                }
            }
        }
		// Agregar evento en la bitácora de eventos ///////
		$idUsuario 				= $sesion->get("id");
		$ipUsuario 				= $sesion->get("ip");
		$idTicket               = $idPlan;
		$pantalla				= "Agregar/Modificar plan";
		$descripcion			= "Se modificó un plan funerario ($nombre) con id=$idTicket. Precio al público=$$precio";
		$sql					= "CALL agregarEvento($idUsuario, '$ipUsuario', '$pantalla', '$descripcion', $idSucursal);";
		$mysqli					->query($sql);
		//////////////////////////////////////////////////
        if($mysqli->commit())
        {
            $response['status']     = 1;
            $response['mensaje']    = $nombre;
            $response['respuesta']  = "Este plan funerario se ha ctualizado correctamente.
                                        </br>No. de plan: <strong>$idTicket</strong> </br> <strong><a href='listarPlanes.php' class='orange'>Lista de planes</a></strong>";
            responder($response, $mysqli);
        }
        else
        {
            $response['status']     = 0;
            $response['respuesta']  = "Ocurrió un error. No se pudo guardar. Error en commit. Vuelve a intentarlo";
            $mysqli->rollback();
            responder($response, $mysqli);
        }
    }
?>

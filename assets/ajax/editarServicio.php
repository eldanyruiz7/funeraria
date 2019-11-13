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
        $permiso = $usuario->permiso("modificarServicio",$mysqli);
        if (!$permiso)
        {
            $response['mensaje'] = "No se pudo modificar este registro. Usuario con permisos insuficientes para realizar esta acción";
            $response['status'] = 0;
            responder($response, $mysqli);
        }
        $id                             = $_POST['inputIdEdit'];
        $nombre                         = $_POST['inputNombreEdit'];
        $descripcion                    = $_POST['inputDescripcionEdit'];
        $precio                         = $_POST['inputPrecioEdit'];

		$idUsuario      				= $sesion->get('id');
        $sql            				= "SELECT idSucursal FROM cat_usuarios WHERE id = $idUsuario LIMIT 1";
        $res_noSucursal 				= $mysqli->query($sql);
        $row_noSucursal 				= $res_noSucursal->fetch_assoc();
        $idSucursal     				= $row_noSucursal['idSucursal'];
        $response = array(
            "status"                    => 1
        );
        if(!$id = validarFormulario('i', $id))
        {
            $response['mensaje']        = "El ID del cliente debe ser numérico";
            $response['status']         = 0;
            $response['focus']          = '';
            responder($response, $mysqli);
        }
        if (!$nombre                    = validarFormulario('s',$nombre, FALSE))
        {
            $response['mensaje']        = "El campo Nombre no puede estar en blanco";
            $response['status']         = 0;
            $response['focus']          = 'inputNombreEdit';
            responder($response, $mysqli);
        }
        $descripcion                    = validarFormulario('s', $descripcion, FALSE);
        if(!$precio = validarFormulario('i', $precio, 0))
        {
            $response['mensaje']        = "El precio debe ser mayor que cero (0)";
            $response['status']         = 0;
            $response['focus']          = 'inputPrecioEdit';
            responder($response, $mysqli);
        }
        $sql = "UPDATE cat_servicios
                SET nombre              = ?,
                    descripcion         = ?,
                    precio              = ?
                WHERE id                = ?
                LIMIT 1";
        if($prepare                     = $mysqli->prepare($sql))
        {
            if(!$prepare->bind_param('ssii', $nombre, $descripcion, $precio, $id))
            {
                $response['mensaje']    = "Error. No se pudo modificar la información. Falló el la vinculación de parámetros. Inténtalo nuevamente";
                $response['status']     = 0;
                responder($response, $mysqli);
            }
            if(!$prepare->execute())
            {
                $response['mensaje']    = "Error. No se pudo modificar la información. Falló el enlace a la base de datos. Inténtalo nuevamente";
                $response['status']     = 0;
                responder($response, $mysqli);
            }
            if($prepare->affected_rows  == 0)
            {
                $response['mensaje']    = "No se modificó nada";
                $response['status']     = 2;
                responder($response, $mysqli);
            }
			// Agregar evento en la bitácora de eventos ///////
			$ipUsuario 					= $sesion->get("ip");
			$pantalla					= "Agregar/Modificar plan";
			$descripcion				= "Se modificó un servicio ($nombre) con id=$id. Precio al público=$$precio";
			$sql						= "CALL agregarEvento($idUsuario, '$ipUsuario', '$pantalla', '$descripcion', $idSucursal);";
			$mysqli						->query($sql);
			//////////////////////////////////////////////////
            $response['mensaje']        = "Servicio '$nombre' fue modificado exitosamente";
            $response['status']         = 1;
            responder($response, $mysqli);
        }
        else
        {
            $response['mensaje']        = "Error. No se pudo modificar. Falló la preparación de los datos. Vuelve a intentarlo nuevamente";
            $response['status']         = 0;
            responder($response, $mysqli);
        }
    }
?>
